<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID Kategori & Satuan
        $atk = Kategori::where('nama_kategori', 'ATK')->first()->id;
        $tinta = Kategori::where('nama_kategori', 'Tinta Printer')->first()->id;
        $kebersihan = Kategori::where('nama_kategori', 'Alat Kebersihan')->first()->id;

        $rim = Satuan::where('nama_satuan', 'Rim')->first()->id;
        $box = Satuan::where('nama_satuan', 'Box')->first()->id;
        $botol = Satuan::where('nama_satuan', 'Botol')->first()->id;
        $pcs = Satuan::where('nama_satuan', 'Pcs')->first()->id;

        // Buat data barang
        Barang::firstOrCreate(
            ['kode_barang' => 'ATK-KRT-A4-80'],
            [
                'nama_barang' => 'Kertas A4 80gr',
                'stok_saat_ini' => 100,
                'kategori_id' => $atk,
                'satuan_id' => $rim,
            ]
        );

        Barang::firstOrCreate(
            ['kode_barang' => 'ATK-SPD-HTM'],
            [
                'nama_barang' => 'Spidol Boardmarker Hitam',
                'stok_saat_ini' => 50,
                'kategori_id' => $atk,
                'satuan_id' => $box,
            ]
        );

        Barang::firstOrCreate(
            ['kode_barang' => 'TIN-EPS-003'],
            [
                'nama_barang' => 'Tinta Epson 003 Black',
                'stok_saat_ini' => 25,
                'kategori_id' => $tinta,
                'satuan_id' => $botol,
            ]
        );
        
        Barang::firstOrCreate(
            ['kode_barang' => 'KBR-SAP-LNT'],
            [
                'nama_barang' => 'Sapu Lantai Ijuk',
                'stok_saat_ini' => 30,
                'kategori_id' => $kebersihan,
                'satuan_id' => $pcs,
            ]
        );
    }
}
