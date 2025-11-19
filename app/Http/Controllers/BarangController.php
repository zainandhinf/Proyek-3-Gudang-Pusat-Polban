<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query()->with(['kategori', 'satuan']);

        if ($request->search) {
            $query->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('kode_barang', 'like', "%{$request->search}%");
        }

        return Inertia::render('Barang/index', [
            'barangs' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Barang/create', [
            'kategoris' => Kategori::all(),
            'satuans' => Satuan::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang|max:50',
            'nama_barang' => 'required|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            // 'stok' => 'integer|min:0',
        ]);

        Barang::create($request->all());

        return Redirect::route('barangs.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return Inertia::render('Barang/edit', [
            'barang' => $barang,
            'kategoris' => Kategori::all(),
            'satuans' => Satuan::all(),
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
        ]);

        $barang->update($request->all());

        return Redirect::route('barangs.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return Redirect::route('barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}