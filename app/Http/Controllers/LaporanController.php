<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use App\Exports\LaporanMutasiExport;
use App\Models\Permintaan;
use App\Exports\LaporanPermintaanExport;
use App\Models\StockOpname;
use App\Exports\LaporanStockOpnameExport;
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




    public function permintaan(Request $request)
    {
        $query = Permintaan::query()
            ->with(['pemohon', 'detailPermintaans.barang.satuan']);

        // 1. Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pengajuan', [
                $request->input('start_date') . ' 00:00:00', 
                $request->input('end_date') . ' 23:59:59'
            ]);
        }

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 3. Filter Search (No Permintaan / Nama Pemohon)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_permintaan', 'like', '%' . $request->input('search') . '%')
                  ->orWhereHas('pemohon', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%' . $request->input('search') . '%');
                  });
            });
        }

        $data = $query->orderBy('tanggal_pengajuan', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        return Inertia::render('Laporan/permintaan', [
            'laporan' => $data->through(fn($p) => [
                'id' => $p->id,
                'tanggal' => Carbon::parse($p->tanggal_pengajuan)->format('d-m-Y'),
                'no_permintaan' => $p->no_permintaan,
                'pemohon' => $p->pemohon->name ?? '-',
                'unit' => $p->jenis_keperluan, // Asumsi jenis_keperluan menyimpan unit/bagian
                'status' => $p->status,
                'total_item' => $p->detailPermintaans->count(),
                'ringkasan_barang' => $p->detailPermintaans->take(2)->map(fn($d) => $d->barang->nama_barang)->join(', ') . ($p->detailPermintaans->count() > 2 ? '...' : ''),
            ]),
            'filters' => $request->only(['start_date', 'end_date', 'status', 'search']),
        ]);
    }

    public function exportPermintaan(Request $request)
    {
        // 1. QUERY DATA (Sama seperti sebelumnya)
        $query = Permintaan::query()
            ->with(['pemohon', 'detailPermintaans.barang.satuan', 'approval']);

        // LOGIKA SELECT ID / FILTER
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->input('selected_ids'));
            $query->whereIn('id', $ids);
            // Urutkan sesuai input user atau ID
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_pengajuan', [
                    $request->input('start_date') . ' 00:00:00', 
                    $request->input('end_date') . ' 23:59:59'
                ]);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('search')) {
                $query->where('no_permintaan', 'like', '%' . $request->input('search') . '%');
            }
        }

        $data = $query->orderBy('tanggal_pengajuan', 'desc')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        // 2. EXPORT EXCEL
        if ($request->input('type') === 'excel') {
            return Excel::download(
                new LaporanPermintaanExport($data), 
                'Form_Permintaan_ATK_' . date('dmY_His') . '.xlsx'
            );
        }

        // 3. EXPORT PDF (BARU)
        if ($request->input('type') === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan_permintaan_pdf', [
                'permintaans' => $data
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Form_Permintaan_ATK_' . date('dmY_His') . '.pdf');
        }

        return back();
    }




    public function stockOpname(Request $request)
    {
        $query = StockOpname::query()
            ->with(['dicatatOleh', 'detailStockOpnames']); // Load detail untuk hitung statistik

        // 1. Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_opname', [
                $request->input('start_date') . ' 00:00:00', 
                $request->input('end_date') . ' 23:59:59'
            ]);
        }

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 3. Search
        if ($request->filled('search')) {
            $query->where('no_opname', 'like', '%' . $request->input('search') . '%');
        }

        $data = $query->orderBy('tanggal_opname', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        return Inertia::render('Laporan/stockopname', [
            'laporan' => $data->through(fn($s) => [
                'id' => $s->id,
                'tanggal' => Carbon::parse($s->tanggal_opname)->format('d-m-Y'),
                'no_opname' => $s->no_opname,
                'pencatat' => $s->dicatatOleh->name ?? '-',
                'status' => $s->status, // Mengambil value Enum atau String
                'total_item' => $s->detailStockOpnames->count(),
                // Menghitung berapa item yang selisihnya TIDAK 0
                'item_selisih' => $s->detailStockOpnames->filter(fn($d) => $d->selisih != 0 && $d->selisih !== null)->count(),
            ]),
            'filters' => $request->only(['start_date', 'end_date', 'status', 'search']),
        ]);
    }

    public function exportStockOpname(Request $request)
    {
        $query = StockOpname::query()
            ->with(['dicatatOleh', 'detailStockOpnames.barang.satuan']);

        // LOGIKA SELECT ID
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->input('selected_ids'));
            $query->whereIn('id', $ids);
        } else {
            // Filter Biasa
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_opname', [
                    $request->input('start_date') . ' 00:00:00', 
                    $request->input('end_date') . ' 23:59:59'
                ]);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
        }

        $data = $query->orderBy('tanggal_opname', 'desc')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        // 1. EXPORT EXCEL (Format Form ATK)
        if ($request->input('type') === 'excel') {
            return Excel::download(
                new LaporanStockOpnameExport($data), 
                'Laporan_Stock_Opname_' . date('dmY_His') . '.xlsx'
            );
        }

        // 2. EXPORT PDF
        if ($request->input('type') === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan_stock_opname_pdf', [
                'opnames' => $data
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Laporan_Stock_Opname_' . date('dmY_His') . '.pdf');
        }

        return back();
    }
}