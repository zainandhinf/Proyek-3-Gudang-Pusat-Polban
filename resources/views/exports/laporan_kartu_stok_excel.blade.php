<table>
    <tr>
        <td colspan="19" style="text-align: center; font-weight: bold; font-size: 16px; height: 35px; vertical-align: middle;">
            KARTU STOK BARANG GUDANG TAHUN {{ $year }}
        </td>
    </tr>
    <tr></tr> <thead>
        <tr>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 5px; background-color: #EEEEEE;">NO</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 20px; background-color: #EEEEEE;">KODE BARANG</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 45px; background-color: #EEEEEE;">NAMA BARANG</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px; background-color: #EEEEEE;">SATUAN</th>
            
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px; background-color: #FFFF99;">SALDO AWAL</th>
            
            <th colspan="12" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #EEEEEE;">MUTASI BULAN</th>
            
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 15px; background-color: #FFCCCC;">TOTAL PEMAKAIAN</th>
            
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px; background-color: #CCFFFF;">SALDO AKHIR</th>
        </tr>
        <tr>
            @foreach(['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGU','SEP','OKT','NOV','DES'] as $bln)
                <th style="border: 1px solid #000000; font-weight: bold; text-align: center; width: 8px; font-size: 10px; background-color: #EEEEEE;">{{ $bln }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($data as $index => $item)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000; text-align: left;">{{ $item->kode_barang }}</td>
            <td style="border: 1px solid #000000;">{{ $item->nama_barang }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $item->satuan }}</td>
            
            <td style="border: 1px solid #000000; text-align: center; background-color: #FFFF99; font-weight: bold;">
                {{ $item->saldo_awal }}
            </td>
            
            @for($m=1; $m<=12; $m++)
                @php 
                    $val = $item->mutasi[$m];
                    // Warna teks: Hijau (Masuk/Plus), Merah (Keluar/Minus)
                    $color = '#000000';
                    if($val > 0) $color = '#006400'; 
                    if($val < 0) $color = '#8B0000';
                @endphp
                <td style="border: 1px solid #000000; text-align: center; color: {{ $color }};">
                    {{ $val == 0 ? '-' : ($val > 0 ? '+'.$val : $val) }}
                </td>
            @endfor

            <td style="border: 1px solid #000000; text-align: center; color: #8B0000; background-color: #FFCCCC;">
                {{ $item->total_pemakaian }}
            </td>

            <td style="border: 1px solid #000000; text-align: center; background-color: #CCFFFF; font-weight: bold;">
                {{ $item->saldo_akhir }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>