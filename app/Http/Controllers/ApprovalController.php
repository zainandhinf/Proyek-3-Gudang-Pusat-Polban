<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'approval')->with('unitKerja');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('nip', 'ilike', "%{$search}%");
            });
        }

        $approvals = $query->paginate(6)->withQueryString();

        return Inertia::render('ManajemenAkun/Approval/Index', [
            'approvals' => $approvals,
            'filters' => $request->only(['search'])
        ]);
    }

    // 👇 Tambahkan Create
    public function create()
    {
        $unitKerjas = UnitKerja::all();
        return Inertia::render('ManajemenAkun/Approval/Create', [
            'unitKerjas' => $unitKerjas
        ]);
    }

    // 👇 Tambahkan Store
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
            'role' => 'approval', // Set otomatis jadi approval
        ]);

        return redirect()->route('manajemen-akun.approval.index')
            ->with('success', 'Akun Approval berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $unitKerjas = UnitKerja::all();

        return Inertia::render('ManajemenAkun/Approval/Edit', [
            'user' => $user,
            'unitKerjas' => $unitKerjas
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
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

        return redirect()->route('manajemen-akun.approval.index')
            ->with('success', 'Data Approval berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('manajemen-akun.approval.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}