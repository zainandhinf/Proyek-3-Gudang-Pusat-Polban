<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MutasiBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MutasiBarang::query()->with(['detail.barang','dicatatOleh']);

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function($qq) use ($q) {
                $qq->where('nomor_mutasi','like',"%{$q}%")
                ->orWhere('keterangan','like',"%{$q}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_mutasi', $request->input('jenis'));
        }

        $mutasis = $query->orderBy('tanggal_mutasi','desc')->paginate(12)->withQueryString();

        $barangs = Barang::with(['kategori','satuan'])->orderBy('nama_barang')->get();

        return Inertia::render('MutasiBarang/index', [
            'mutasis' => $mutasis->through(fn($m) => [
                'id' => $m->id,
                'nomor_mutasi' => $m->nomor_mutasi,
                'jenis_mutasi' => $m->jenis_mutasi,
                'tanggal_mutasi' => $m->tanggal_mutasi->format('d-m-Y'),
                'keterangan' => $m->keterangan,
                'dicatat_oleh' => [ 'id' => $m->dicatatOleh->id ?? null, 'name' => $m->dicatatOleh->name ?? '-' ],
            ]),
            'barangs' => $barangs,
            'filters' => $request->only(['search','jenis']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs = Barang::with(['satuan'])
            ->select('id','nama_barang','satuan_id','stok_saat_ini')
            ->orderBy('nama_barang')
            ->get();

        return Inertia::render('MutasiBarang/create', ['barangs' => $barangs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_mutasi' => 'required|in:masuk,keluar',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            // ------------------------------
            // 1. SIMPAN HEADER MUTASI
            // ------------------------------
            $mutasi = MutasiBarang::create([
                'jenis_mutasi' => $validated['jenis_mutasi'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'keterangan' => $validated['keterangan'] ?? null,
                'dicatat_oleh_user_id' => auth()->id(),
            ]);

            // -------------------------------------
            // 2. LOOP DETAIL MUTASI
            // -------------------------------------
            foreach ($validated['items'] as $item) {

                $barang = Barang::findOrFail($item['barang_id']);

                // Hitung stok baru
                if ($validated['jenis_mutasi'] === 'masuk') {
                    $stokBaru = $barang->stok_saat_ini + $item['jumlah'];
                } else { // keluar
                    // validasi stok tidak boleh negatif
                    if ($barang->stok_saat_ini < $item['jumlah']) {
                        throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi!");
                    }
                    $stokBaru = $barang->stok_saat_ini - $item['jumlah'];
                }

                // -------------------------------------
                // 2A. SIMPAN DETAIL
                // -------------------------------------
                DetailMutasiBarang::create([
                    'mutasi_barang_id' => $mutasi->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // -------------------------------------
                // 2B. UPDATE STOK BARANG
                // -------------------------------------
                $barang->update([
                    'stok_saat_ini' => $stokBaru
                ]);
            }

            DB::commit();

            return redirect()
                ->route('mutasi-barang.index')
                ->with('success', 'Mutasi barang berhasil dicatat.');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(MutasiBarang $mutasiBarang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MutasiBarang $mutasiBarang)
    {
        $mutasiBarang->load(['detail.barang.satuan']);

        $barangs = Barang::with('satuan')
            ->select('id','nama_barang','satuan_id','stok_saat_ini')
            ->orderBy('nama_barang')
            ->get();

        return Inertia::render('MutasiBarang/edit', [
            'mutasi' => $mutasiBarang,
            'barangs' => $barangs
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MutasiBarang $mutasiBarang)
    {
        $validated = $request->validate([
            'jenis_mutasi' => 'required|in:masuk,keluar',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // STEP 1 — REVERT STOK LAMA
            foreach ($mutasiBarang->detail as $old) {

                $barang = Barang::findOrFail($old->barang_id);

                if ($mutasiBarang->jenis_mutasi === 'masuk') {
                    // revert stok: kurangi stok yang dulu ditambahkan
                    $barang->stok_saat_ini -= $old->jumlah;
                } else {
                    // revert stok: tambahkan kembali stok yang dulu dikurangi
                    $barang->stok_saat_ini += $old->jumlah;
                }

                $barang->save();
            }

            // Hapus semua detail lama
            $mutasiBarang->detail()->delete();


            // STEP 2 — UPDATE HEADER
            $mutasiBarang->update([
                'jenis_mutasi' => $validated['jenis_mutasi'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);


            // STEP 3 — APPLY MUTASI BARU
            foreach ($validated['items'] as $item) {
                
                $barang = Barang::findOrFail($item['barang_id']);

                if ($validated['jenis_mutasi'] === 'masuk') {
                    $stokBaru = $barang->stok_saat_ini + $item['jumlah'];
                } else {
                    if ($barang->stok_saat_ini < $item['jumlah']) {
                        throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi!");
                    }
                    $stokBaru = $barang->stok_saat_ini - $item['jumlah'];
                }

                DetailMutasiBarang::create([
                    'mutasi_barang_id' => $mutasiBarang->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                ]);

                $barang->update(['stok_saat_ini' => $stokBaru]);
            }

            DB::commit();

            return redirect()
                ->route('mutasi-barang.index')
                ->with('success', 'Mutasi barang berhasil diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MutasiBarang $mutasiBarang)
    {
        //
    }

}
