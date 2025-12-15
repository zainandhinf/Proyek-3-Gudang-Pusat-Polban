<!DOCTYPE html>
<html>
<head>
    <title>Bukti Mutasi Barang</title>
    <style>
        /* Reset & Dasar */
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        
        /* Container per Halaman */
        .page-container {
            width: 100%;
            /* Pastikan tidak ada margin collapse */
            overflow: hidden; 
        }

        /* Page Break: Memaksa pindah halaman setelah elemen ini */
        .page-break {
            page-break-after: always;
        }

        /* Styling Header Kop */
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .header h3 { margin: 2px 0; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        
        /* Tabel Info Header (Tgl, No Bukti, dll) */
        .info-table { width: 100%; margin-bottom: 10px; border: none; }
        .info-table td { padding: 2px; vertical-align: top; border: none; }
        .label { font-weight: bold; width: 130px; }
        
        /* Tabel Rincian Barang */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; font-size: 10px; }
        .data-table td { font-size: 10px; }
        
        /* Helper Text Align */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Footer Tanda Tangan */
        .footer-ttd { width: 100%; margin-top: 20px; }
        .ttd-table { width: 100%; border: none; }
        .ttd-table td { border: none; text-align: center; vertical-align: top; }
        .space-ttd { height: 60px; }
    </style>
</head>
<body>

    @foreach($mutasis as $mutasi)
        <div class="page-container">
            <div class="header">
                <h2>POLITEKNIK NEGERI BANDUNG</h2>
                <h3>BUKTI {{ strtoupper($mutasi->jenis_mutasi) }} BARANG PERSEDIAAN</h3>
            </div>
            
            <hr style="border: 1px double #000; margin-bottom: 15px;">

            <table class="info-table">
                <tr>
                    <td class="label">TGL PENGELUARAN</td>
                    <td>: {{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">NO. BUKTI</td>
                    <td>: {{ $mutasi->nomor_mutasi }}</td>
                </tr>
                <tr>
                    <td class="label">NO. DOKUMEN</td>
                    <td>: {{ $mutasi->no_dokumen ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">UNIT / TUJUAN</td>
                    <td>
                        : 
                        @if($mutasi->jenis_mutasi == 'keluar' && $mutasi->permintaan)
                            {{ $mutasi->permintaan->unit_kerja }}
                        @elseif($mutasi->jenis_mutasi == 'masuk')
                            Gudang Pusat (Masuk/Pengadaan)
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">NO</th>
                        <th width="15%">KODE BARANG</th>
                        <th>NAMA BARANG</th>
                        <th width="20%">SPESIFIKASI</th>
                        <th width="10%">SATUAN</th>
                        <th width="8%">JUMLAH</th>
                        <th width="20%">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($mutasi->detail as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        
                        <td class="text-center">
                            {{ $item->barang->kelompokBarang->kode_kelompok ?? '00' }}.{{ sprintf('%03d', $item->barang->no_urut ?? 0) }}
                        </td>
                        
                        <td>{{ $item->barang->nama_barang }}</td>
                        
                        <td>{{ $item->barang->spesifikasi ?? '-' }}</td>
                        
                        <td class="text-center">{{ $item->barang->satuan->nama_satuan ?? 'Pcs' }}</td>
                        
                        <td class="text-center font-bold">{{ $item->jumlah }}</td>
                        
                        <td>{{ $item->catatan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer-ttd">
                <table class="ttd-table">
                    <tr>
                        <td width="30%"></td> <td width="40%"></td> <td width="30%">Bandung, {{ $tanggal_cetak }}</td>
                    </tr>
                    <tr>
                        <td>Yang Menyerahkan,</td>
                        <td>Yang Menerima,</td>
                        <td>Mengetahui,</td>
                    </tr>
                    <tr>
                        <td class="space-ttd"></td>
                        <td class="space-ttd"></td>
                        <td class="space-ttd"></td>
                    </tr>
                    <tr>
                        <td class="font-bold">( Petugas Gudang )</td>
                        <td class="font-bold">( ........................... )</td>
                        <td class="font-bold">( Kepala Urusan Gudang )</td>
                    </tr>
                </table>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>