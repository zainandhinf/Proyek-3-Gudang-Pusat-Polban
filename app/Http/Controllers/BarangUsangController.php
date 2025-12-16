<?php

namespace App\Http\Controllers;

use App\Models\BarangUsang;
use App\Models\DetailBarangUsang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Imports\BarangUsangImport;
use Maatwebsite\Excel\Facades\Excel;

class BarangUsangController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangUsang::query()->with(['dicatatOleh', 'detail']);

        if ($request->filled('search')) {
            $query->where('no_catat', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('no_bukti', 'like', '%' . $request->input('search') . '%');
        }

        // Tampilkan data header
        $barangUsangs = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('BarangUsang/index', [
            'barangUsangs' => $barangUsangs,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        // Kirim data barang untuk dropdown
        $barangs = Barang::with('satuan')->orderBy('nama_barang')->get();
        return Inertia::render('BarangUsang/create', [
            'barangs' => $barangs
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Sesuaikan nama field form)
        $request->validate([
            'tanggal_catat' => 'required|date',
            'no_bukti'      => 'required|string|max:255',
            'items'         => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah'    => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // 2. Buat Header (Gunakan no_catat & tanggal_catat, Hapus status)
            $barangUsang = BarangUsang::create([
                'no_catat' => 'BR-' . now()->format('YmdHis'), // Generate No Catat
                'tanggal_catat' => $request->tanggal_catat,
                'no_bukti' => $request->no_bukti,
                'keterangan' => $request->keterangan,
                'dicatat_oleh_user_id' => Auth::id(),
            ]);

            // 3. Buat Detail (Hapus satuan)
            foreach ($request->items as $item) {
                DetailBarangUsang::create([
                    'barang_usang_id' => $barangUsang->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'keterangan_rusak' => $item['catatan'] ?? null,
                ]);
            }
        });

        return redirect()->route('barang-usang.index')->with('success', 'Laporan barang rusak berhasil disimpan.');
    }

    public function show($id)
    {
        $barangUsang = BarangUsang::with(['detail.barang', 'dicatatOleh'])->findOrFail($id);

        return Inertia::render('BarangUsang/detail', [
            'laporan' => $barangUsang,
            'detail' => $barangUsang->detail
        ]);
    }

    public function destroy($id)
    {
        BarangUsang::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    }


    /**
     * Import Data Barang Usang dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new BarangUsangImport, $request->file('file'));
            return back()->with('success', 'Laporan Barang Rusak berhasil diimport & Stok dikurangi!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}