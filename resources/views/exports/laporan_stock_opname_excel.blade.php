<table>
    @foreach($opnames as $opname)
        {{-- HEADER RESMI --}}
        <tr>
            <td colspan="8" style="font-weight: bold; font-size: 14px;">POLITEKNIK NEGERI BANDUNG</td>
        </tr>
        <tr>
            <td colspan="8" style="font-weight: bold;">SUBBAGIAN TATA USAHA - URUSAN ASET DAN RUMAH TANGGA</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="8" style="text-align: center; font-weight: bold; font-size: 16px; border: 1px solid #000;">
                BERITA ACARA HASIL STOCK OPNAME (AUDIT STOK)
            </td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right; font-size: 10px;">NO. DOKUMEN: SO/{{ $opname->no_opname }}</td>
        </tr>
        <tr></tr>

        {{-- INFO OPNAME --}}
        <tr>
            <td colspan="2" style="font-weight: bold;">Nomor Opname</td>
            <td colspan="2">: {{ $opname->no_opname }}</td>
            <td></td>
            <td colspan="3" style="text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($opname->tanggal_opname)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Status</td>
            <td colspan="6">: {{ $opname->status }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Pencatat</td>
            <td colspan="6">: {{ $opname->dicatatOleh->name ?? '-' }}</td>
        </tr>
        <tr><td colspan="8"></td></tr>

        {{-- TABEL HASIL OPNAME --}}
        <thead>
            <tr>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;">NO.</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;" colspan="2">NAMA BARANG</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;">SATUAN</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;">STOK SISTEM</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;">STOK FISIK</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;">SELISIH</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee; vertical-align: middle;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($opname->detailStockOpnames as $index => $detail)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;" colspan="2">{{ $detail->barang->nama_barang }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $detail->barang->satuan->nama_satuan ?? 'Pcs' }}</td>
                
                <td style="border: 1px solid #000; text-align: center;">{{ $detail->stok_sistem }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $detail->stok_fisik ?? '-' }}</td>
                
                {{-- Warna Merah Jika Ada Selisih --}}
                <td style="border: 1px solid #000; text-align: center; {{ $detail->selisih != 0 ? 'color: red; font-weight: bold;' : '' }}">
                    {{ $detail->selisih > 0 ? '+'.$detail->selisih : $detail->selisih }}
                </td>
                
                <td style="border: 1px solid #000;">{{ $detail->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>

        {{-- FOOTER TTD --}}
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="3" style="text-align: center;">Dibuat Oleh (Pencatat),</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">Mengetahui (Kepala Unit),</td>
        </tr>
        <tr><td colspan="8" height="40"></td></tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $opname->dicatatOleh->name ?? '.......................' }}</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">( ..................................... )</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">NIP. ....................</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">NIP. ....................</td>
        </tr>

        {{-- Jarak Antar Halaman --}}
        <tr><td colspan="8" height="30" style="background-color: #808080;"></td></tr>
        <tr></tr>
    @endforeach
</table>