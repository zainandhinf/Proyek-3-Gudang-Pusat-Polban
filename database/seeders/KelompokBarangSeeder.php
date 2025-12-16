<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelompokBarangSeeder extends Seeder
{
    public function run(): void
    {
        // Reset table jika perlu (opsional, hati-hati jika production)
        // DB::table('kelompok_barangs')->truncate();

        $data = [
            ['kode_kelompok' => '1.01.03.01.001', 'nama_kelompok' => 'Alat Tulis'],
            ['kode_kelompok' => '1.01.03.01.002', 'nama_kelompok' => 'Tinta Tulis, Tinta Stempel'],
            ['kode_kelompok' => '1.01.03.01.003', 'nama_kelompok' => 'Penjepit Kertas'],
            ['kode_kelompok' => '1.01.03.01.004', 'nama_kelompok' => 'Penghapus/Korektor'],
            ['kode_kelompok' => '1.01.03.01.005', 'nama_kelompok' => 'Buku Tulis'],
            ['kode_kelompok' => '1.01.03.01.006', 'nama_kelompok' => 'Ordner Dan Map'],
            ['kode_kelompok' => '1.01.03.01.007', 'nama_kelompok' => 'Penggaris'],
            ['kode_kelompok' => '1.01.03.01.008', 'nama_kelompok' => 'Cutter (Alat Tulis Kantor)'],
            ['kode_kelompok' => '1.01.03.01.009', 'nama_kelompok' => 'Pita Mesin Ketik'],
            ['kode_kelompok' => '1.01.03.01.010', 'nama_kelompok' => 'Alat Perekat'],
            ['kode_kelompok' => '1.01.03.01.011', 'nama_kelompok' => 'Stadler HD'],
            ['kode_kelompok' => '1.01.03.01.012', 'nama_kelompok' => 'Staples'],
            ['kode_kelompok' => '1.01.03.01.013', 'nama_kelompok' => 'Isi Staples'],
            ['kode_kelompok' => '1.01.03.01.014', 'nama_kelompok' => 'Barang Cetakan'],
            ['kode_kelompok' => '1.01.03.01.015', 'nama_kelompok' => 'Seminar Kit'],
            ['kode_kelompok' => '1.01.03.01.999', 'nama_kelompok' => 'Alat Tulis Kantor Lainnya'],
            ['kode_kelompok' => '1.01.03.02.001', 'nama_kelompok' => 'Kertas HVS'],
            ['kode_kelompok' => '1.01.03.02.002', 'nama_kelompok' => 'Berbagai Kertas'],
            ['kode_kelompok' => '1.01.03.02.003', 'nama_kelompok' => 'Kertas Cover'],
            ['kode_kelompok' => '1.01.03.02.004', 'nama_kelompok' => 'Amplop'],
            ['kode_kelompok' => '1.01.03.02.005', 'nama_kelompok' => 'Kop Surat'],
            ['kode_kelompok' => '1.01.03.02.999', 'nama_kelompok' => 'Kertas Dan Cover Lainnya'],
            ['kode_kelompok' => '1.01.03.05.001', 'nama_kelompok' => 'Sapu Dan Sikat'],
            ['kode_kelompok' => '1.01.03.05.002', 'nama_kelompok' => 'Alat-Alat Pel Dan Lap'],
            ['kode_kelompok' => '1.01.03.05.003', 'nama_kelompok' => 'Ember, Slang, Dan Tempat Air Lainnya'],
            ['kode_kelompok' => '1.01.03.05.004', 'nama_kelompok' => 'Keset Dan Tempat Sampah'],
            ['kode_kelompok' => '1.01.03.05.005', 'nama_kelompok' => 'Kunci, Kran Dan Semprotan'],
            ['kode_kelompok' => '1.01.03.05.006', 'nama_kelompok' => 'Alat Pengikat'],
            ['kode_kelompok' => '1.01.03.05.007', 'nama_kelompok' => 'Peralatan Ledeng'],
            ['kode_kelompok' => '1.01.03.05.008', 'nama_kelompok' => 'Bahan Kimia Untuk Pembersih'],
            ['kode_kelompok' => '1.01.03.05.009', 'nama_kelompok' => 'Alat Untuk Makan Dan Minum'],
            ['kode_kelompok' => '1.01.03.05.010', 'nama_kelompok' => 'Kaos Lampu Petromak'],
            ['kode_kelompok' => '1.01.03.05.011', 'nama_kelompok' => 'Kaca Lampu Petromak'],
            ['kode_kelompok' => '1.01.03.05.012', 'nama_kelompok' => 'Pengharum Ruangan'],
            ['kode_kelompok' => '1.01.03.05.013', 'nama_kelompok' => 'Kuas'],
            ['kode_kelompok' => '1.01.03.05.014', 'nama_kelompok' => 'Segel/Tanda Pengaman'],
            ['kode_kelompok' => '1.01.03.05.999', 'nama_kelompok' => 'Perabot Kantor Lainnya'],
        ];

        foreach ($data as $item) {
            DB::table('kelompok_barangs')->updateOrInsert(
                ['kode_kelompok' => $item['kode_kelompok']],
                ['nama_kelompok' => $item['nama_kelompok'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}