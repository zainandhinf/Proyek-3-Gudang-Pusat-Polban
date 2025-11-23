<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mutasi_barang_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mutasi_barang_id')->constrained('mutasi_barangs')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            $table->text('catatan')->nullable();
            // tidak ada timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_barang_details');
    }
};
