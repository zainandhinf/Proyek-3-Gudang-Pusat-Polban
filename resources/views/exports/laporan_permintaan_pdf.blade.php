<!DOCTYPE html>
<html>
<head>
    <title>Form Permintaan Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
        
        /* Layout Halaman */
        .page-break { page-break-after: always; }
        .container { width: 100%; padding: 10px; }

        /* Header */
        .header { text-align: left; margin-bottom: 20px; }
        .header h1 { font-size: 14px; margin: 0; font-weight: bold; text-transform: uppercase; }
        .header h2 { font-size: 12px; margin: 0; font-weight: bold; text-transform: uppercase; }
        
        /* Judul Form */
        .title-box { text-align: center; border: 1px solid #000; padding: 5px; margin-bottom: 5px; }
        .title-box h3 { margin: 0; font-size: 14px; font-weight: bold; }
        .doc-number { text-align: right; font-size: 9px; margin-bottom: 20px; }

        /* Info Section */
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 2px; vertical-align: top; }
        .label { width: 120px; font-weight: bold; }

        /* Tabel Barang */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 5px; }
        .items-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .col-no { width: 30px; text-align: center; }
        .col-qty { width: 60px; text-align: center; }
        .col-unit { width: 80px; text-align: center; }

        /* Tanda Tangan */
        .signature-section { margin-top: 30px; width: 100%; }
        .signature-box { width: 40%; float: left; text-align: center; }
        .signature-box-right { width: 40%; float: right; text-align: center; }
        .sign-space { height: 70px; }
        .name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    @foreach($permintaans as $p)
    <div class="container">
        
        <div class="header">
            <h1>POLITEKNIK NEGERI BANDUNG</h1>
            <h2>SUBBAGIAN TATA USAHA - URUSAN ASET DAN RUMAH TANGGA</h2>
        </div>

        <div class="title-box">
            <h3>FORMULIR PERMINTAAN KEBUTUHAN ATK/ BARANG/ALAT KEBERSIHAN</h3>
        </div>
        <div class="doc-number">NO. DOKUMEN: PL1.R2.8.IK.02.01.FKBA</div>

        <table class="info-table">
            <tr>
                <td class="label">Nomor</td>
                <td>: {{ $p->no_permintaan }}</td>
                <td style="text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Kepada Yth</td>
                <td colspan="2">: Kepala Bagian Keuangan dan Umum</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">  Politeknik Negeri Bandung</td>
            </tr>
            <tr>
                <td class="label">Keperluan (Unit)</td>
                <td colspan="2">: {{ $p->jenis_keperluan }} - {{ $p->pemohon->name }}</td>
            </tr>
        </table>

        <p>Dengan ini kami mengajukan ATK/ Barang kebersihan untuk keperluan tersebut di atas, adapun rinciannya sebagai berikut:</p>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">NO.</th>
                    <th>NAMA BARANG</th>
                    <th>SPESIFIKASI</th>
                    <th class="col-unit">SATUAN</th>
                    <th class="col-qty">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($p->detailPermintaans as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang }}</td>
                    <td>{{ $detail->barang->spesifikasi ?? '-' }}</td>
                    <td style="text-align: center;">{{ $detail->barang->satuan->nama_satuan ?? 'Pcs' }}</td>
                    <td style="text-align: center;">{{ $detail->jumlah_diminta }}</td>
                </tr>
                @endforeach
                
                {{-- Isi baris kosong agar tabel tidak terlalu pendek --}}
                @for($i = 0; $i < (8 - $p->detailPermintaans->count()); $i++)
                <tr>
                    <td style="color: white">.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                <p>Pemohon,<br>{{ $p->jenis_keperluan }}</p>
                <div class="sign-space"></div>
                <p class="name">{{ $p->pemohon->name }}</p>
                <p>NIP. ...........................</p>
            </div>

            <div class="signature-box-right">
                <p>Mengetahui/Menyetujui,<br>PPK/ Koordinator Unit</p>
                <div class="sign-space"></div>
                <p class="name">( ....................................... )</p>
                <p>NIP. ...........................</p>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>

    {{-- Page Break untuk data selanjutnya, kecuali data terakhir --}}
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

    @endforeach

</body>
</html>