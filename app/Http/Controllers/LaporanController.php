<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use App\Exports\LaporanMutasiExport;
use App\Models\Permintaan;
use App\Exports\LaporanPermintaanExport;
use App\Models\StockOpname;
use App\Exports\LaporanStockOpnameExport;
use App\Models\BarangUsang;
use App\Exports\LaporanBarangUsangExport;
use App\Models\Barang;
use App\Models\KelompokBarang;
use App\Exports\LaporanDataBarangExport;
use App\Exports\KartuStokExport;
use App\Models\DetailBarangUsang;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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




    public function barangUsang(Request $request)
    {
        $query = BarangUsang::query()
            ->with(['dicatatOleh', 'detail.barang']); // Load detail

        // 1. Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_catat', [
                $request->input('start_date'), 
                $request->input('end_date')
            ]);
        }

        // 2. Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_catat', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('no_bukti', 'like', '%' . $request->input('search') . '%');
            });
        }

        $data = $query->orderBy('tanggal_catat', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        return Inertia::render('Laporan/barangusang', [
            'laporan' => $data->through(fn($item) => [
                'id' => $item->id,
                'tanggal' => Carbon::parse($item->tanggal_catat)->format('d-m-Y'),
                'no_catat' => $item->no_catat,
                'no_bukti' => $item->no_bukti ?? '-',
                'pencatat' => $item->dicatatOleh->name ?? '-',
                'keterangan' => $item->keterangan,
                'total_item' => $item->detail->count(),
                'ringkasan' => $item->detail->take(2)->map(fn($d) => $d->barang->nama_barang)->join(', ') . ($item->detail->count() > 2 ? '...' : ''),
            ]),
            'filters' => $request->only(['start_date', 'end_date', 'search']),
        ]);
    }

    public function exportBarangUsang(Request $request)
    {
        $query = BarangUsang::query()
            ->with(['dicatatOleh', 'detail.barang.satuan']);

        // LOGIKA SELECT ID
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->input('selected_ids'));
            $query->whereIn('id', $ids);
        } else {
            // Filter Biasa
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_catat', [
                    $request->input('start_date'), 
                    $request->input('end_date')
                ]);
            }
            if ($request->filled('search')) {
                $query->where('no_catat', 'like', '%' . $request->input('search') . '%');
            }
        }

        $data = $query->orderBy('tanggal_catat', 'desc')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        // 1. EXPORT EXCEL
        if ($request->input('type') === 'excel') {
            return Excel::download(
                new LaporanBarangUsangExport($data), 
                'Laporan_Barang_Rusak_' . date('dmY_His') . '.xlsx'
            );
        }

        // 2. EXPORT PDF
        if ($request->input('type') === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan_barang_usang_pdf', [
                'data' => $data
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Laporan_Barang_Rusak_' . date('dmY_His') . '.pdf');
        }

        return back();
    }




    public function dataBarang(Request $request)
    {
        $query = Barang::query()->with(['satuan', 'kelompokBarang']);

        // 1. Filter Kelompok Barang
        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_barang_id', $request->input('kelompok_id'));
        }

        // 2. Search (Nama / Kode)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->input('search') . '%');
            });
        }

        $barangs = $query->orderBy('nama_barang', 'asc')
                         ->paginate(20)
                         ->withQueryString();

        return Inertia::render('Laporan/databarang', [
            'barangs' => $barangs,
            'kelompok_barangs' => KelompokBarang::all(), // Untuk dropdown filter
            'filters' => $request->only(['search', 'kelompok_id']),
        ]);
    }

    public function exportDataBarang(Request $request)
    {
        $query = Barang::query()->with(['satuan', 'kelompokBarang']);

        // LOGIKA SELECT ID (Prioritas)
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->input('selected_ids'));
            $query->whereIn('id', $ids);
        } else {
            // Filter Biasa
            if ($request->filled('kelompok_id')) {
                $query->where('kelompok_barang_id', $request->input('kelompok_id'));
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('nama_barang', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('kode_barang', 'like', '%' . $request->input('search') . '%');
                });
            }
        }

        $data = $query->orderBy('kode_barang', 'asc')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        // 1. EXPORT EXCEL
        if ($request->input('type') === 'excel') {
            return Excel::download(
                new LaporanDataBarangExport($data), 
                'Laporan_Data_Barang_' . date('dmY_His') . '.xlsx'
            );
        }

        // 2. EXPORT PDF
        if ($request->input('type') === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan_data_barang_pdf', [
                'barangs' => $data,
                'tanggal_cetak' => Carbon::now()->format('d F Y')
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Laporan_Data_Barang_' . date('dmY_His') . '.pdf');
        }

        return back();
    }



    
    // public function kartuStok(Request $request)
    // {
    //     $year = $request->input('year', date('Y'));
    //     $search = $request->input('search');

    //     // 1. Ambil Data Master Barang
    //     $query = Barang::query()->with(['satuan']);

    //     if ($search) {
    //         $query->where('nama_barang', 'like', "%{$search}%")
    //               ->orWhere('kode_barang', 'like', "%{$search}%");
    //     }

    //     // Paginasi agar performa tetap ringan meski data banyak
    //     $barangs = $query->orderBy('nama_barang')->paginate(15)->withQueryString();

    //     // 2. Logika Automatisasi (Transformasi Data)
    //     // Kita hitung pergerakan stok untuk setiap barang di halaman ini
    //     $kartuStok = $barangs->through(function ($barang) use ($year) {
            
    //         // A. Hitung Saldo Awal (Akumulasi transaksi sebelum tahun ini)
    //         $saldoAwal = DB::table('transaksi_barangs')
    //             ->where('barang_id', $barang->id)
    //             ->where('tanggal', '<', $year . '-01-01')
    //             ->selectRaw("SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE -jumlah END) as total")
    //             ->value('total') ?? 0;

    //         // B. Ambil Transaksi Tahun Ini (Group per Bulan)
    //         // Menggunakan query agregasi agar cepat
    //         $transaksiTahunIni = DB::table('transaksi_barangs')
    //             ->where('barang_id', $barang->id)
    //             ->whereYear('tanggal', $year)
    //             ->select(
    //                 // Syntax ini kompatibel dengan MySQL & PostgreSQL
    //                 DB::raw('EXTRACT(MONTH FROM tanggal) as bulan'),
    //                 'jenis',
    //                 DB::raw('SUM(jumlah) as total')
    //             )
    //             ->groupBy('bulan', 'jenis')
    //             ->get();

    //         // C. Siapkan Array Kosong untuk 12 Bulan
    //         $mutasi = array_fill(1, 12, 0); // Index 1=Jan, 12=Des
    //         $totalMutasiTahunIni = 0;

    //         // D. Isi Array dengan Data Transaksi
    //         foreach ($transaksiTahunIni as $t) {
    //             $bulan = (int)$t->bulan;
    //             if ($t->jenis === 'masuk') {
    //                 $mutasi[$bulan] += $t->total; // Tambah
    //                 $totalMutasiTahunIni += $t->total;
    //             } else {
    //                 $mutasi[$bulan] -= $t->total; // Kurang
    //                 $totalMutasiTahunIni -= $t->total;
    //             }
    //         }

    //         // E. Hitung Saldo Akhir
    //         $saldoAkhir = $saldoAwal + $totalMutasiTahunIni;

    //         return [
    //             'id' => $barang->id,
    //             'kode_barang' => $barang->kode_barang,
    //             'nama_barang' => $barang->nama_barang,
    //             'satuan' => $barang->satuan->nama_satuan ?? '-',
    //             'saldo_awal' => $saldoAwal,
    //             'mutasi' => $mutasi, // Array [1=>0, 2=>5, 3=>-2, ...]
    //             'saldo_akhir' => $saldoAkhir,
    //             'keterangan' => '' // Bisa diisi logic tambahan jika perlu
    //         ];
    //     });

    //     return Inertia::render('Laporan/KartuStok/index', [
    //         'kartuStok' => $kartuStok,
    //         'filters' => ['search' => $search, 'year' => $year],
    //         'years' => range(date('Y'), 2020), // Dropdown tahun dari sekarang sampai 2020
    //     ]);
    // }

    // public function kartuStok(Request $request)
    // {
    //     $year = $request->input('year', date('Y'));
    //     $search = $request->input('search');

    //     // 1. Ambil Barang
    //     $query = Barang::query()->with(['satuan', 'kelompokBarang']);

    //     if ($search) {
    //         $query->where('nama_barang', 'like', "%{$search}%")
    //               ->orWhere('kode_barang', 'like', "%{$search}%");
    //     }

    //     $barangs = $query->orderBy('kode_barang')->paginate(20)->withQueryString();

    //     // 2. Transformasi Data (Hitung Mutasi Per Bulan)
    //     $kartuStok = $barangs->through(function ($barang) use ($year) {
            
    //         // A. Hitung Saldo Awal (Total transaksi SEBELUM tahun ini)
    //         // Logic: Total Masuk - Total Keluar di tahun-tahun sebelumnya
    //         $saldoAwalMasuk = DetailMutasiBarang::where('barang_id', $barang->id)
    //             ->whereHas('mutasi', fn($q) => $q->whereYear('tanggal_mutasi', '<', $year)->where('jenis_mutasi', 'masuk'))
    //             ->sum('jumlah');
            
    //         $saldoAwalKeluar = DetailMutasiBarang::where('barang_id', $barang->id)
    //             ->whereHas('mutasi', fn($q) => $q->whereYear('tanggal_mutasi', '<', $year)->where('jenis_mutasi', 'keluar'))
    //             ->sum('jumlah');

    //         // Tambahkan juga logika Barang Usang (Keluar) jika perlu
    //         // ...

    //         $saldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

    //         // B. Ambil Transaksi Per Bulan di Tahun Terpilih
    //         $transaksi = DetailMutasiBarang::select(
    //                 // PERBAIKAN DI SINI: Gunakan EXTRACT untuk Postgres
    //                 DB::raw('EXTRACT(MONTH FROM mutasi_barangs.tanggal_mutasi) as bulan'),
    //                 'mutasi_barangs.jenis_mutasi',
    //                 DB::raw('SUM(detail_mutasi_barangs.jumlah) as total')
    //             )
    //             ->join('mutasi_barangs', 'detail_mutasi_barangs.mutasi_barang_id', '=', 'mutasi_barangs.id')
    //             ->where('detail_mutasi_barangs.barang_id', $barang->id)
    //             ->whereYear('mutasi_barangs.tanggal_mutasi', $year)
    //             ->groupBy('bulan', 'jenis_mutasi')
    //             ->get();

    //         // C. Mapping ke Array 12 Bulan
    //         $mutasiBulanan = array_fill(1, 12, 0);
            
    //         foreach ($transaksi as $t) {
    //             // Pastikan bulan dicasting ke integer agar bisa jadi index array
    //             $bulan = (int) $t->bulan; 

    //             if ($t->jenis_mutasi === 'masuk') {
    //                 $mutasiBulanan[$bulan] += $t->total;
    //             } else {
    //                 $mutasiBulanan[$bulan] -= $t->total;
    //             }
    //         }

    //         // D. Hitung Total Pemakaian (Total Barang Keluar Tahun Ini)
    //         $totalPemakaian = $transaksi->where('jenis_mutasi', 'keluar')->sum('total');

    //         // E. Saldo Akhir (Berdasarkan perhitungan, bukan stok saat ini di master)
    //         // Agar ketahuan kalau ada selisih
    //         $saldoBerjalan = $saldoAwal + array_sum($mutasiBulanan);

    //         return [
    //             'id' => $barang->id,
    //             'kode_barang' => $barang->kode_barang,
    //             'nama_barang' => $barang->nama_barang,
    //             'satuan' => $barang->satuan->nama_satuan ?? '-',
    //             'saldo_awal' => $saldoAwal,
    //             'mutasi' => $mutasiBulanan, // Array index 1-12
    //             'saldo_akhir' => $saldoBerjalan, 
    //             'total_pemakaian' => $totalPemakaian,
    //             'real_stock' => $barang->stok_saat_ini // Untuk pembanding
    //         ];
    //     });

    //     return Inertia::render('Laporan/KartuStok/index', [
    //         'kartuStok' => $kartuStok,
    //         'filters' => ['search' => $search, 'year' => $year],
    //         'years' => range(date('Y'), date('Y') - 5), // Dropdown tahun
    //     ]);
    // }

    public function kartuStok(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $search = $request->input('search');

        // 1. Ambil Barang
        $query = Barang::query()->with(['satuan', 'kelompokBarang']);

        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
        }

        $barangs = $query->orderBy('kode_barang')->paginate(20)->withQueryString();

        // 2. Transformasi Data (METODE MUNDUR / BACKWARDS CALCULATION)
        $kartuStok = $barangs->through(function ($barang) use ($year) {
            
            // --- A. Hitung Pergerakan Stok "Masa Depan" (Setelah Tahun Ini) ---
            // Tujuannya: Mengembalikan stok saat ini ke posisi Akhir Tahun yang dipilih
            
            // 1. Mutasi Masuk (Masa Depan) -> Harus DIKURANGI dari stok saat ini
            $futureMasuk = DetailMutasiBarang::where('barang_id', $barang->id)
                ->whereHas('mutasi', fn($q) => $q->whereYear('tanggal_mutasi', '>', $year))
                ->sum('jumlah');

            // 2. Mutasi Keluar (Masa Depan) -> Harus DITAMBAH ke stok saat ini
            $futureKeluar = DetailMutasiBarang::where('barang_id', $barang->id)
                ->whereHas('mutasi', fn($q) => $q->whereYear('tanggal_mutasi', '>', $year)->where('jenis_mutasi', 'keluar'))
                ->sum('jumlah');

            // 3. Barang Usang (Masa Depan) -> Harus DITAMBAH ke stok saat ini
            $futureRusak = DetailBarangUsang::where('barang_id', $barang->id)
                ->whereHas('barangUsang', fn($q) => $q->whereYear('tanggal_catat', '>', $year))
                ->sum('jumlah');

            // Hitung Saldo Akhir Tahun Terpilih
            // Rumus: Stok Sekarang - Masuk Future + Keluar Future + Rusak Future
            $saldoAkhirTahun = $barang->stok_saat_ini - $futureMasuk + $futureKeluar + $futureRusak;


            // --- B. Hitung Transaksi Selama Tahun Terpilih (Per Bulan) ---
            
            // 1. Mutasi (Masuk & Keluar)
            $transaksiMutasi = DetailMutasiBarang::select(
                    DB::raw('EXTRACT(MONTH FROM mutasi_barangs.tanggal_mutasi) as bulan'),
                    'mutasi_barangs.jenis_mutasi',
                    DB::raw('SUM(detail_mutasi_barangs.jumlah) as total')
                )
                ->join('mutasi_barangs', 'detail_mutasi_barangs.mutasi_barang_id', '=', 'mutasi_barangs.id')
                ->where('detail_mutasi_barangs.barang_id', $barang->id)
                ->whereYear('mutasi_barangs.tanggal_mutasi', $year)
                ->groupBy('bulan', 'jenis_mutasi')
                ->get();

            // 2. Barang Usang (Selalu Keluar)
            $transaksiRusak = DetailBarangUsang::select(
                    DB::raw('EXTRACT(MONTH FROM barang_usangs.tanggal_catat) as bulan'),
                    DB::raw('SUM(detail_barang_usangs.jumlah) as total')
                )
                ->join('barang_usangs', 'detail_barang_usangs.barang_usang_id', '=', 'barang_usangs.id')
                ->where('detail_barang_usangs.barang_id', $barang->id)
                ->whereYear('barang_usangs.tanggal_catat', $year)
                ->groupBy('bulan')
                ->get();

            // --- C. Mapping ke Array 12 Bulan ---
            $mutasiBulanan = array_fill(1, 12, 0);
            $totalMasukTahunIni = 0;
            $totalKeluarTahunIni = 0; // Termasuk pemakaian & rusak

            // Proses Mutasi
            foreach ($transaksiMutasi as $t) {
                $bulan = (int) $t->bulan; 
                if ($t->jenis_mutasi === 'masuk') {
                    $mutasiBulanan[$bulan] += $t->total;
                    $totalMasukTahunIni += $t->total;
                } else {
                    $mutasiBulanan[$bulan] -= $t->total;
                    $totalKeluarTahunIni += $t->total;
                }
            }

            // Proses Barang Rusak (Dianggap Keluar)
            foreach ($transaksiRusak as $r) {
                $bulan = (int) $r->bulan;
                $mutasiBulanan[$bulan] -= $r->total; // Kurangi stok
                $totalKeluarTahunIni += $r->total;
            }

            // --- D. Hitung Saldo Awal Tahun ---
            // Rumus: Saldo Akhir - Total Masuk + Total Keluar
            $saldoAwal = $saldoAkhirTahun - $totalMasukTahunIni + $totalKeluarTahunIni;

            return [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'satuan' => $barang->satuan->nama_satuan ?? '-',
                'saldo_awal' => $saldoAwal, // Sekarang ini hasil kalkulasi mundur
                'mutasi' => $mutasiBulanan, 
                'saldo_akhir' => $saldoAkhirTahun, 
                'total_pemakaian' => $totalKeluarTahunIni,
                'real_stock' => $barang->stok_saat_ini // Sekedar info debug
            ];
        });

        return Inertia::render('Laporan/KartuStok/index', [
            'kartuStok' => $kartuStok,
            'filters' => ['search' => $search, 'year' => $year],
            'years' => range(date('Y'), date('Y') - 5),
        ]);
    }

    public function exportKartuStok(Request $request)
    {
        // Logika export mirip dengan LaporanDataBarangExport
        // Panggil class export baru
        return Excel::download(new KartuStokExport($request->year), 'Kartu_Stok_'.$request->year.'.xlsx');
    }
}