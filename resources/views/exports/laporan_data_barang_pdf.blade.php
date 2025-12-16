<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; } /* Font diperkecil sedikit agar muat */
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; font-size: 9px; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-mono { font-family: monospace; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 9px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Barang Gudang</h1>
        <p>Politeknik Negeri Bandung</p>
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kd Kel.</th> <th width="12%">Kode Brg</th>
                <th>Nama Barang</th>
                <th width="15%">Kelompok</th>
                <th width="8%">Satuan</th>
                <th width="8%">Stok</th>
                <th width="15%">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $index => $barang)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center text-mono">{{ $barang->kelompokBarang->kode_kelompok ?? '-' }}</td>
                <td class="text-mono">{{ $barang->kode_barang }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->kelompokBarang->nama_kelompok ?? '-' }}</td>
                <td class="text-center">{{ $barang->satuan->nama_satuan ?? '-' }}</td>
                <td class="text-center">{{ $barang->stok_saat_ini }}</td>
                <td class="text-right">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Inventaris
    </div>
</body>
</html>