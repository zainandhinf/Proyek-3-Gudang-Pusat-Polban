<table>
    <tr>
        <th colspan="7" style="text-align: center;">POLITEKNIK NEGERI BANDUNG</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: center;">BUKTI {{ strtoupper($mutasi->jenis_mutasi) }} BARANG PERSEDIAAN</th>
    </tr>
    <tr></tr> <tr>
        <td colspan="2" style="font-weight: bold;">TGL PENGELUARAN</td>
        <td colspan="5">: {{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">NO. BUKTI</td>
        <td colspan="5">: {{ $mutasi->nomor_mutasi }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">NO. DOKUMEN</td>
        <td colspan="5">: {{ $mutasi->no_dokumen ?? '-' }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">UNIT / TUJUAN</td>
        <td colspan="5">
            : 
            @if($mutasi->jenis_mutasi == 'keluar' && $mutasi->permintaan)
                {{ $mutasi->permintaan->unit_kerja }}
            @elseif($mutasi->jenis_mutasi == 'masuk')
                Gudang Pusat (Masuk)
            @else
                -
            @endif
        </td>
    </tr>
    <tr></tr> <thead>
        <tr>
            <th style="border: 1px solid #000; text-align: center; width: 5px;">NO</th>
            <th style="border: 1px solid #000; text-align: center; width: 20px;">KODE BARANG</th>
            <th style="border: 1px solid #000; text-align: center; width: 35px;">NAMA BARANG</th>
            <th style="border: 1px solid #000; text-align: center; width: 25px;">SPESIFIKASI</th>
            <th style="border: 1px solid #000; text-align: center; width: 10px;">SATUAN</th>
            <th style="border: 1px solid #000; text-align: center; width: 10px;">JUMLAH</th>
            <th style="border: 1px solid #000; text-align: center; width: 30px;">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($details as $item)
        <tr>
            <td style="border: 1px solid #000; text-align: center; vertical-align: top;">{{ $no++ }}</td>
            
            <td style="border: 1px solid #000; text-align: left; vertical-align: top;">
                {{ $item->barang->kelompokBarang->kode_kelompok ?? '00' }}.{{ sprintf('%03d', $item->barang->no_urut ?? 0) }}
            </td>
            
            <td style="border: 1px solid #000; vertical-align: top;">{{ $item->barang->nama_barang }}</td>
            
            <td style="border: 1px solid #000; vertical-align: top;">{{ $item->barang->spesifikasi ?? '-' }}</td>
            
            <td style="border: 1px solid #000; text-align: center; vertical-align: top;">{{ $item->barang->satuan->nama_satuan ?? 'Pcs' }}</td>
            
            <td style="border: 1px solid #000; text-align: center; vertical-align: top; font-weight: bold;">{{ $item->jumlah }}</td>
            
            <td style="border: 1px solid #000; vertical-align: top;">{{ $item->catatan ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2" style="text-align: center;">Bandung, {{ $tanggal_cetak }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">Yang Menyerahkan,</td>
            <td></td>
            <td></td>
            <td colspan="1"></td>
            <td colspan="2" style="text-align: center;">Yang Menerima,</td>
        </tr>
        <tr>
            <td colspan="7" style="height: 60px;"></td> </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">( Petugas Gudang )</td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="2" style="text-align: center; font-weight: bold;">( ........................... )</td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">Mengetahui,</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; height: 60px;"></td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; font-weight: bold;">( Kepala Urusan Gudang )</td>
        </tr>
    </tfoot>
</table>