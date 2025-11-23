<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\MutasiBarangDetail;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MutasiBarang $mutasiBarang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MutasiBarang $mutasiBarang)
    {
        //
    }
}
