<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\KelompokBarang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query()->with(['satuan', 'kelompokBarang']);

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
            'satuans' => Satuan::all(),
            'kelompok_barangs' => KelompokBarang::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|max:255',
            'kelompok_barang_id' => 'required|exists:kelompok_barangs,id',
            'no_urut' => 'required|numeric', // Input manual operator
            'satuan_id' => 'required|exists:satuans,id',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048', // Validasi Foto
        ]);

        // 1. Generate Kode Barang (Kode Kelompok + No Urut)
        // Contoh: KTS + . + 001 = KTS.001
        $kelompok = KelompokBarang::find($request->kelompok_barang_id);
        // Format no_urut jadi 3 digit (001, 010, etc) atau sesuai input mentah
        // $kode_final = $kelompok->kode_kelompok . '.' . str_pad($request->no_urut, 3, '0', STR_PAD_LEFT);
        $kode_final = $kelompok->kode_kelompok . '.' . $request->no_urut;

        // Cek unik manual agar pesan error jelas
        if (Barang::where('kode_barang', $kode_final)->exists()) {
            return back()->withErrors(['no_urut' => 'Kode barang ' . $kode_final . ' sudah ada. Ganti no urut.']);
        }

        // 2. Handle Upload Foto
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('barang_images', 'public');
        }

        // 3. Simpan
        Barang::create([
            'kode_barang' => $kode_final,
            'nama_barang' => $request->nama_barang,
            'stok_saat_ini' => $request->stok ?? 0,
            'harga' => $request->harga,
            'satuan_id' => $request->satuan_id,
            'kelompok_barang_id' => $request->kelompok_barang_id,
            'deskripsi' => $request->deskripsi,
            'foto' => $pathFoto,
        ]);

        return Redirect::route('barangs.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return Inertia::render('Barang/edit', [
            'barang' => $barang,
            'satuans' => Satuan::all(),
            'kelompok_barangs' => KelompokBarang::all(),
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        // Validasi (Kode barang biasanya tidak diganti, tapi jika perlu fitur itu, logikanya mirip store)
        $request->validate([
            'nama_barang' => 'required|max:255',
            'satuan_id' => 'required|exists:satuans,id',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['foto', 'kode_barang', 'method']);

        // Handle Foto Baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $data['foto'] = $request->file('foto')->store('barang_images', 'public');
        }

        $barang->update($data);

        return Redirect::route('barangs.index')->with('success', 'Data barang diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }
        $barang->delete();
        return Redirect::route('barangs.index')->with('success', 'Barang berhasil dihapus.');
    }


    /**
     * Import Data Barang dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new BarangImport, $request->file('file'));
            
            return back()->with('success', 'Import Selesai! Stok barang yang ada telah diakumulasikan, dan barang baru telah ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}