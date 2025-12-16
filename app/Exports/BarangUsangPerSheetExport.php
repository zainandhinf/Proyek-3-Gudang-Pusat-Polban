<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BarangUsangPerSheetExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    // Mengatur Nama Tab Sheet (Contoh: BR-20251216...)
    public function title(): string
    {
        // Bersihkan karakter ilegal excel dan potong jadi 30 char
        $cleanTitle = str_replace(['/', '\\', '*', '?', ':', '[', ']'], '-', $this->item->no_catat);
        return substr($cleanTitle, 0, 30);
    }

    public function view(): View
    {
        return view('exports.laporan_barang_usang_excel', [
            'item' => $this->item // Kirim object tunggal
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Judul Politeknik (Baris 1) - Center & Bold
            1 => [
                'font' => ['bold' => true, 'size' => 14], 
                'alignment' => ['horizontal' => 'center']
            ],
            // Sub Judul (Baris 2) - Center & Bold
            2 => [
                'font' => ['bold' => true, 'size' => 12], 
                'alignment' => ['horizontal' => 'center']
            ],
            // Judul Berita Acara (Baris 4) - Center & Border Box
            4 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center'],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ],
            
            // Header Tabel (Baris ke-11) 
            // Sesuaikan dengan posisi <thead> di file Blade
            11 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EAEAEA']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}