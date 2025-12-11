<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\DetailBarangMasukController;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $barangmasuks = BarangMasuk::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('tanggal_masuk', 'like', "%{$search}%")
                    ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('dicatat_oleh_user', 'like', "%{$search}%");
            })
            ->with('detailBarangMasuks.barang.satuan')
            ->with('dicatatOleh')
            ->paginate(10)
            ->withQueryString();

        $barangs = Barang::with(['kategori', 'satuan'])
            ->orderBy('nama_barang')
            ->get();

        return Inertia::render('BarangMasuk/index', [
            'barangmasuks' => $barangmasuks->through(fn($item) => [
                'id' => $item->id,
                'tanggal_masuk' => $item->tanggal_masuk_formatted,
                'keterangan' => $item->keterangan,
                'detailBarangMasuks' => $item->detailBarangMasuks,
                'dicatat_oleh' => [
                    'id' => $item->dicatatOleh->id ?? null,
                    'name' => $item->dicatatOleh->name ?? 'Tidak diketahui',
                ],
            ]),
            'barangs' => $barangs,
            'filters' => $request->only(['search']),
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
        $validated = $request->validate([
            'tanggal_masuk' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $barangMasuk = BarangMasuk::create([
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'keterangan' => $validated['keterangan'],
            'dicatat_oleh_user_id' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            app(DetailBarangMasukController::class)->store($barangMasuk->id, $item);
        }

        return redirect()->route('barang-masuk.index')->with('success', 'Data barang masuk berhasil disimpan.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::with([
            'detailBarangMasuks.barang.satuan',
            'dicatatOleh'
        ])->findOrFail($id);

        return Inertia::render('BarangMasuk/detail', [
            'barangMasuk' => [
                'id' => $barangMasuk->id,
                'tanggal_masuk' => $barangMasuk->tanggal_masuk->format('d-m-Y'),
                'keterangan' => $barangMasuk->keterangan,
                'operator' => $barangMasuk->dicatatOleh->name ?? '-',
            ],
            'detailBarang' => $barangMasuk->detailBarangMasuks->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'nama_barang' => $detail->barang->nama_barang,
                    'jumlah' => $detail->jumlah_masuk,
                    'satuan' => $detail->barang->satuan->nama_satuan ?? '-',
                ];
            }),
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::with([
            'detailBarangMasuks.barang.satuan'
        ])->findOrFail($id);

        $barangs = Barang::with(['satuan'])
            ->orderBy('nama_barang')
            ->get();

        return Inertia::render('BarangMasuk/edit', [
            'barangMasuk' => [
                'id' => $barangMasuk->id,
                'tanggal_masuk' => $barangMasuk->tanggal_masuk->format('Y-m-d'),
                'keterangan' => $barangMasuk->keterangan,
            ],
            'detailBarang' => $barangMasuk->detailBarangMasuks->map(fn($d) => [
                'id' => $d->id,
                'barang_id' => $d->barang_id,
                'nama_barang' => $d->barang->nama_barang,
                'jumlah' => $d->jumlah_masuk,
                'satuan' => $d->barang->satuan->nama_satuan ?? '-',
            ]),
            'barangs' => $barangs
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_masuk' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],

            'items.*.id' => ['nullable', 'integer'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);

        // HEADER UPDATE
        $barangMasuk->update([
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'keterangan' => $validated['keterangan'],
        ]);

        $existingIds = $barangMasuk->detailBarangMasuks()->pluck('id')->toArray();
        $sentIds = collect($validated['items'])->pluck('id')->filter()->toArray();

        // DELETE ANY detail removed
        $idsToDelete = array_diff($existingIds, $sentIds);
        if (!empty($idsToDelete)) {
            $barangMasuk->detailBarangMasuks()->whereIn('id', $idsToDelete)->delete();
        }

        // UPDATE / INSERT
        foreach ($validated['items'] as $item) {
            if (isset($item['id'])) {
                // update existing
                $barangMasuk->detailBarangMasuks()
                    ->where('id', $item['id'])
                    ->update([
                        'barang_id' => $item['barang_id'],
                        'jumlah_masuk' => $item['jumlah'],
                    ]);
            } else {
                // create new
                $barangMasuk->detailBarangMasuks()
                    ->create([
                        'barang_id' => $item['barang_id'],
                        'jumlah_masuk' => $item['jumlah'],
                    ]);
            }
        }

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Data barang masuk berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $barangMasuk->delete();

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Data barang masuk dan semua detailnya berhasil dihapus.');
    }

}
