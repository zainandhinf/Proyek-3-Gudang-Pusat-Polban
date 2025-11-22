<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
            ['kode_kategori' => 'ATK', 'nama_kategori' => 'ATK', 'deskripsi' => 'Alat Tulis Kantor', 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'KBR', 'nama_kategori' => 'Alat Kebersihan', 'deskripsi' => 'Perlengkapan untuk kebersihan', 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'TIN', 'nama_kategori' => 'Tinta Printer', 'deskripsi' => 'Tinta untuk printer', 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'LAIN', 'nama_kategori' => 'Lain-lain', 'deskripsi' => 'Barang lain-lain', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
