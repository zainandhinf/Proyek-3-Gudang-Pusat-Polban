<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        $satuans = [
            'Rim', 'Box', 'Pcs', 'Botol', 'Unit', 
            'Buah', 'Pack', 'Lembar', 'Set', 'Rol', 
            'Kaleng', 'Galon', 'Batang', 'Meter', 'Dus'
        ];

        foreach ($satuans as $satuan) {
            DB::table('satuans')->updateOrInsert(
                ['nama_satuan' => $satuan],
                ['keterangan' => 'Satuan ' . $satuan, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}