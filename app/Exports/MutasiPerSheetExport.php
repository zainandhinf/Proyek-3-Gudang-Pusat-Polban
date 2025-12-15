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

class MutasiPerSheetExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $mutasi;

    public function __construct($mutasi)
    {
        $this->mutasi = $mutasi;
    }

    // Mengatur Nama Tab Sheet (Contoh: MB-2025-001)
    public function title(): string
    {
        // Ganti karakter ilegal excel dengan '-' dan batasi panjang 30 karakter
        return substr(str_replace(['/', '\\', '*', '?', ':', '[', ']'], '-', $this->mutasi->nomor_mutasi), 0, 30);
    }

    public function view(): View
    {
        // Pastikan nama file view blade sesuai dengan yang Anda buat
        return view('exports.laporan_mutasi_excel', [
            'mutasi' => $this->mutasi,
            'details' => $this->mutasi->detail, 
            'tanggal_cetak' => date('d F Y')
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Judul Politeknik (Baris 1) - Center
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            // Judul Bukti (Baris 2) - Center
            2 => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']],
            
            // Header Tabel (Baris ke-9) - Bold, Background Abu, Border
            9 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EAEAEA']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}