<?php

namespace App\Imports;

use App\Models\BarangUsang;
use App\Models\DetailBarangUsang;
use App\Models\Barang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BarangUsangImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // 1. STATE HEADER
        $headerData = [
            'no_catat' => null,
            'tanggal'  => null,
            'no_bukti' => null,
            'keterangan' => '-',
        ];

        // 2. MAPPING KOLOM DINAMIS
        $colIndex = [
            'nama_barang' => -1,
            'jumlah'      => -1,
            'keterangan'  => -1, // Detail Kerusakan
        ];

        DB::transaction(function () use ($rows, &$headerData, &$colIndex) {
            foreach ($rows as $row) {
                // Bersihkan setiap sel
                $row = $row->map(fn($item) => $this->cleanValue($item));
                $firstCol = strtoupper($row[0] ?? '');

                // ==========================================================
                // A. DETEKSI INFO HEADER
                // ==========================================================
                
                // Cari No Dokumen (System ID)
                // Di file export: "NO. DOKUMEN: BR-xxxx" (biasanya di kanan) atau "Nomor Dokumen : BR-xxx"
                if (Str::contains($firstCol, 'NOMOR DOKUMEN')) {
                    $headerData['no_catat'] = $this->cleanValue($row[2] ?? $row[1] ?? '');
                    
                    // Cari Tanggal di baris yang sama
                    foreach ($row as $cell) {
                        if (Str::contains(strtoupper($cell), 'TANGGAL')) {
                            $rawDate = str_ireplace(['TANGGAL', ':'], '', $cell);
                            $headerData['tanggal'] = $this->parseDate(trim($rawDate));
                        }
                    }
                }
                
                // Fallback pencarian No Dokumen di Header Atas (Kop)
                if (Str::contains($firstCol, 'NO. DOKUMEN')) {
                     // Biasanya di file excel laporannya ada di kolom paling kanan (index besar) atau digabung
                     // Kita cari value di row ini yang formatnya "BR-..."
                     foreach($row as $cell) {
                         if (Str::contains($cell, 'BR-')) {
                             $headerData['no_catat'] = $this->cleanValue(str_replace('NO. DOKUMEN', '', $cell));
                         }
                     }
                }

                // Cari Nomor Bukti Manual
                if (Str::contains($firstCol, 'NOMOR BUKTI MANUAL') || Str::contains($firstCol, 'NO. BUKTI')) {
                    $headerData['no_bukti'] = $this->cleanValue($row[2] ?? $row[1] ?? '');
                }

                // Cari Keterangan
                if (Str::contains($firstCol, 'KETERANGAN')) {
                    $headerData['keterangan'] = $this->cleanValue($row[2] ?? $row[1] ?? '-');
                }

                // ==========================================================
                // B. DETEKSI POSISI KOLOM
                // ==========================================================
                // Mencari baris judul tabel: "NO.", "NAMA BARANG", "JUMLAH", "DETAIL KERUSAKAN"
                if ($firstCol === 'NO.' || $firstCol === 'NO') {
                    foreach ($row as $idx => $val) {
                        $valUpper = strtoupper($val);
                        
                        if ($valUpper === 'NAMA BARANG') $colIndex['nama_barang'] = $idx;
                        if ($valUpper === 'JUMLAH') $colIndex['jumlah'] = $idx;
                        if (Str::contains($valUpper, 'KERUSAKAN') || $valUpper === 'KETERANGAN') $colIndex['keterangan'] = $idx;
                    }
                    continue; 
                }

                // ==========================================================
                // C. PROSES DATA BARANG
                // ==========================================================
                if (!empty($headerData['no_catat']) && $colIndex['nama_barang'] !== -1 && is_numeric($firstCol)) {
                    
                    $namaBarang = $row[$colIndex['nama_barang']] ?? '';
                    $jumlah     = (int) ($row[$colIndex['jumlah']] ?? 0);
                    $kerusakan  = $row[$colIndex['keterangan']] ?? null;

                    if (!empty($namaBarang) && $jumlah > 0) {
                        $this->saveDetail($headerData, $namaBarang, $jumlah, $kerusakan);
                    }
                }
            }
        });
    }

    private function saveDetail($header, $namaBarang, $jumlah, $kerusakan)
    {
        // 1. Buat/Ambil Header Barang Usang
        $laporan = BarangUsang::firstOrCreate(
            ['no_catat' => $header['no_catat']],
            [
                'tanggal_catat' => $header['tanggal'] ?? now(),
                'no_bukti'      => $header['no_bukti'],
                'keterangan'    => $header['keterangan'],
                'dicatat_oleh_user_id' => Auth::id() ?? 1,
            ]
        );

        // 2. Cari Barang (By Nama)
        $barang = Barang::where('nama_barang', 'LIKE', trim($namaBarang))->first();

        if ($barang) {
            if ($barang->stok_saat_ini < $jumlah) {
                throw new \Exception("Gagal Import: Stok {$barang->nama_barang} tidak cukup untuk dicatat rusak sejumlah {$jumlah}. Stok tersedia: {$barang->stok_saat_ini}");
            }

            // 3. Cek Duplikat Detail
            $exists = DetailBarangUsang::where('barang_usang_id', $laporan->id)
                        ->where('barang_id', $barang->id)
                        ->exists();

            if (!$exists) {
                // Simpan Detail
                DetailBarangUsang::create([
                    'barang_usang_id' => $laporan->id,
                    'barang_id'       => $barang->id,
                    'jumlah'          => $jumlah,
                    'keterangan_rusak'=> $kerusakan
                ]);

                // 4. UPDATE STOK MASTER (Stok Berkurang)
                // Barang rusak = keluar dari persediaan aktif
                $barang->decrement('stok_saat_ini', $jumlah);
            }
        }
    }

    private function cleanValue($value)
    {
        if (is_null($value)) return '';
        $v = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', (string)$value);
        return trim(str_replace([':', '  '], ['', ' '], $v));
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