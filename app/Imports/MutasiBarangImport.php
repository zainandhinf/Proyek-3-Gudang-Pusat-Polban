<?php

namespace App\Imports;

use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use App\Models\Barang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MutasiBarangImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // 1. STATE PENAMPUNG DATA (Mencegah reset saat ganti baris)
        $headerData = [
            'jenis' => null,
            'tanggal' => null,
            'no_bukti' => null,
            'no_dokumen' => null,
            'keterangan' => '-',
        ];

        // 2. DEFAULT MAPPING (Akan di-overwrite jika header tabel ditemukan)
        // Kita set default berdasarkan file export terakhir Anda:
        // A=No, B=Kode Kelompok, C=Kode Barang, D=Nama, E=Kelompok, F=Satuan, G=Stok, H=Harga
        // TAPI file import Anda sepertinya: A=No, B=Kode Barang, C=Nama...
        // Jadi kita buat dynamic.
        $colIndex = [
            'kode_barang' => -1, // -1 artinya belum ketemu
            'nama_barang' => -1,
            'jumlah'      => -1, 
            'catatan'     => -1,
        ];

        DB::transaction(function () use ($rows, &$headerData, &$colIndex) {
            foreach ($rows as $row) {
                // Bersihkan setiap sel dari spasi misterius
                $row = $row->map(fn($item) => $this->cleanValue($item));
                $firstCol = strtoupper($row[0] ?? '');

                // ==========================================================
                // A. DETEKSI INFO HEADER (KOP SURAT)
                // ==========================================================
                if (Str::contains($firstCol, 'BUKTI MASUK')) $headerData['jenis'] = 'masuk';
                if (Str::contains($firstCol, 'BUKTI KELUAR')) $headerData['jenis'] = 'keluar';

                // Cari Tanggal di kolom manapun di baris ini
                if (Str::contains($firstCol, 'TGL')) {
                    foreach ($row as $cell) {
                        if (Str::contains(strtoupper($cell), 'DECEMBER') || preg_match('/\d{2}[\/\-\s]\d{2}[\/\-\s]\d{4}/', $cell)) {
                             $headerData['tanggal'] = $this->parseDate($cell);
                             break;
                        }
                    }
                }

                if (Str::contains($firstCol, 'NO. BUKTI')) {
                    $headerData['no_bukti'] = $this->cleanValue($row[2] ?? $row[1] ?? '');
                }
                
                if (Str::contains($firstCol, 'NO. DOKUMEN')) {
                    $headerData['no_dokumen'] = $this->cleanValue($row[2] ?? $row[1] ?? '');
                }

                // ==========================================================
                // B. DETEKSI POSISI KOLOM (MAPPING DINAMIS)
                // ==========================================================
                // Kita cek seluruh baris, jika ada kata "KODE BARANG" atau "JUMLAH"
                // kita kunci index kolomnya.
                $isHeaderRow = false;
                foreach ($row as $idx => $val) {
                    $valUpper = strtoupper($val);
                    if (Str::contains($valUpper, 'KODE BARANG') || $valUpper === 'KODE') {
                        $colIndex['kode_barang'] = $idx;
                        $isHeaderRow = true;
                    }
                    if ($valUpper === 'NAMA BARANG') {
                        $colIndex['nama_barang'] = $idx;
                    }
                    if ($valUpper === 'JUMLAH' || $valUpper === 'QTY') {
                        $colIndex['jumlah'] = $idx;
                    }
                    if (Str::contains($valUpper, 'KETERANGAN') || Str::contains($valUpper, 'CATATAN')) {
                        $colIndex['catatan'] = $idx;
                    }
                }

                if ($isHeaderRow) continue; // Skip baris judul tabel

                // ==========================================================
                // C. PROSES DATA BARANG
                // ==========================================================
                // Syarat: Header No Bukti sudah dapat, dan Kolom Kode Barang sudah teridentifikasi
                if (!empty($headerData['no_bukti']) && $colIndex['kode_barang'] !== -1) {
                    
                    $kodeBarang = $row[$colIndex['kode_barang']] ?? '';
                    $namaBarang = $row[$colIndex['nama_barang']] ?? ''; // Cadangan jika kode tidak ketemu
                    $jumlah     = (int) ($row[$colIndex['jumlah']] ?? 0);
                    $catatan    = $row[$colIndex['catatan']] ?? null;

                    // Validasi minimal: Kode atau Nama harus ada, dan Jumlah > 0
                    if ((!empty($kodeBarang) || !empty($namaBarang)) && $jumlah > 0) {
                        $this->saveDetail($headerData, $kodeBarang, $namaBarang, $jumlah, $catatan);
                    }
                }
            }
        });
    }

    private function saveDetail($header, $kode, $nama, $jumlah, $catatan)
    {
        // 1. Buat/Ambil Header Mutasi
        $mutasi = MutasiBarang::firstOrCreate(
            ['no_bukti' => $header['no_bukti']], 
            [
                'jenis_mutasi' => $header['jenis'] ?? 'keluar', // Default
                'tanggal_mutasi' => $header['tanggal'] ?? now(),
                'no_dokumen' => $header['no_dokumen'],
                'keterangan' => 'Import Excel Auto',
                'dicatat_oleh_user_id' => Auth::id() ?? 1,
            ]
        );

        // 2. Cari Barang (Logika Ganda: Kode -> Nama)
        $barang = null;
        if (!empty($kode)) {
            $barang = Barang::where('kode_barang', $kode)->first();
        }
        
        // Jika tidak ketemu by Kode, coba by Nama (persis)
        if (!$barang && !empty($nama)) {
            $barang = Barang::where('nama_barang', 'LIKE', $nama)->first();
        }

        // 3. Simpan Detail Jika Barang Ditemukan
        if ($barang) {
            // Cek duplikasi agar tidak double input jika file diupload 2x
            $exists = DetailMutasiBarang::where('mutasi_barang_id', $mutasi->id)
                        ->where('barang_id', $barang->id)
                        ->exists();

            if (!$exists) {
                DetailMutasiBarang::create([
                    'mutasi_barang_id' => $mutasi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'catatan' => $catatan
                ]);

                // Update Stok Master
                if (($header['jenis'] ?? 'keluar') === 'masuk') {
                    $barang->increment('stok_saat_ini', $jumlah);
                } else {
                    $barang->decrement('stok_saat_ini', $jumlah);
                }
            }
        }
    }

    /**
     * Membersihkan string dari karakter aneh
     */
    private function cleanValue($value)
    {
        if (is_null($value)) return '';
        // Hapus karakter non-breaking space & trim
        $v = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', (string)$value);
        return trim($v);
    }

    private function parseDate($value)
    {
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}