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
        Schema::create('detail_barang_usangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_usang_id')->constrained('barang_usangs')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('jumlah');
            $table->string('keterangan_rusak')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang_usangs');
    }
};
