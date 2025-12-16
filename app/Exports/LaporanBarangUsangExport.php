<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class LaporanBarangUsangExport implements WithMultipleSheets
{
    use Exportable;

    protected $laporan;

    // Constructor menerima Collection data Header Barang Usang
    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->laporan as $item) {
            // Panggil class per-sheet untuk setiap dokumen laporan
            $sheets[] = new BarangUsangPerSheetExport($item);
        }

        return $sheets;
    }
}