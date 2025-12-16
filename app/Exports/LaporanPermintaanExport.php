<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPermintaanExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $permintaans;

    public function __construct($permintaans)
    {
        $this->permintaans = $permintaans;
    }

    public function view(): View
    {
        return view('exports.laporan_permintaan_excel', [
            'permintaans' => $this->permintaans
        ]);
    }

    // Styling opsional untuk merapikan excel
    public function styles(Worksheet $sheet)
    {
        return [
            // Style default font
             1 => ['font' => ['family' => 'Arial']],
        ];
    }
}