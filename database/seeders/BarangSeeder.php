<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\KelompokBarang;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID Satuan untuk referensi
        $sat_pcs = Satuan::where('nama_satuan', 'Pcs')->first()->id ?? 1;
        $sat_buah = Satuan::where('nama_satuan', 'Buah')->first()->id ?? 1;
        $sat_rim = Satuan::where('nama_satuan', 'Rim')->first()->id ?? 1;
        $sat_box = Satuan::where('nama_satuan', 'Box')->first()->id ?? 1;
        $sat_botol = Satuan::where('nama_satuan', 'Botol')->first()->id ?? 1;
        $sat_pack = Satuan::where('nama_satuan', 'Pack')->first()->id ?? 1;
        $sat_lembar = Satuan::where('nama_satuan', 'Lembar')->first()->id ?? 1;
        $sat_rol = Satuan::where('nama_satuan', 'Rol')->first()->id ?? 1;
        $sat_kaleng = Satuan::where('nama_satuan', 'Kaleng')->first()->id ?? 1;

        // Definisi Data Barang per Kelompok
        $daftar_barang = [
            // Alat Tulis (1.01.03.01.001)
            '1.01.03.01.001' => [
                ['Ballpoint Standard Hitam', $sat_pcs, 2500],
                ['Ballpoint Standard Biru', $sat_pcs, 2500],
                ['Pensil 2B Faber Castel', $sat_pcs, 4000],
                ['Spidol Permanent Hitam', $sat_pcs, 8000],
                ['Spidol Whiteboard Biru', $sat_pcs, 9000],
            ],
            // Tinta (1.01.03.01.002)
            '1.01.03.01.002' => [
                ['Tinta Epson 664 Black', $sat_botol, 85000],
                ['Tinta Epson 664 Cyan', $sat_botol, 85000],
                ['Tinta Stempel Ungu 50ml', $sat_botol, 25000],
                ['Bak Stempel (Pad) No.1', $sat_buah, 30000],
            ],
            // Penjepit Kertas (1.01.03.01.003)
            '1.01.03.01.003' => [
                ['Binder Clip No. 105', $sat_box, 6000],
                ['Binder Clip No. 107', $sat_box, 8500],
                ['Binder Clip No. 260', $sat_box, 15000],
                ['Paper Clip No. 3 (Kecil)', $sat_box, 3000],
                ['Paper Clip No. 1 (Besar)', $sat_box, 5000],
            ],
            // Penghapus/Korektor (1.01.03.01.004)
            '1.01.03.01.004' => [
                ['Tip-Ex (Correction Pen)', $sat_buah, 7000],
                ['Penghapus Karet Kecil', $sat_buah, 2000],
                ['Penghapus Whiteboard', $sat_buah, 12000],
            ],
            // Buku Tulis (1.01.03.01.005)
            '1.01.03.01.005' => [
                ['Buku Ekspedisi 100 Lembar', $sat_buah, 15000],
                ['Buku Folio Bergaris 100 Lembar', $sat_buah, 25000],
                ['Buku Kwitansi Besar', $sat_buah, 10000],
                ['Buku Agenda Kerja', $sat_buah, 35000],
            ],
            // Ordner & Map (1.01.03.01.006)
            '1.01.03.01.006' => [
                ['Ordner Bindex Folio 7cm', $sat_buah, 35000],
                ['Map Snelhechter Plastik Merah', $sat_buah, 4000],
                ['Map Snelhechter Plastik Biru', $sat_buah, 4000],
                ['Map Batik Kertas', $sat_buah, 2500],
                ['Clear Holder Isi 40 Lembar', $sat_buah, 22000],
            ],
            // Penggaris (1.01.03.01.007)
            '1.01.03.01.007' => [
                ['Penggaris Besi 30cm', $sat_buah, 12000],
                ['Penggaris Plastik 30cm', $sat_buah, 3000],
            ],
            // Cutter (1.01.03.01.008)
            '1.01.03.01.008' => [
                ['Cutter Besar L-500', $sat_buah, 18000],
                ['Cutter Kecil A-300', $sat_buah, 9000],
                ['Isi Cutter Besar (Tube)', $sat_tube ?? $sat_box, 15000],
            ],
            // Alat Perekat (1.01.03.01.010)
            '1.01.03.01.010' => [
                ['Lem Stick 25gr', $sat_buah, 8000],
                ['Lakban Hitam 2 Inch', $sat_rol, 12000],
                ['Lakban Bening 2 Inch', $sat_rol, 11000],
                ['Double Tape 1 Inch', $sat_rol, 9000],
                ['Lem Cair Povinal 500ml', $sat_botol, 25000],
            ],
            // Staples (1.01.03.01.012)
            '1.01.03.01.012' => [
                ['Stapler HD-10 (Kecil)', $sat_buah, 15000],
                ['Stapler HD-50 (Sedang)', $sat_buah, 45000],
                ['Stapler Jilid Besar', $sat_buah, 120000],
            ],
            // Isi Staples (1.01.03.01.013)
            '1.01.03.01.013' => [
                ['Isi Staples No. 10', $sat_box, 2500],
                ['Isi Staples No. 3 (Untuk HD-50)', $sat_box, 4500],
            ],
            // Kertas HVS (1.01.03.02.001)
            '1.01.03.02.001' => [
                ['Kertas HVS A4 70gr', $sat_rim, 45000],
                ['Kertas HVS A4 80gr', $sat_rim, 55000],
                ['Kertas HVS F4 70gr', $sat_rim, 50000],
                ['Kertas HVS A3 80gr', $sat_rim, 110000],
            ],
            // Amplop (1.01.03.02.004)
            '1.01.03.02.004' => [
                ['Amplop Putih Polos 90 (Kecil)', $sat_box, 20000],
                ['Amplop Coklat Kabinet (Sedang)', $sat_pack, 35000],
                ['Amplop Coklat Folio (Besar)', $sat_pack, 55000],
            ],
            // Sapu & Sikat (1.01.03.05.001)
            '1.01.03.05.001' => [
                ['Sapu Ijuk Lantai', $sat_buah, 25000],
                ['Sapu Lidi Taman', $sat_buah, 15000],
                ['Sikat WC Gagang Panjang', $sat_buah, 18000],
                ['Sikat Lantai Plastik', $sat_buah, 10000],
            ],
            // Alat Pel & Lap (1.01.03.05.002)
            '1.01.03.05.002' => [
                ['Kain Pel Set (Gagang+Kain)', $sat_set ?? $sat_buah, 45000],
                ['Refill Kain Pel', $sat_buah, 15000],
                ['Kanebo (Lap Chamois)', $sat_buah, 20000],
                ['Kain Lap Microfiber', $sat_buah, 12000],
            ],
            // Ember & Tempat Air (1.01.03.05.003)
            '1.01.03.05.003' => [
                ['Ember Plastik 4 Galon', $sat_buah, 25000],
                ['Gayung Air', $sat_buah, 8000],
                ['Selang Air 10 Meter', $sat_rol, 75000],
            ],
            // Keset & Tempat Sampah (1.01.03.05.004)
            '1.01.03.05.004' => [
                ['Tempat Sampah Injak 10L', $sat_buah, 65000],
                ['Tempat Sampah Jaring Besi', $sat_buah, 45000],
                ['Keset Karet Welcome', $sat_buah, 55000],
                ['Kantong Sampah Hitam Besar', $sat_pack, 25000],
            ],
            // Bahan Pembersih (1.01.03.05.008)
            '1.01.03.05.008' => [
                ['Cairan Pembersih Lantai 1L', $sat_botol, 18000],
                ['Sabun Cuci Tangan 500ml', $sat_botol, 15000],
                ['Pembersih Kaca (Glass Cleaner)', $sat_botol, 12000],
                ['Porstex Pembersih Keramik', $sat_botol, 20000],
                ['Deterjen Bubuk 1kg', $sat_pack, 22000],
            ],
            // Pengharum Ruangan (1.01.03.05.012)
            '1.01.03.05.012' => [
                ['Pengharum Ruangan Semprot 300ml', $sat_kaleng, 28000],
                ['Kamper Toilet (Bagus) Isi 5', $sat_pack, 18000],
                ['Pengharum Gantung Mobil/Ruangan', $sat_buah, 12000],
            ],
        ];

        // Loop dan Insert ke Database
        foreach ($daftar_barang as $kode_kelompok => $items) {
            $kelompok = KelompokBarang::where('kode_kelompok', $kode_kelompok)->first();

            if ($kelompok) {
                $counter = 1;
                foreach ($items as $item) {
                    $nama_barang = $item[0];
                    $satuan_id   = $item[1];
                    $harga       = $item[2];
                    $stok        = rand(10, 100); // Stok acak 10-100

                    // Format Kode Barang: 1.01.03.01.001.001 (Menambahkan 3 digit urut)
                    $kode_barang = $kode_kelompok . '.' . str_pad($counter, 3, '0', STR_PAD_LEFT);

                    Barang::firstOrCreate(
                        ['kode_barang' => $kode_barang],
                        [
                            'nama_barang' => $nama_barang,
                            'kelompok_barang_id' => $kelompok->id,
                            'satuan_id' => $satuan_id,
                            'stok_saat_ini' => $stok,
                            'harga' => $harga,
                            'deskripsi' => 'Seeded Data ' . $kelompok->nama_kelompok,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $counter++;
                }
            }
        }
    }
}