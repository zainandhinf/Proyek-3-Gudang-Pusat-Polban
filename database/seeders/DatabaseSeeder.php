<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Unit Kerja dulu
        $unitId = DB::table('unit_kerjas')->insertGetId([
            'nama_unit' => 'Pusat Komputer',
            'tipe_unit' => 'UnitLain',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UnitKerjaSeeder::class,
            SatuanSeeder::class,
            KelompokBarangSeeder::class,
        ]);
        
        $this->call([
            UserSeeder::class,
            BarangSeeder::class,
        ]);
    }
}