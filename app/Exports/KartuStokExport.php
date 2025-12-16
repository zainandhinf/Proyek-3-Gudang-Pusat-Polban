<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\DetailMutasiBarang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class KartuStokExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function view(): View
    {
        $year = $this->year;
        
        // 1. Ambil semua barang (tanpa pagination)
        $barangs = Barang::with(['satuan', 'kelompokBarang'])
            ->orderBy('kode_barang')
            ->get();

        // 2. Transform data untuk setiap barang
        $data = $barangs->map(function ($barang) use ($year) {
            
            // --- A. Hitung Saldo Awal (Transaksi sebelum tahun ini) ---
            // Saldo Awal = Total Masuk (Lama) - Total Keluar (Lama)
            
            $masukLama = DetailMutasiBarang::where('barang_id', $barang->id)
                ->whereHas('mutasi', function($q) use ($year) {
                    $q->whereYear('tanggal_mutasi', '<', $year)
                      ->where('jenis_mutasi', 'masuk');
                })->sum('jumlah');
            
            $keluarLama = DetailMutasiBarang::where('barang_id', $barang->id)
                ->whereHas('mutasi', function($q) use ($year) {
                    $q->whereYear('tanggal_mutasi', '<', $year)
                      ->where('jenis_mutasi', 'keluar');
                })->sum('jumlah');

            $saldoAwal = $masukLama - $keluarLama;

            // --- B. Ambil Transaksi Tahun Ini (Group per Bulan) ---
            // Menggunakan EXTRACT(MONTH) agar support PostgreSQL
            $transaksi = DetailMutasiBarang::select(
                    DB::raw('EXTRACT(MONTH FROM mutasi_barangs.tanggal_mutasi) as bulan'),
                    'mutasi_barangs.jenis_mutasi',
                    DB::raw('SUM(detail_mutasi_barangs.jumlah) as total')
                )
                ->join('mutasi_barangs', 'detail_mutasi_barangs.mutasi_barang_id', '=', 'mutasi_barangs.id')
                ->where('detail_mutasi_barangs.barang_id', $barang->id)
                ->whereYear('mutasi_barangs.tanggal_mutasi', $year)
                ->groupBy('bulan', 'jenis_mutasi')
                ->get();

            // --- C. Mapping ke Array 12 Bulan ---
            $mutasi = array_fill(1, 12, 0); // Index 1 s/d 12 (Jan-Des)
            $totalPemakaian = 0;

            foreach ($transaksi as $t) {
                $bulan = (int) $t->bulan; 
                
                if ($t->jenis_mutasi === 'masuk') {
                    $mutasi[$bulan] += $t->total;
                } else {
                    // Jika keluar, kurangi mutasi (untuk saldo) & tambah pemakaian
                    $mutasi[$bulan] -= $t->total; 
                    $totalPemakaian += $t->total;
                }
            }

            // --- D. Hitung Saldo Akhir ---
            // Saldo Awal + Total Pergerakan Tahun Ini
            $saldoAkhir = $saldoAwal + array_sum($mutasi);

            return (object) [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'satuan'      => $barang->satuan->nama_satuan ?? '-',
                'saldo_awal'  => $saldoAwal,
                'mutasi'      => $mutasi, // Array [1=>val, 2=>val...]
                'total_pemakaian' => $totalPemakaian,
                'saldo_akhir' => $saldoAkhir,
            ];
        });

        return view('exports.laporan_kartu_stok_excel', [
            'data' => $data,
            'year' => $year
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Judul Besar Bold & Center
            1 => ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => 'center']],
            // Header Tabel Bold & Center (Baris 3 & 4 karena ada rowspan)
            3 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
            4 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
        ];
    }
}