<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Permintaan;
use App\Models\MutasiBarang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // --- LOGIKA UNTUK OPERATOR ---
        if ($user->role->value === 'operator') {
            $data['stats_operator'] = [
                'total_barang' => Barang::count(),
                'permintaan_pending' => Permintaan::where('status', 'Pending')->count(),
                // Contoh logika barang keluar bulan ini
                'barang_keluar' => 128, // Anda bisa ganti dengan query real dari MutasiBarang
                'stok_menipis' => Barang::where('stok_saat_ini', '<=', 5)->count(),
            ];

            $data['permintaan_terbaru'] = Permintaan::with('pemohon')
                ->where('status', 'Pending')
                ->latest()
                ->limit(5)
                ->get();

            // Mockup aktivitas (bisa diambil dari log mutasi)
            $data['aktivitas'] = [
                ['judul' => 'Barang Masuk', 'desc' => 'Terima 50 Rim Kertas A4', 'waktu' => '10 menit lalu', 'tipe' => 'masuk'],
                ['judul' => 'Permintaan Disetujui', 'desc' => 'ATK Jurusan T. Komputer', 'waktu' => '1 jam lalu', 'tipe' => 'approve'],
                ['judul' => 'Peringatan Stok', 'desc' => 'Tinta Hitam sisa 2 pcs', 'waktu' => '3 jam lalu', 'tipe' => 'warning'],
            ];
        } 
        
        // --- LOGIKA UNTUK PEMOHON ---
        else {
            $data['barangs'] = Barang::with(['satuan', 'kategori'])
                ->latest()
                ->limit(8)
                ->get();

            $data['riwayats'] = Permintaan::where('pemohon_user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            $data['stats_pemohon'] = [
                'total' => Permintaan::where('pemohon_user_id', $user->id)->count(),
                'proses' => Permintaan::where('pemohon_user_id', $user->id)->whereIn('status', ['Pending', 'Processed'])->count(),
                'disetujui' => Permintaan::where('pemohon_user_id', $user->id)->where('status', 'Approved')->count(),
            ];
        }

        return Inertia::render('Dashboard', $data);
    }
}