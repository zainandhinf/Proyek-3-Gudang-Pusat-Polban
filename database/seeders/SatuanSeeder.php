<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('satuans')->insert([
            ['nama_satuan' => 'Rim', 'keterangan' => 'Satuan untuk kertas', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Box', 'keterangan' => 'Satuan untuk kotak', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Pcs', 'keterangan' => 'Satuan untuk bijian', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Botol', 'keterangan' => 'Satuan untuk cairan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Unit', 'keterangan' => 'Satuan untuk barang tunggal', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
