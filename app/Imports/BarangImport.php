<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\KelompokBarang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BarangImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Mapping Index Kolom Dinamis
        $colIndex = [
            'kode_kelompok' => -1,
            'kode_barang'   => -1,
            'nama_barang'   => -1,
            'nama_kelompok' => -1,
            'satuan'        => -1,
            'stok'          => -1,
            'harga'         => -1,
        ];

        DB::transaction(function () use ($rows, &$colIndex) {
            foreach ($rows as $row) {
                // Bersihkan sel dari karakter aneh
                $row = $row->map(fn($item) => $this->cleanValue($item));
                $firstCol = strtoupper($row[0] ?? '');

                // ==========================================================
                // A. DETEKSI HEADER TABEL (MAPPING)
                // ==========================================================
                // Mencari baris yang kolom pertamanya "NO" atau "NO."
                if ($firstCol === 'NO' || $firstCol === 'NO.') {
                    foreach ($row as $idx => $val) {
                        $valUpper = strtoupper($val);
                        
                        if (Str::contains($valUpper, 'KODE KELOMPOK')) $colIndex['kode_kelompok'] = $idx;
                        if (Str::contains($valUpper, 'KODE BARANG'))   $colIndex['kode_barang'] = $idx;
                        if (Str::contains($valUpper, 'NAMA BARANG'))   $colIndex['nama_barang'] = $idx;
                        if (Str::contains($valUpper, 'KELOMPOK'))      $colIndex['nama_kelompok'] = $idx;
                        if (Str::contains($valUpper, 'SATUAN'))        $colIndex['satuan'] = $idx;
                        if (Str::contains($valUpper, 'STOK'))          $colIndex['stok'] = $idx;
                        if (Str::contains($valUpper, 'HARGA'))         $colIndex['harga'] = $idx;
                    }
                    continue; // Skip baris header ini
                }

                // ==========================================================
                // B. PROSES DATA BARANG
                // ==========================================================
                // Syarat: Kolom Kode Barang sudah ditemukan & Kolom pertama adalah Angka (Data)
                if ($colIndex['kode_barang'] !== -1 && is_numeric($firstCol)) {
                    
                    $kodeBarang = $row[$colIndex['kode_barang']] ?? null;
                    
                    if (!empty($kodeBarang)) {
                        $this->processBarang($row, $colIndex);
                    }
                }
            }
        });
    }

    private function processBarang($row, $colIndex)
    {
        // 1. Ambil Data Mentah dari Excel
        $kodeBarang   = $row[$colIndex['kode_barang']];
        $namaBarang   = $row[$colIndex['nama_barang']];
        $stokExcel    = (int) $row[$colIndex['stok']]; // Stok dari Excel
        $harga        = (int) str_replace(['.', ',', 'Rp', ' '], '', $row[$colIndex['harga']] ?? '0');
        
        $namaSatuan   = $row[$colIndex['satuan']] ?? 'Pcs';
        $kodeKelompok = $row[$colIndex['kode_kelompok']] ?? 'UMUM';
        $namaKelompok = $row[$colIndex['nama_kelompok']] ?? 'Umum';

        // 2. Siapkan Master Data (Satuan & Kelompok)
        $satuan = Satuan::firstOrCreate(['nama_satuan' => $namaSatuan]);
        $kelompok = KelompokBarang::firstOrCreate(
            ['kode_kelompok' => $kodeKelompok],
            ['nama_kelompok' => $namaKelompok]
        );

        // 3. LOGIKA UTAMA: CEK KETERSEDIAAN BARANG
        $barang = Barang::where('kode_barang', $kodeBarang)->first();

        if ($barang) {
            // ==============================================================
            // KASUS A: BARANG SUDAH ADA -> AKUMULASI STOK
            // ==============================================================
            // Stok Baru = Stok Lama di DB + Stok dari Excel
            // Harga akan diupdate mengikuti harga terbaru dari Excel
            
            $stokBaru = $barang->stok_saat_ini + $stokExcel;

            $barang->update([
                'stok_saat_ini' => $stokBaru,
                'harga'         => $harga, // Update harga ke yang terbaru
            ]);

        } else {
            // ==============================================================
            // KASUS B: BARANG BELUM ADA -> BUAT BARU
            // ==============================================================
            Barang::create([
                'kode_barang'        => $kodeBarang,
                'nama_barang'        => $namaBarang,
                'stok_saat_ini'      => $stokExcel, // Stok awal sesuai Excel
                'harga'              => $harga,
                'satuan_id'          => $satuan->id,
                'kelompok_barang_id' => $kelompok->id,
                'deskripsi'          => 'Import Excel',
            ]);
        }
    }

    private function cleanValue($value)
    {
        if (is_null($value)) return '';
        // Hapus karakter non-breaking space & trim
        $v = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', (string)$value);
        return trim($v);
    }
}