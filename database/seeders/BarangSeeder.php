<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\KelompokBarang;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil & Satuan

        $rim = Satuan::where('nama_satuan', 'Rim')->first()->id;
        $box = Satuan::where('nama_satuan', 'Box')->first()->id;
        $botol = Satuan::where('nama_satuan', 'Botol')->first()->id;
        $pcs = Satuan::where('nama_satuan', 'Pcs')->first()->id;

        // 2. Ambil ID Kelompok Barang (Pastikan data ini sudah ada di KelompokBarangSeeder)
        $kel_kertas = KelompokBarang::where('kode_kelompok', 'KTS')->first()->id;
        $kel_atk = KelompokBarang::where('kode_kelompok', 'ATK')->first()->id;
        $kel_elektronik = KelompokBarang::where('kode_kelompok', 'ELK')->first()->id; 
        // Jika belum ada 'KBS', gunakan salah satu yang ada atau tambahkan di KelompokBarangSeeder
        $kel_lain = KelompokBarang::where('kode_kelompok', 'ATK')->first()->id; 

        // 3. Buat Data Barang (Wajib sertakan 'kelompok_barang_id')
        Barang::firstOrCreate(
            ['kode_barang' => 'KTS.001'], // Sesuaikan format baru (Kelompok.NoUrut)
            [
                'nama_barang' => 'Kertas A4 80gr',
                'stok_saat_ini' => 100,
                'harga' => 55000,
                'satuan_id' => $rim,
                'kelompok_barang_id' => $kel_kertas,
                'deskripsi' => 'rim',
            ]
        );

        Barang::firstOrCreate(
            ['kode_barang' => 'ATK.001'],
            [
                'nama_barang' => 'Spidol Boardmarker Hitam',
                'stok_saat_ini' => 50,
                'harga' => 44000,
                'satuan_id' => $box,
                'kelompok_barang_id' => $kel_atk,
                'deskripsi' => 'box',
            ]
        );

        Barang::firstOrCreate(
            ['kode_barang' => 'ELK.001'],
            [
                'nama_barang' => 'Tinta Epson 003 Black',
                'stok_saat_ini' => 25,
                'harga' => 33000,
                'satuan_id' => $botol,
                'kelompok_barang_id' => $kel_elektronik, 
                'deskripsi' => 'botol',
            ]
        );
        
        Barang::firstOrCreate(
            ['kode_barang' => 'ATK.002'],
            [
                'nama_barang' => 'Sapu Lantai Ijuk',
                'stok_saat_ini' => 30,
                'harga' => 22000,
                'satuan_id' => $pcs,
                'kelompok_barang_id' => $kel_lain,
                'deskripsi' => 'pcs',
            ]
        );
    }
}
