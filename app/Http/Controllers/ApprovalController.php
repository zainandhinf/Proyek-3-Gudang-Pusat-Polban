<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    public function index()
    {
        $approvals = User::where('role', Role::APPROVAL)
            ->with('unitKerja')
            ->paginate(10);

        return Inertia::render('ManajemenAkun/Approval/Index', [
            'approvals' => $approvals
        ]);
    }

    public function create()
    {
        $units = UnitKerja::all();
        return Inertia::render('ManajemenAkun/Approval/Create', [
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
            'role' => Role::APPROVAL, 
        ]);

        return redirect()->route('manajemen-akun.approval.index')
            ->with('success', 'Akun Approval berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $units = UnitKerja::all();

        return Inertia::render('ManajemenAkun/Approval/Edit', [
            'user' => $user,
            'units' => $units
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nip' => 'required|string|unique:users,nip,'.$user->id,
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

    // 👇 INI YANG TADINYA BELUM ADA
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('manajemen-akun.approval.index')
            ->with('success', 'Akun Approval berhasil dihapus!');
    }
}