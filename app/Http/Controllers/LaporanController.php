<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use App\Exports\LaporanMutasiExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function mutasi(Request $request)
    {
        $query = MutasiBarang::query()
            ->with(['dicatatOleh', 'detail.barang.satuan', 'detail.barang.kelompokBarang']);

        // --- 1. Filter Tanggal ---
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_mutasi', [
                $request->input('start_date'), 
                $request->input('end_date')
            ]);
        }

        // --- 2. Filter Jenis ---
        if ($request->filled('jenis')) {
            $query->where('jenis_mutasi', $request->input('jenis'));
        }

        // --- 3. Filter Nomor Mutasi (Search) ---
        if ($request->filled('search')) {
            $query->where('nomor_mutasi', 'like', '%' . $request->input('search') . '%');
        }

        $data = $query->orderBy('tanggal_mutasi', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        return Inertia::render('Laporan/mutasi', [
            'laporan' => $data->through(fn($m) => [
                'id' => $m->id,
                'tanggal' => Carbon::parse($m->tanggal_mutasi)->format('d-m-Y'),
                'nomor_mutasi' => $m->nomor_mutasi,
                'no_dokumen' => $m->no_dokumen ?? '-',
                'no_bukti' => $m->no_bukti ?? '-',
                'jenis' => $m->jenis_mutasi,
                'total_item' => $m->detail->count(), 
                'ringkasan_barang' => $m->detail->take(2)->map(fn($d) => $d->barang->nama_barang)->join(', ') . ($m->detail->count() > 2 ? '...' : ''),
                'keterangan' => $m->keterangan,
                'oleh' => $m->dicatatOleh->name ?? '-',
            ]),
            'filters' => $request->only(['start_date', 'end_date', 'jenis', 'search']), // Tambah search
        ]);
    }

    public function exportMutasi(Request $request)
    {
        $query = MutasiBarang::query()
            ->with([
                'detail.barang.satuan',
                'detail.barang.kelompokBarang',
                'permintaan',
                'dicatatOleh'
            ]);

        // --- LOGIKA UTAMA EKSPOR ---
        
        // Cek apakah ada ID spesifik yang dipilih user (dikirim sebagai string comma-separated: "1,2,5")
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->input('selected_ids'));
            $query->whereIn('id', $ids);
            
            // Urutkan berdasarkan urutan ID yang dipilih (opsional) atau tetap tanggal
            $query->orderBy('tanggal_mutasi', 'asc');
            
        } else {
            // JIKA TIDAK ADA YANG DIPILIH, GUNAKAN FILTER BIASA
            
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_mutasi', [
                    $request->input('start_date'), 
                    $request->input('end_date')
                ]);
            }

            if ($request->filled('jenis')) {
                $query->where('jenis_mutasi', $request->input('jenis'));
            }

            if ($request->filled('search')) {
                $query->where('nomor_mutasi', 'like', '%' . $request->input('search') . '%');
            }
            
            $query->orderBy('tanggal_mutasi', 'asc');
        }

        $mutasis = $query->get();

        if ($mutasis->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        // --- EXCEL ---
        if ($request->input('type') === 'excel') {
            return Excel::download(
                new LaporanMutasiExport($mutasis), 
                'Bukti_Mutasi_' . date('dmY_His') . '.xlsx'
            );
        }

        // --- PDF (PORTRAIT) ---
        if ($request->input('type') === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan_mutasi_pdf', [
                'mutasis' => $mutasis,
                'tanggal_cetak' => date('d F Y')
            ])->setPaper('a4', 'portrait'); // Pastikan Portrait

            return $pdf->download('Bukti_Mutasi_' . date('dmY_His') . '.pdf');
        }
        
        return back();
    }
}