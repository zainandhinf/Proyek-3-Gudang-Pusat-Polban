<table>
    @foreach($permintaans as $p)
        {{-- HEADER FORMULIR --}}
        <tr>
            <td colspan="7" style="font-weight: bold; font-size: 14px;">POLITEKNIK NEGERI BANDUNG</td>
        </tr>
        <tr>
            <td colspan="7" style="font-weight: bold;">SUBBAGIAN TATA USAHA - URUSAN ASET DAN RUMAH TANGGA</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="7" style="text-align: center; font-weight: bold; font-size: 16px; border: 1px solid #000;">
                FORMULIR PERMINTAAN KEBUTUHAN ATK/ BARANG/ALAT KEBERSIHAN
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right; font-size: 10px;">NO. DOKUMEN: PL1.R2.8.IK.02.01.FKBA</td>
        </tr>
        <tr></tr>

        {{-- INFO PERMINTAAN --}}
        <tr>
            <td colspan="2" style="font-weight: bold;">Nomor</td>
            <td>: {{ $p->no_permintaan }}</td>
            <td></td>
            <td colspan="3" style="text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Kepada Yth</td>
            <td colspan="5">: Kepala Bagian Keuangan dan Umum</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="5">  Politeknik Negeri Bandung</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Keperluan (Unit)</td>
            <td colspan="5">: {{ $p->jenis_keperluan }} - {{ $p->pemohon->name }}</td>
        </tr>
        <tr>
            <td colspan="7">Dengan ini kami mengajukan ATK/ Barang kebersihan untuk keperluan tersebut di atas, adapun rinciannya sebagai berikut:</td>
        </tr>
        <tr></tr>

        {{-- TABEL ITEM --}}
        <thead>
            <tr>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">NO.</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;" colspan="2">NAMA BARANG</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;" colspan="2">SPESIFIKASI (UKURAN, TYPE)</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">SATUAN</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #eeeeee;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach($p->detailPermintaans as $index => $detail)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;" colspan="2">{{ $detail->barang->nama_barang }}</td>
                <td style="border: 1px solid #000;" colspan="2">
                    {{-- Mencoba menampilkan spesifikasi jika ada di tabel barang, jika tidak kosong --}}
                    {{ $detail->barang->spesifikasi ?? '-' }}
                </td>
                <td style="border: 1px solid #000; text-align: center;">{{ $detail->barang->satuan->nama_satuan ?? 'Pcs' }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $detail->jumlah_diminta }}</td>
            </tr>
            @endforeach
            
            {{-- Baris Kosong Pelengkap (Opsional agar terlihat seperti form kertas) --}}
            @for($i = 0; $i < (5 - $p->detailPermintaans->count()); $i++)
            <tr>
                <td style="border: 1px solid #000; color: #fff;">.</td>
                <td style="border: 1px solid #000;" colspan="2"></td>
                <td style="border: 1px solid #000;" colspan="2"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
            </tr>
            @endfor
        </tbody>

        {{-- FOOTER TTD --}}
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: center;">Pemohon,</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center;">Mengetahui/Menyetujui,</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">{{ $p->jenis_keperluan }}</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center;">PPK/ Koordinator Unit</td>
        </tr>
        <tr><td colspan="7" height="40"></td></tr> {{-- Spasi Tanda Tangan --}}
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $p->pemohon->name }}</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">( ..................................... )</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">NIP. ....................</td>
            <td colspan="3"></td>
            <td colspan="2" style="text-align: center;">NIP. ....................</td>
        </tr>

        {{-- Jarak Antar Dokumen Jika Export Banyak --}}
        <tr><td colspan="7" height="30" style="background-color: #808080;"></td></tr>
        <tr></tr>
    @endforeach
</table>