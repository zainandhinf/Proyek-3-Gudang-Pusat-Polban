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
        Schema::create('detail_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih');
            // tidak ada timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_stock_opnames');
    }
};
