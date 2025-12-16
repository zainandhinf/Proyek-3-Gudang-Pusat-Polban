<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class LaporanStockOpnameExport implements WithMultipleSheets
{
    use Exportable;

    protected $opnames;

    // Constructor menerima Collection data Header Stock Opname
    public function __construct($opnames)
    {
        $this->opnames = $opnames;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->opnames as $opname) {
            // Panggil class per-sheet untuk setiap data opname
            $sheets[] = new StockOpnamePerSheetExport($opname);
        }

        return $sheets;
    }
}