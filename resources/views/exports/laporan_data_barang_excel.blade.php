<table>
    <tr>
        <td colspan="8" style="text-align: center; font-weight: bold; font-size: 16px;">
            LAPORAN DATA BARANG GUDANG
        </td>
    </tr>
    <tr>
        <td colspan="8" style="text-align: center; font-size: 11px;">
            POLITEKNIK NEGERI BANDUNG - Tanggal Cetak: {{ date('d F Y') }}
        </td>
    </tr>
    <tr></tr>

    <thead>
        <tr>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">NO</th>
            {{-- KOLOM BARU --}}
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">KODE KELOMPOK</th>
            
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">KODE BARANG</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">NAMA BARANG</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">KELOMPOK BARANG</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">SATUAN</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">STOK SAAT INI</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">HARGA (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($barangs as $index => $barang)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            
            {{-- DATA KODE KELOMPOK --}}
            <td style="border: 1px solid #000; text-align: center;">{{ $barang->kelompokBarang->kode_kelompok ?? '-' }}</td>
            
            <td style="border: 1px solid #000; text-align: left;">{{ $barang->kode_barang }}</td>
            <td style="border: 1px solid #000;">{{ $barang->nama_barang }}</td>
            <td style="border: 1px solid #000;">{{ $barang->kelompokBarang->nama_kelompok ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $barang->satuan->nama_satuan ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $barang->stok_saat_ini }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($barang->harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>