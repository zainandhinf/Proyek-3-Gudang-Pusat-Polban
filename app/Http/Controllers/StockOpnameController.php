<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\DetailStockOpname;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Enums\StatusOpname;
use App\Imports\StockOpnameImport;
use Maatwebsite\Excel\Facades\Excel;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $opnames = StockOpname::with('dicatatOleh')
            ->latest()
            ->paginate(10);

        return Inertia::render('StockOpname/index', [
            'opnames' => $opnames
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Cek apakah ada sesi yang masih menggantung (Status: Proses)
        $ongoing = StockOpname::where('status', StatusOpname::PROSES->value)->first();
        if ($ongoing) {
            return redirect()->route('stock-opname.edit', $ongoing->id)
                ->with('warning', 'Harap selesaikan sesi opname yang sedang berjalan terlebih dahulu.');
        }

        // 2. Mulai Sesi Baru & Snapshot
        DB::transaction(function () {
            // A. Buat Header (Tanpa Keterangan)
            $opname = StockOpname::create([
                'no_opname' => 'SO-' . now()->format('YmdHis'),
                'tanggal_opname' => now(),
                'dicatat_oleh_user_id' => Auth::id(),
                'status' => 'Proses',
            ]);

            // B. Snapshot Stok (Copy stok saat ini ke tabel detail)
            $barangs = Barang::all();
            foreach ($barangs as $barang) {
                DetailStockOpname::create([
                    'stock_opname_id' => $opname->id,
                    'barang_id' => $barang->id,
                    'stok_sistem' => $barang->stok_saat_ini,
                    'stok_fisik' => null, // Awalnya null (belum dihitung)
                    'selisih' => null,
                ]);
            }
        });

        // 3. Redirect langsung ke halaman hitung (Edit)
        $newOpname = StockOpname::latest()->first();
        return redirect()->route('stock-opname.edit', $newOpname->id)
             ->with('success', 'Sesi Opname dimulai. Stok sistem telah dikunci.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $opname = StockOpname::with(['detailStockOpnames.barang.satuan', 'dicatatOleh'])
            ->findOrFail($id);

        return Inertia::render('StockOpname/detail', [
            'opname' => $opname
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $opname = StockOpname::with(['detailStockOpnames.barang.satuan', 'dicatatOleh'])
            ->findOrFail($id);

        // Jika sudah selesai, lempar ke halaman detail (tidak boleh edit lagi)
        if ($opname->status === StatusOpname::SELESAI) {
            return redirect()->route('stock-opname.show', $id);
        }

        return Inertia::render('StockOpname/form', [
            'opname' => $opname
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $opname = StockOpname::with('detailStockOpnames.barang')->findOrFail($id);

        // SKENARIO 1: SELESAI (FINISH)
        if ($request->has('finish') && $request->finish == true) {
            DB::transaction(function () use ($opname) {
                // Update Status
                $opname->update(['status' => 'Selesai']);

                // Update Stok Master Barang
                foreach ($opname->detailStockOpnames as $detail) {
                    // Hanya update jika stok fisik diisi
                    if (!is_null($detail->stok_fisik)) {
                        $detail->barang->update([
                            'stok_saat_ini' => $detail->stok_fisik
                        ]);
                    }
                }
            });

            return redirect()->route('stock-opname.index')
                ->with('success', 'Stock Opname selesai. Stok master barang telah diperbarui.');
        }

        // SKENARIO 2: SIMPAN DRAFT (SAVE PROGRESS)
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:detail_stock_opnames,id',
            'items.*.stok_fisik' => 'nullable|integer|min:0',
            'items.*.keterangan' => 'nullable|string', // Keterangan per item (alasan selisih) tetap ada
        ]);

        foreach ($validated['items'] as $item) {
            $detail = DetailStockOpname::find($item['id']);
            
            // Hitung selisih otomatis
            $fisik = $item['stok_fisik'];
            $selisih = is_null($fisik) ? null : ($fisik - $detail->stok_sistem);

            $detail->update([
                'stok_fisik' => $fisik,
                'selisih' => $selisih,
                'keterangan' => $item['keterangan'],
            ]);
        }

        return redirect()->route('stock-opname.index')->with('success', 'Progress Disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     $opname = StockOpname::findOrFail($id);
    //     if ($opname->status !== 'Proses') {
    //         return back()->with('error', 'Tidak bisa membatalkan sesi yang sudah selesai.');
    //     }
    //     $opname->delete();
        
    //     return redirect()->route('stock-opname.index')->with('success', 'Sesi opname dibatalkan.');
    // }

    public function destroy($id)
    {
        $opname = StockOpname::findOrFail($id);

        // SALAH (Membandingkan Enum vs String):
        // if ($opname->status !== 'Proses') { 

        // BENAR (Membandingkan Enum vs Enum):
        if ($opname->status !== StatusOpname::PROSES) { 
            return back()->with('error', 'Tidak bisa membatalkan sesi yang sudah selesai.');
        }

        $opname->delete();
        
        return redirect()->route('stock-opname.index')->with('success', 'Sesi opname dibatalkan.');
    }

    /**
     * Import Data SO dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new StockOpnameImport, $request->file('file'));
            
            return redirect()->route('stock-opname.index')
                ->with('success', 'Data Stock Opname berhasil diimport & Stok Master diperbarui!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
