<table>
    @foreach($data as $item)
        {{-- HEADER RESMI --}}
        <tr>
            <td colspan="7" style="font-weight: bold; font-size: 14px;">POLITEKNIK NEGERI BANDUNG</td>
        </tr>
        <tr>
            <td colspan="7" style="font-weight: bold;">SUBBAGIAN TATA USAHA - URUSAN ASET DAN RUMAH TANGGA</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="7" style="text-align: center; font-weight: bold; font-size: 16px; border: 1px solid #000;">
                BERITA ACARA BARANG RUSAK / USANG
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right; font-size: 10px;">NO. DOKUMEN: {{ $item->no_catat }}</td>
        </tr>
        <tr></tr>

        {{-- INFO DOKUMEN --}}
        <tr>
            <td colspan="2" style="font-weight: bold;">Nomor Dokumen</td>
            <td colspan="2">: {{ $item->no_catat }}</td>
            <td></td>
            <td colspan="2" style="text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($item->tanggal_catat)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Nomor Bukti Manual</td>
            <td colspan="5">: {{ $item->no_bukti ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Keterangan</td>
            <td colspan="5">: {{ $item->keterangan ?? '-' }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>

        {{-- TABEL BARANG --}}
        <thead>
            <tr>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">NO.</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;" colspan="2">NAMA BARANG</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">JUMLAH</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">SATUAN</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;" colspan="2">DETAIL KERUSAKAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($item->detail as $index => $detail)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;" colspan="2">{{ $detail->barang->nama_barang }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold; color: red;">{{ $detail->jumlah }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $detail->barang->satuan->nama_satuan ?? 'Pcs' }}</td>
                <td style="border: 1px solid #000;" colspan="2">{{ $detail->keterangan_rusak ?? '-' }}</td>
            </tr>
            @endforeach
            
            {{-- Spacer Rows --}}
            @for($i = 0; $i < (5 - $item->detail->count()); $i++)
            <tr>
                <td style="border: 1px solid #000; color: #fff;">.</td>
                <td style="border: 1px solid #000;" colspan="2"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;" colspan="2"></td>
            </tr>
            @endfor
        </tbody>

        {{-- FOOTER TTD --}}
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: center;">Pelapor / Pencatat,</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center;">Mengetahui,</td>
        </tr>
        <tr><td colspan="7" height="40"></td></tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $item->dicatatOleh->name ?? '.......................' }}</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">( ..................................... )</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">NIP. ....................</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center;">NIP. ....................</td>
        </tr>

        {{-- Jarak Antar Halaman --}}
        <tr><td colspan="7" height="30" style="background-color: #808080;"></td></tr>
        <tr></tr>
    @endforeach
</table>