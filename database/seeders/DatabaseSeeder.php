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

        // 2. Buat User OPERATOR
        User::create([
            'nip'           => '199001012022011001',
            'name'          => 'Operator Gudang',
            'email'         => 'operator@polban.ac.id',
            'password'      => Hash::make('password123'),
            'role'          => 'operator', // <--- Set role operator
            'unit_kerja_id' => $unitId,
        ]);

        // 3. Buat User APPROVAL (Untuk tes login approval nanti)
        User::create([
            'nip'           => '198001012010011002',
            'name'          => 'Kepala Unit',
            'email'         => 'approval@polban.ac.id',
            'password'      => Hash::make('password123'),
            'role'          => 'approval', // <--- Set role approval
            'unit_kerja_id' => $unitId,
        ]);

        // 4. Buat User PEMOHON (Untuk tes request barang)
        User::create([
            'nip'           => '200001012023011003',
            'name'          => 'Mahasiswa/Staff',
            'email'         => 'pemohon@polban.ac.id',
            'password'      => Hash::make('password123'),
            'role'          => 'pemohon', // <--- Set role pemohon
            'unit_kerja_id' => $unitId,
        ]);
    }
}