<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar kategori (Index).
     */
    public function index(Request $request)
    {
        $kategoris = Kategori::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('nama_kategori', 'like', "%{$search}%")
                      ->orWhere('kode_kategori', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->latest() // Mengurutkan dari yang terbaru
            ->paginate(10)
            ->withQueryString();

        // Pastikan file Vue ada di resources/js/Pages/Kategori/Index.vue
        return Inertia::render('Kategori/index', [
            'kategoris' => $kategoris,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menampilkan form tambah kategori (Create).
     */
    public function create()
    {
        // Pastikan file Vue ada di resources/js/Pages/Kategori/Create.vue
        return Inertia::render('Kategori/create');
    }

    /**
     * Menyimpan kategori baru ke database (Store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required|unique:kategoris,kode_kategori|max:50',
            'nama_kategori' => 'required|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create($request->all());

        return Redirect::route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit kategori (Edit).
     */
    public function edit(Kategori $kategori)
    {
        // Pastikan file Vue ada di resources/js/Pages/Kategori/Edit.vue
        return Inertia::render('Kategori/edit', [
            'kategori' => $kategori,
        ]);
    }

    /**
     * Memperbarui data kategori (Update).
     */
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            // Validasi unique mengecualikan ID kategori yang sedang diedit
            'kode_kategori' => 'required|max:50|unique:kategoris,kode_kategori,' . $kategori->id,
            'nama_kategori' => 'required|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        return Redirect::route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori (Destroy).
     */
    public function destroy(Kategori $kategori)
    {
        if ($kategori->barangs()->exists()) {
            return Redirect::route('kategori.index')->with('error', 'Gagal: Kategori digunakan oleh Barang.');
        }

        $kategori->delete();

        return Redirect::route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}