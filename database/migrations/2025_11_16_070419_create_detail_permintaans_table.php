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
        Schema::create('detail_permintaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('jumlah_diminta');
            $table->string('keterangan')->nullable();
            // tidak ada timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permintaans');
    }
};
