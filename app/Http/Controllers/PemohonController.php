<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use App\Enums\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PemohonController extends Controller
{
    public function index()
    {
        // Menggunakan Enum Role::PEMOHON agar lebih aman
        // Pastikan di User.php sudah ada cast ke Role::class
        $pemohons = User::where('role', Role::PEMOHON) 
            ->with('unitKerja')
            ->paginate(10);

        return Inertia::render('ManajemenAkun/Pemohon/Index', [
            'pemohons' => $pemohons
        ]);
    }

    public function create()
    {
        $units = UnitKerja::all(); 
        return Inertia::render('ManajemenAkun/Pemohon/Create', [
            'units' => $units
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|string|unique:users,nip',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'unit_kerja_id' => $request->unit_kerja_id,
            'password' => Hash::make($request->password),
            // Menggunakan Enum langsung (Laravel akan otomatis ambil valuenya karena sudah di-cast di Model)
            'role' => Role::PEMOHON, 
        ]);
        

        return redirect()->route('manajemen-akun.pemohon.index')
            ->with('success', 'Akun Pemohon berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $units = UnitKerja::all();

        return Inertia::render('ManajemenAkun/Pemohon/Edit', [
            'user' => $user,
            'units' => $units
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi unik kecuali punya user ini sendiri
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nip' => 'required|string|unique:users,nip,'.$user->id,
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'password' => 'nullable|string|min:8|confirmed', // Password opsional saat edit
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'unit_kerja_id' => $request->unit_kerja_id,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('manajemen-akun.pemohon.index')
            ->with('success', 'Data Pemohon berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($id);
        
        // Hapus user
        $user->delete();

        // Kembali ke halaman index
        return redirect()->route('manajemen-akun.pemohon.index')
            ->with('success', 'Akun berhasil dihapus!');
    }
}