<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Rusak</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .page-break { page-break-after: always; }
        .header { text-align: left; margin-bottom: 15px; }
        .header h1 { font-size: 14px; margin: 0; font-weight: bold; }
        .title-box { text-align: center; border: 1px solid #000; padding: 5px; margin-bottom: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background-color: #f0f0f0; text-align: center; }
        .text-center { text-align: center; }
        .signature-section { margin-top: 30px; }
        .sig-box { width: 40%; display: inline-block; text-align: center; vertical-align: top; }
        .sig-spacer { width: 18%; display: inline-block; }
    </style>
</head>
<body>
    @foreach($data as $item)
        <div class="header">
            <h1>POLITEKNIK NEGERI BANDUNG</h1>
            <div>SUBBAGIAN TATA USAHA - URUSAN ASET DAN RUMAH TANGGA</div>
        </div>

        <div class="title-box">BERITA ACARA BARANG RUSAK / USANG</div>

        <table style="border: none; margin-bottom: 10px;">
            <tr style="border: none;">
                <td style="border: none; width: 120px;">No. Dokumen</td>
                <td style="border: none;">: {{ $item->no_catat }}</td>
                <td style="border: none; text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($item->tanggal_catat)->format('d F Y') }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">No. Bukti</td>
                <td style="border: none;">: {{ $item->no_bukti ?? '-' }}</td>
            </tr>
             <tr style="border: none;">
                <td style="border: none;">Keterangan</td>
                <td style="border: none;">: {{ $item->keterangan ?? '-' }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Barang</th>
                    <th width="10%">Jumlah</th>
                    <th width="15%">Satuan</th>
                    <th width="30%">Detail Kerusakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->detail as $index => $d)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $d->barang->nama_barang }}</td>
                    <td class="text-center">{{ $d->jumlah }}</td>
                    <td class="text-center">{{ $d->barang->satuan->nama_satuan ?? '-' }}</td>
                    <td>{{ $d->keterangan_rusak }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature-section">
            <div class="sig-box">
                <p>Pelapor / Pencatat,</p>
                <br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">{{ $item->dicatatOleh->name ?? '..................' }}</p>
            </div>
            <div class="sig-spacer"></div>
            <div class="sig-box">
                <p>Mengetahui,</p>
                <br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">( .................................. )</p>
            </div>
        </div>

        @if(!$loop->last) <div class="page-break"></div> @endif
    @endforeach
</body>
</html>