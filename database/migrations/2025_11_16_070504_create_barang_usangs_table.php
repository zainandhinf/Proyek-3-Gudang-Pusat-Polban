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
        Schema::create('barang_usangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dicatat_oleh_user_id')->constrained('users');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('jumlah');
            $table->string('alasan')->nullable();
            $table->timestamp('tanggal_catat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_usangs');
    }
};
