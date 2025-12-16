<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function kartuStok(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $search = $request->input('search');

        // 1. Ambil Data Master Barang
        $query = Barang::query()->with(['satuan']);

        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
        }

        // Paginasi agar performa tetap ringan meski data banyak
        $barangs = $query->orderBy('nama_barang')->paginate(15)->withQueryString();

        // 2. Logika Automatisasi (Transformasi Data)
        // Kita hitung pergerakan stok untuk setiap barang di halaman ini
        $kartuStok = $barangs->through(function ($barang) use ($year) {
            
            // A. Hitung Saldo Awal (Akumulasi transaksi sebelum tahun ini)
            $saldoAwal = DB::table('transaksi_barangs')
                ->where('barang_id', $barang->id)
                ->where('tanggal', '<', $year . '-01-01')
                ->selectRaw("SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE -jumlah END) as total")
                ->value('total') ?? 0;

            // B. Ambil Transaksi Tahun Ini (Group per Bulan)
            // Menggunakan query agregasi agar cepat
            $transaksiTahunIni = DB::table('transaksi_barangs')
                ->where('barang_id', $barang->id)
                ->whereYear('tanggal', $year)
                ->select(
                    // Syntax ini kompatibel dengan MySQL & PostgreSQL
                    DB::raw('EXTRACT(MONTH FROM tanggal) as bulan'),
                    'jenis',
                    DB::raw('SUM(jumlah) as total')
                )
                ->groupBy('bulan', 'jenis')
                ->get();

            // C. Siapkan Array Kosong untuk 12 Bulan
            $mutasi = array_fill(1, 12, 0); // Index 1=Jan, 12=Des
            $totalMutasiTahunIni = 0;

            // D. Isi Array dengan Data Transaksi
            foreach ($transaksiTahunIni as $t) {
                $bulan = (int)$t->bulan;
                if ($t->jenis === 'masuk') {
                    $mutasi[$bulan] += $t->total; // Tambah
                    $totalMutasiTahunIni += $t->total;
                } else {
                    $mutasi[$bulan] -= $t->total; // Kurang
                    $totalMutasiTahunIni -= $t->total;
                }
            }

            // E. Hitung Saldo Akhir
            $saldoAkhir = $saldoAwal + $totalMutasiTahunIni;

            return [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'satuan' => $barang->satuan->nama_satuan ?? '-',
                'saldo_awal' => $saldoAwal,
                'mutasi' => $mutasi, // Array [1=>0, 2=>5, 3=>-2, ...]
                'saldo_akhir' => $saldoAkhir,
                'keterangan' => '' // Bisa diisi logic tambahan jika perlu
            ];
        });

        return Inertia::render('Laporan/KartuStok/index', [
            'kartuStok' => $kartuStok,
            'filters' => ['search' => $search, 'year' => $year],
            'years' => range(date('Y'), 2020), // Dropdown tahun dari sekarang sampai 2020
        ]);
    }
}