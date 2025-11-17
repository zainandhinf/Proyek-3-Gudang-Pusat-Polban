<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UnitKerja;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID unit kerja untuk relasi
        $unitJTK = UnitKerja::where('nama_unit', 'JTK (Teknik Komputer)')->first()->id;
        $unitUmum = UnitKerja::where('nama_unit', 'Bagian Umum')->first()->id;

        // User 1: OPERATOR
        User::firstOrCreate(
            ['email' => 'operator@polban.ac.id'],
            [
                'name' => 'Ahmad Operator',
                'nip' => '111111111',
                'password' => Hash::make('password'),
                'role' => Role::OPERATOR,
                'unit_kerja_id' => null, // Operator tidak terikat unit
            ]
        );

        // User 2: APPROVAL
        User::firstOrCreate(
            ['email' => 'approval@polban.ac.id'],
            [
                'name' => 'Budi Approval',
                'nip' => '222222222',
                'password' => Hash::make('password'),
                'role' => Role::APPROVAL,
                'unit_kerja_id' => null, // Approval tidak terikat unit
            ]
        );

        // User 3: PEMOHON (Unit Lain)
        User::firstOrCreate(
            ['email' => 'pemohon@polban.ac.id'],
            [
                'name' => 'Citra Pemohon (Umum)',
                'nip' => '333333333',
                'password' => Hash::make('password'),
                'role' => Role::PEMOHON,
                'unit_kerja_id' => $unitUmum,
            ]
        );

        // User 4: PEMOHON (Jurusan - untuk tes Aturan Bisnis )
        User::firstOrCreate(
            ['email' => 'pemohon.jtk@polban.ac.id'],
            [
                'name' => 'Dedi Pemohon (JTK)',
                'nip' => '444444444',
                'password' => Hash::make('password'),
                'role' => Role::PEMOHON,
                'unit_kerja_id' => $unitJTK,
            ]
        );
    }
}
