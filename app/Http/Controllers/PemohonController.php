<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Tambahkan ini buat validasi update
use Inertia\Inertia;

class PemohonController extends Controller
{
    public function index(Request $request) 
    {
        $query = User::where('role', 'pemohon')->with('unitKerja');

        // Logic Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%") // ilike untuk Postgres
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('nip', 'ilike', "%{$search}%");
            });
        }

        $pemohons = $query->paginate(6)->withQueryString();

        return Inertia::render('ManajemenAkun/Pemohon/Index', [
            'pemohons' => $pemohons,
            'filters' => $request->only(['search'])
        ]);
    }

    // 👇 INI YANG TADI HILANG (Fungsi untuk menampilkan Form Tambah)
    public function create()
    {
        // Ambil data Unit Kerja buat Dropdown
        $unitKerjas = UnitKerja::all();

        return Inertia::render('ManajemenAkun/Pemohon/Create', [
            'unitKerjas' => $unitKerjas
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
            'role' => 'pemohon', // Kita set string manual biar aman
        ]);

        return redirect()->route('manajemen-akun.pemohon.index')
            ->with('success', 'Akun Pemohon berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $unitKerjas = UnitKerja::all(); // Variabel disamakan jadi unitKerjas

        return Inertia::render('ManajemenAkun/Pemohon/Edit', [
            'user' => $user,
            'unitKerjas' => $unitKerjas
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Pake Rule::unique biar bisa ignore ID sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'nip' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'unit_kerja_id' => $request->unit_kerja_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('manajemen-akun.pemohon.index')
            ->with('success', 'Data Pemohon berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('manajemen-akun.pemohon.index')
            ->with('success', 'Akun berhasil dihapus!');
    }
}