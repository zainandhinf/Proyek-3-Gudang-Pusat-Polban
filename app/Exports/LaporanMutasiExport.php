<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class LaporanMutasiExport implements WithMultipleSheets
{
    use Exportable;

    protected $mutasis;

    // Constructor menerima Collection data Header Mutasi
    public function __construct($mutasis)
    {
        $this->mutasis = $mutasis;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->mutasis as $mutasi) {
            // Untuk setiap header mutasi, panggil class Sheet yang spesifik
            $sheets[] = new MutasiPerSheetExport($mutasi);
        }

        return $sheets;
    }
}