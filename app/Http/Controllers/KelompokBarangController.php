<?php

namespace App\Http\Controllers;

use App\Models\KelompokBarang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class KelompokBarangController extends Controller
{
    public function index(Request $request)
    {
        $kelompok_barangs = KelompokBarang::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nama_kelompok', 'like', "%{$search}%")
                      ->orWhere('kode_kelompok', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('KelompokBarang/index', [
            'kelompok_barangs' => $kelompok_barangs,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('KelompokBarang/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kelompok' => 'required|string|max:10|unique:kelompok_barangs,kode_kelompok',
            'nama_kelompok' => 'required|string|max:255',
        ]);

        // Paksa kode kelompok jadi huruf besar (uppercase)
        KelompokBarang::create([
            'kode_kelompok' => strtoupper($request->kode_kelompok),
            'nama_kelompok' => $request->nama_kelompok,
        ]);

        return Redirect::route('kelompok-barang.index')->with('success', 'Kelompok Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelompok = KelompokBarang::findOrFail($id);
        return Inertia::render('KelompokBarang/edit', [
            'kelompok' => $kelompok
        ]);
    }

    public function update(Request $request, $id)
    {
        $kelompok = KelompokBarang::findOrFail($id);

        $request->validate([
            'kode_kelompok' => 'required|string|max:10|unique:kelompok_barangs,kode_kelompok,' . $kelompok->id,
            'nama_kelompok' => 'required|string|max:255',
        ]);

        $kelompok->update([
            'kode_kelompok' => strtoupper($request->kode_kelompok),
            'nama_kelompok' => $request->nama_kelompok,
        ]);

        return Redirect::route('kelompok-barang.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelompok = KelompokBarang::findOrFail($id);
        $kelompok->delete();

        return Redirect::route('kelompok-barang.index')->with('success', 'Data berhasil dihapus.');
    }
}