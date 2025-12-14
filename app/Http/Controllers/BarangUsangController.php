<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangUsang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class BarangUsangController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangUsang::query()->with('barang');

        if ($request->search) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('kode_barang', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('BarangUsang/index', [
            'barangUsangs' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('BarangUsang/create', [
            'barangs' => Barang::all(), // Kirim data barang untuk dropdown
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_laporan' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string',
        ]);

        // Cek stok cukup atau tidak (opsional, tapi disarankan)
        $barang = Barang::find($request->barang_id);
        if ($barang->stok < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Jumlah melebihi stok yang tersedia.']);
        }

        BarangUsang::create($request->all());

        return Redirect::route('barang-usang.index')->with('success', 'Laporan barang usang berhasil dibuat.');
    }

    public function edit(BarangUsang $barangUsang)
    {
        return Inertia::render('BarangUsang/edit', [
            'barangUsang' => $barangUsang,
            'barangs' => Barang::all(),
        ]);
    }

    public function update(Request $request, BarangUsang $barangUsang)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_laporan' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string',
        ]);

        $barangUsang->update($request->all());

        return Redirect::route('barang-usang.index')->with('success', 'Laporan diperbarui.');
    }

    public function destroy(BarangUsang $barangUsang)
    {
        $barangUsang->delete();
        return Redirect::route('barang-usang.index')->with('success', 'Laporan dihapus.');
    }
}