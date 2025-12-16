<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanDataBarangExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $barangs;

    public function __construct($barangs)
    {
        $this->barangs = $barangs;
    }

    public function view(): View
    {
        return view('exports.laporan_data_barang_excel', [
            'barangs' => $this->barangs
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
             1 => ['font' => ['bold' => true, 'size' => 14]], // Judul Besar
             2 => ['font' => ['bold' => true]], // Sub Judul
             4 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'EEEEEE']]], // Header Tabel
        ];
    }
}