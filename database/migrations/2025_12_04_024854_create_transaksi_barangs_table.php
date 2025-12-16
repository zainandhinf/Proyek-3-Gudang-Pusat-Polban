<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_barangs', function (Blueprint $table) {
            $table->id();
            // Kunci otomatisasi: Relasi ke tabel master barang
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            
            $table->date('tanggal'); 
            $table->enum('jenis', ['masuk', 'keluar']); // Masuk = Pembelian, Keluar = Permintaan
            $table->integer('jumlah');
            $table->text('keterangan')->nullable(); // Untuk kolom 'Ket' di Excel
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_barangs');
    }
};