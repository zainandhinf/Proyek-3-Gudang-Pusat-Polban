<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SatuanController extends Controller
{
    /**
     * Menampilkan daftar data satuan dengan pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $satuans = Satuan::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nama_satuan', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
            })
            ->paginate(5) // Mengambil 5 data per halaman
            ->withQueryString();

        return Inertia::render('Satuan/index', [
            'satuans' => $satuans,
            'filters' => $request->only('search') // Kirim filter pencarian kembali ke Vue
        ]);
    }

    /**
     * Menampilkan form untuk membuat data baru.
     */
    public function create()
    {
        return Inertia::render('Satuan/create');
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'nama_satuan' => 'required|string|unique:satuans|max:100',
            'keterangan' => 'nullable|string|max:255',
        ])->validate();

        Satuan::create($request->all());

        return Redirect::route('satuans.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data.
     */
    public function edit(Satuan $satuan)
    {
        return Inertia::render('Satuan/edit', [
            'satuan' => $satuan
        ]);
    }

    /**
     * Update data di database.
     */
    public function update(Request $request, Satuan $satuan)
    {
        Validator::make($request->all(), [
            'nama_satuan' => 'required|string|max:100|unique:satuans,nama_satuan,' . $satuan->id,
            'keterangan' => 'nullable|string|max:255',
        ])->validate();

        $satuan->update($request->all());

        return Redirect::route('satuans.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    /**
     * Menghapus data dari database.
     */
    public function destroy(Satuan $satuan)
    {
        // Tambahkan proteksi jika satuan masih digunakan oleh barang
        if ($satuan->barangs()->count() > 0) {
            return Redirect::route('satuans.index')->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh data barang.');
        }

        $satuan->delete();

        return Redirect::route('satuans.index')->with('success', 'Satuan berhasil dihapus.');
    }
}