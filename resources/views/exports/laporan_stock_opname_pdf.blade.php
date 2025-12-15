<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stock Opname</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .page-break { page-break-after: always; }
        .header { text-align: left; margin-bottom: 20px; }
        .header h1 { font-size: 14px; margin: 0; font-weight: bold; }
        .title-box { text-align: center; border: 1px solid #000; padding: 5px; margin-bottom: 10px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background-color: #f0f0f0; text-align: center; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-red { color: red; font-weight: bold; }

        .signature-section { margin-top: 30px; display: flex; justify-content: space-between; }
        /* CSS Float fallback for PDF library */
        .sig-left { float: left; width: 40%; text-align: center; }
        .sig-right { float: right; width: 40%; text-align: center; }
    </style>
</head>
<body>
    @foreach($opnames as $opname)
        <div class="header">
            <h1>POLITEKNIK NEGERI BANDUNG</h1>
            <div>SUBBAGIAN TATA USAHA - URUSAN ASET DAN RUMAH TANGGA</div>
        </div>

        <div class="title-box">BERITA ACARA HASIL STOCK OPNAME</div>

        <table style="border: none; margin-bottom: 10px;">
            <tr style="border: none;">
                <td style="border: none; width: 100px;">No. Opname</td>
                <td style="border: none;">: {{ $opname->no_opname }}</td>
                <td style="border: none; text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($opname->tanggal_opname)->format('d F Y') }}</td>
            </tr>
             <tr style="border: none;">
                <td style="border: none;">Pencatat</td>
                <td style="border: none;">: {{ $opname->dicatatOleh->name ?? '-' }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Barang</th>
                    <th width="10%">Satuan</th>
                    <th width="10%">Sistem</th>
                    <th width="10%">Fisik</th>
                    <th width="10%">Selisih</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opname->detailStockOpnames as $index => $d)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $d->barang->nama_barang }}</td>
                    <td class="text-center">{{ $d->barang->satuan->nama_satuan ?? '-' }}</td>
                    <td class="text-center">{{ $d->stok_sistem }}</td>
                    <td class="text-center">{{ $d->stok_fisik ?? '-' }}</td>
                    <td class="text-center {{ $d->selisih != 0 ? 'text-red' : '' }}">
                        {{ $d->selisih > 0 ? '+'.$d->selisih : $d->selisih }}
                    </td>
                    <td>{{ $d->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature-section">
            <div class="sig-left">
                <p>Pencatat,</p>
                <br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">{{ $opname->dicatatOleh->name ?? '..................' }}</p>
            </div>
            <div class="sig-right">
                <p>Mengetahui,</p>
                <br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">( .................................. )</p>
            </div>
            <div style="clear: both;"></div>
        </div>

        @if(!$loop->last) <div class="page-break"></div> @endif
    @endforeach
</body>
</html>