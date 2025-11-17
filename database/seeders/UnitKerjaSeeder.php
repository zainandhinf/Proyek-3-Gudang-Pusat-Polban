<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\TipeUnit;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('unit_kerjas')->insert([
            [
                'nama_unit' => 'JTK (Teknik Komputer)',
                'tipe_unit' => TipeUnit::JURUSAN,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_unit' => 'Bagian Umum',
                'tipe_unit' => TipeUnit::UNIT_LAIN,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_unit' => 'Direktorat',
                'tipe_unit' => TipeUnit::UNIT_LAIN,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
