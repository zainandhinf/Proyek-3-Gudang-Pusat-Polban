<?php

namespace App\Imports;

use App\Models\StockOpname;
use App\Models\DetailStockOpname;
use App\Models\Barang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StockOpnameImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // 1. STATE HEADER (Menampung info konteks saat ini)
        $headerData = [
            'no_opname' => null,
            'tanggal'   => null,
            'pencatat'  => null,
        ];

        // 2. MAPPING KOLOM DINAMIS (Akan diisi otomatis saat ketemu judul tabel)
        $colIndex = [
            'nama_barang' => -1,
            'stok_sistem' => -1,
            'stok_fisik'  => -1,
            'keterangan'  => -1,
        ];

        DB::transaction(function () use ($rows, &$headerData, &$colIndex) {
            foreach ($rows as $row) {
                // Bersihkan setiap sel
                $row = $row->map(fn($item) => $this->cleanValue($item));
                $firstCol = strtoupper($row[0] ?? '');

                // ==========================================================
                // A. DETEKSI INFO HEADER
                // ==========================================================
                
                // Cari No Opname (Biasanya di kolom A atau B)
                // Contoh di CSV: "Nomor Opname : SO-2025..."
                if (Str::contains($firstCol, 'NOMOR OPNAME')) {
                    // Coba ambil dari kolom C (index 2) atau B (index 1)
                    $val = $this->cleanValue($row[2] ?? $row[1] ?? '');
                    if (!empty($val)) {
                        $headerData['no_opname'] = $val;
                    }

                    // Cari Tanggal di baris yang sama (biasanya di kolom kanan)
                    foreach ($row as $cell) {
                        if (Str::contains(strtoupper($cell), 'TANGGAL')) {
                            $rawDate = str_ireplace(['TANGGAL', ':'], '', $cell);
                            $headerData['tanggal'] = $this->parseDate(trim($rawDate));
                        }
                    }
                }

                // Cari Pencatat
                if (Str::contains($firstCol, 'PENCATAT')) {
                    $headerData['pencatat'] = $this->cleanValue($row[2] ?? $row[1] ?? '');
                }

                // ==========================================================
                // B. DETEKSI POSISI KOLOM (MAPPING)
                // ==========================================================
                // Mencari baris judul tabel: "NO.", "NAMA BARANG", dll
                if ($firstCol === 'NO.' || $firstCol === 'NO') {
                    foreach ($row as $idx => $val) {
                        $valUpper = strtoupper($val);
                        
                        if ($valUpper === 'NAMA BARANG') $colIndex['nama_barang'] = $idx;
                        if ($valUpper === 'STOK SISTEM' || $valUpper === 'SISTEM') $colIndex['stok_sistem'] = $idx;
                        if ($valUpper === 'STOK FISIK' || $valUpper === 'FISIK') $colIndex['stok_fisik'] = $idx;
                        if ($valUpper === 'KETERANGAN' || $valUpper === 'KET') $colIndex['keterangan'] = $idx;
                    }
                    continue; // Skip baris judul ini
                }

                // ==========================================================
                // C. PROSES DATA BARANG
                // ==========================================================
                // Syarat: Header No Opname sudah dapat, Kolom Nama Barang sudah teridentifikasi
                // Dan Kolom pertama adalah Angka (No Urut)
                if (!empty($headerData['no_opname']) && $colIndex['nama_barang'] !== -1 && is_numeric($firstCol)) {
                    
                    $namaBarang = $row[$colIndex['nama_barang']] ?? '';
                    $stokSistem = (int) ($row[$colIndex['stok_sistem']] ?? 0);
                    
                    // Ambil Stok Fisik
                    // Jika di excel "-", anggap 0. Jika kosong, anggap 0.
                    $rawFisik = $row[$colIndex['stok_fisik']] ?? 0;
                    $stokFisik = ($rawFisik === '-' || $rawFisik === '') ? 0 : (int)$rawFisik;
                    
                    $keterangan = $row[$colIndex['keterangan']] ?? null;

                    if (!empty($namaBarang)) {
                        $this->saveDetail($headerData, $namaBarang, $stokSistem, $stokFisik, $keterangan);
                    }
                }
            }
        });
    }

    private function saveDetail($header, $namaBarang, $sistem, $fisik, $ket)
    {
        // 1. Buat/Ambil Header SO
        $opname = StockOpname::firstOrCreate(
            ['no_opname' => $header['no_opname']],
            [
                'tanggal_opname' => $header['tanggal'] ?? now(),
                'dicatat_oleh_user_id' => Auth::id() ?? 1,
                'status' => 'Selesai', // Import otomatis dianggap selesai
            ]
        );

        // 2. Cari Barang (Berdasarkan Nama, karena file laporan tidak punya kode)
        // Pastikan Nama Barang di Excel SAMA PERSIS dengan di Database
        $barang = Barang::where('nama_barang', 'LIKE', trim($namaBarang))->first();

        if ($barang) {
            $selisih = $fisik - $sistem;

            // 3. Simpan/Update Detail
            DetailStockOpname::updateOrCreate(
                [
                    'stock_opname_id' => $opname->id,
                    'barang_id' => $barang->id
                ],
                [
                    'stok_sistem' => $sistem,
                    'stok_fisik' => $fisik,
                    'selisih' => $selisih,
                    'keterangan' => $ket
                ]
            );

            // 4. UPDATE MASTER STOK BARANG (PENTING!)
            // Karena ini Stock Opname, stok fisik adalah kebenaran (truth).
            // Kita update stok di tabel 'barangs' agar sesuai fisik.
            $barang->update(['stok_saat_ini' => $fisik]);
        }
    }

    private function cleanValue($value)
    {
        if (is_null($value)) return '';
        // Hapus karakter aneh (non-breaking space) & trim
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