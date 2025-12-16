<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Permintaan;
use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // --- FIX ROLE DETECTION ---
        // Konversi Enum ke string lowercase agar aman
        $role = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;
        $role = strtolower((string)$role); 

        // ======================================================
        // LOGIKA UNTUK OPERATOR & ADMIN & APPROVAL
        // ======================================================
        if (in_array($role, ['operator', 'admin', 'approval'])) {
            
            // 1. STATISTIK UTAMA
            $data['stats_operator'] = [
                'total_barang'       => Barang::count(),
                'permintaan_pending' => Permintaan::where('status', 'Pending')->count(),
                'barang_keluar'      => (int) DetailMutasiBarang::whereHas('mutasi', function($q) {
                                            $q->where('jenis_mutasi', 'keluar')
                                              ->whereMonth('tanggal_mutasi', Carbon::now()->month)
                                              ->whereYear('tanggal_mutasi', Carbon::now()->year);
                                        })->sum('jumlah'), 
                'stok_menipis'       => Barang::where('stok_saat_ini', '<=', 5)->count(),
            ];

            // 2. PERMINTAAN TERBARU (Pastikan toArray untuk hindari masalah serialisasi)
            $data['permintaan_terbaru'] = Permintaan::with('pemohon')
                ->where('status', 'Pending')
                ->latest()
                ->limit(5)
                ->get()
                ->toArray();

            // 3. AKTIVITAS TERBARU
            $lastMutasi = MutasiBarang::with('detail.barang')
                ->latest('tanggal_mutasi')
                ->limit(5) // Ambil agak banyak biar aman
                ->get()
                ->map(function($m) {
                    $tipe = $m->jenis_mutasi == 'masuk' ? 'masuk' : 'warning';
                    $firstDetail = $m->detail->first();
                    $barangName = $firstDetail?->barang?->nama_barang ?? 'Item';
                    $count = $m->detail->count();
                    
                    $desc = $m->jenis_mutasi == 'masuk' 
                        ? "Masuk: $barangName " . ($count > 1 ? "+ ".($count-1)." lainnya" : "")
                        : "Keluar: $barangName " . ($count > 1 ? "+ ".($count-1)." lainnya" : "");
                    
                    return [
                        'judul' => $m->jenis_mutasi == 'masuk' ? 'Barang Masuk' : 'Barang Keluar',
                        'desc'  => $desc,
                        'waktu' => Carbon::parse($m->created_at)->diffForHumans(),
                        'tipe'  => $tipe,
                        'timestamp' => $m->created_at
                    ];
                });

            $lastPermintaan = Permintaan::with('pemohon')
                ->latest('created_at')
                ->limit(5)
                ->get()
                ->map(function($p) {
                    $nama = $p->pemohon->name ?? 'User';
                    
                    // --- PERBAIKAN DI SINI ---
                    // Cek apakah jenis_keperluan adalah Enum, jika ya ambil ->value
                    $jenis = $p->jenis_keperluan instanceof \BackedEnum 
                        ? $p->jenis_keperluan->value 
                        : $p->jenis_keperluan;

                    return [
                        'judul' => 'Permintaan Baru',
                        'desc'  => "Dari: $nama ($jenis)", // Gunakan variabel $jenis yang sudah berupa string
                        'waktu' => Carbon::parse($p->created_at)->diffForHumans(),
                        'tipe'  => 'approve', 
                        'timestamp' => $p->created_at
                    ];
                });

            // Merge & Sort
            $data['aktivitas'] = $lastMutasi->merge($lastPermintaan)
                ->sortByDesc('timestamp')
                ->take(5)
                ->values()
                ->toArray();

            // 4. DATA GRAFIK (REAL TIME TAHUN INI)
            // Fix Query Postgres & MySQL Compatibility
            $chartMasukRaw = DetailMutasiBarang::selectRaw('EXTRACT(MONTH FROM mutasi_barangs.tanggal_mutasi) as bulan, SUM(detail_mutasi_barangs.jumlah) as total')
                ->join('mutasi_barangs', 'detail_mutasi_barangs.mutasi_barang_id', '=', 'mutasi_barangs.id')
                ->where('mutasi_barangs.jenis_mutasi', 'masuk')
                ->whereYear('mutasi_barangs.tanggal_mutasi', Carbon::now()->year)
                ->groupBy('bulan')
                ->get();

            $chartKeluarRaw = DetailMutasiBarang::selectRaw('EXTRACT(MONTH FROM mutasi_barangs.tanggal_mutasi) as bulan, SUM(detail_mutasi_barangs.jumlah) as total')
                ->join('mutasi_barangs', 'detail_mutasi_barangs.mutasi_barang_id', '=', 'mutasi_barangs.id')
                ->where('mutasi_barangs.jenis_mutasi', 'keluar')
                ->whereYear('mutasi_barangs.tanggal_mutasi', Carbon::now()->year)
                ->groupBy('bulan')
                ->get();

            // Mapping manual agar index bulan aman
            $mapMasuk = [];
            foreach($chartMasukRaw as $item) $mapMasuk[(int)$item->bulan] = (int)$item->total;

            $mapKeluar = [];
            foreach($chartKeluarRaw as $item) $mapKeluar[(int)$item->bulan] = (int)$item->total;

            $dataMasuk = [];
            $dataKeluar = [];
            for ($i = 1; $i <= 12; $i++) {
                $dataMasuk[] = $mapMasuk[$i] ?? 0;
                $dataKeluar[] = $mapKeluar[$i] ?? 0;
            }

            $data['chart_data'] = [
                'masuk' => $dataMasuk,
                'keluar' => $dataKeluar
            ];
        } 
        
        // ======================================================
        // LOGIKA UNTUK PEMOHON
        // ======================================================
        else {
            $data['barangs'] = Barang::with(['satuan', 'kelompokBarang'])
                ->where('stok_saat_ini', '>', 0)
                ->latest()
                ->limit(10)
                ->get();

            $data['riwayats'] = Permintaan::where('pemohon_user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            $data['stats_pemohon'] = [
                'total'     => Permintaan::where('pemohon_user_id', $user->id)->count(),
                'proses'    => Permintaan::where('pemohon_user_id', $user->id)->whereIn('status', ['Pending', 'Processed'])->count(),
                'disetujui' => Permintaan::where('pemohon_user_id', $user->id)->where('status', 'Approved')->count(),
            ];
        }

        return Inertia::render('Dashboard', $data);
    }
}