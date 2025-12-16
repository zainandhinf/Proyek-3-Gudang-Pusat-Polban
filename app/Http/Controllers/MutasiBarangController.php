<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Imports\MutasiBarangImport;
use Maatwebsite\Excel\Facades\Excel;

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
                ->orWhere('no_dokumen','like',"%{$q}%")
                ->orWhere('no_bukti','like',"%{$q}%")  
                ->orWhere('keterangan','like',"%{$q}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_mutasi', $request->input('jenis'));
        }

        $mutasis = $query->orderBy('tanggal_mutasi','desc')->paginate(12)->withQueryString();

        $barangs = Barang::with(['kelompokBarang','satuan'])->orderBy('nama_barang')->get();

        return Inertia::render('MutasiBarang/index', [
            'mutasis' => $mutasis->through(fn($m) => [
                'id' => $m->id,
                'nomor_mutasi' => $m->nomor_mutasi,
                'no_dokumen' => $m->no_dokumen,
                'no_bukti' => $m->no_bukti,
                'jenis_mutasi' => $m->jenis_mutasi,
                'tanggal_mutasi' => $m->tanggal_mutasi,
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
            'no_dokumen' => 'nullable|string|max:255',
            'no_bukti' => 'nullable|string|max:255',
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
                'no_dokumen' => $validated['no_dokumen'] ?? null,
                'no_bukti' => $validated['no_bukti'] ?? null,
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
    public function show($id)
    {
        $mutasi = MutasiBarang::with(['detail.barang.satuan', 'dicatatOleh'])
            ->findOrFail($id);

        return Inertia::render('MutasiBarang/detail', [
            'mutasi' => [
                'id' => $mutasi->id,
                'nomor_mutasi' => $mutasi->nomor_mutasi,
                'no_dokumen' => $mutasi->no_dokumen,
                'no_bukti' => $mutasi->no_bukti,
                'jenis_mutasi' => $mutasi->jenis_mutasi,
                'tanggal_mutasi' => $mutasi->tanggal_mutasi->format('d-m-Y'),
                'keterangan' => $mutasi->keterangan,
                'dicatat_oleh' => $mutasi->dicatatOleh->name ?? '-',
            ],

            'detail' => $mutasi->detail->map(function ($d) use ($mutasi) {

                $stokAwal = $d->barang->stok_saat_ini;

                // Hitung stok sebelum & sesudah
                if ($mutasi->jenis_mutasi === 'masuk') {
                    $stokSebelum = $stokAwal - $d->jumlah;
                    $stokSesudah = $stokAwal;
                } else {
                    $stokSebelum = $stokAwal + $d->jumlah;
                    $stokSesudah = $stokAwal;
                }

                return [
                    'id' => $d->id,
                    'barang_id' => $d->barang_id,
                    'nama_barang' => $d->barang->nama_barang,
                    'jumlah' => $d->jumlah,
                    'satuan' => $d->barang->satuan->nama_satuan ?? '-',
                    'catatan' => $d->catatan,

                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                ];
            }),
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(MutasiBarang $mutasiBarang)
    // {
    //     $mutasiBarang->load(['detail.barang.satuan']);

    //     $barangs = Barang::with('satuan')
    //         ->select('id','nama_barang','satuan_id','stok_saat_ini')
    //         ->orderBy('nama_barang')
    //         ->get();

    //     return Inertia::render('MutasiBarang/edit', [
    //         'mutasi' => $mutasiBarang,
    //         'barangs' => $barangs
    //     ]);
    // }

    public function edit($id)
    {
        $mutasi = MutasiBarang::with(['detail.barang.satuan'])->findOrFail($id);
        $barangs = Barang::with(['satuan'])->orderBy('nama_barang')->get();

        return Inertia::render('MutasiBarang/edit', [
            'mutasi' => [
                'id' => $mutasi->id,
                'jenis_mutasi' => $mutasi->jenis_mutasi,
                'tanggal_mutasi' => $mutasi->tanggal_mutasi->format('Y-m-d'),
                'no_dokumen' => $mutasi->no_dokumen,
                'no_bukti' => $mutasi->no_bukti,
                'keterangan' => $mutasi->keterangan,
                'detail' => $mutasi->detail->map(fn($d) => [
                    'id' => $d->id,
                    'barang_id' => $d->barang_id,
                    'jumlah' => $d->jumlah,
                    'catatan' => $d->catatan,
                    'barang' => [
                        'nama_barang' => $d->barang->nama_barang,
                        'stok_saat_ini' => $d->barang->stok_saat_ini,
                        'satuan' => [
                            'nama_satuan' => $d->barang->satuan->nama_satuan ?? '-'
                        ]
                    ]
                ]),
            ],
            'barangs' => $barangs
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_mutasi' => ['required', 'in:masuk,keluar'],
            'tanggal_mutasi' => ['required', 'date'],
            'no_dokumen' => 'nullable|string|max:255',
            'no_bukti' => 'nullable|string|max:255',
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.catatan' => ['nullable', 'string'],
        ]);

        $mutasi = MutasiBarang::findOrFail($id);

        // HEADER UPDATE
        $mutasi->update([
            'jenis_mutasi' => $validated['jenis_mutasi'],
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'no_dokumen' => $validated['no_dokumen'],
            'no_bukti' => $validated['no_bukti'],
            'keterangan' => $validated['keterangan'],
        ]);

        // MANAGE DETAIL MUTASI
        $existingIds = $mutasi->detail()->pluck('id')->toArray();
        $sentIds = collect($validated['items'])->pluck('id')->filter()->toArray();

        // Delete removed rows
        $idsToDelete = array_diff($existingIds, $sentIds);
        if (!empty($idsToDelete)) {
            $mutasi->detail()->whereIn('id', $idsToDelete)->delete();
        }

        // Insert + update
        foreach ($validated['items'] as $item) {
            if (!empty($item['id'])) {
                // Update
                $mutasi->detail()->where('id', $item['id'])->update([
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                ]);
            } else {
                // Insert baru
                $mutasi->detail()->create([
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }
        }

        return redirect()->route('mutasi-barang.index')
            ->with('success', 'Data Mutasi Barang berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mutasiBarang = MutasiBarang::findOrFail($id);

        $mutasiBarang->delete();

        return redirect()
            ->route('mutasi-barang.index')
            ->with('success', 'Data barang masuk dan semua detailnya berhasil dihapus.');
    }


    /**
     * Import mutasi barang from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new MutasiBarangImport, $request->file('file'));
            return back()->with('success', 'Data mutasi berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
    
    // Opsional: Buat route untuk download template excel kosong
    public function template()
    {
        // Logika generate file excel dummy header
        // Header: Tanggal, Jenis, No Bukti, Keterangan, Kode Barang, Jumlah, Catatan
        // ... (bisa skip dulu jika user buat manual)
    }

}
