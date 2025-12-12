<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelompok')->unique(); // Contoh: ATK, KTS
            $table->string('nama_kelompok'); // Contoh: Alat Tulis, Kertas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_barangs');
    }
};