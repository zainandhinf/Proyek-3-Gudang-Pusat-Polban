<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelompokBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kelompok_barangs')->insert([
            ['kode_kelompok' => 'KTS', 'nama_kelompok' => 'Kertas'],
            ['kode_kelompok' => 'ATK', 'nama_kelompok' => 'Alat Tulis'],
            ['kode_kelompok' => 'ELK', 'nama_kelompok' => 'Elektronik'],
            ['kode_kelompok' => 'KBS', 'nama_kelompok' => 'Alat Kebersihan'],
        ]);
    }
}
