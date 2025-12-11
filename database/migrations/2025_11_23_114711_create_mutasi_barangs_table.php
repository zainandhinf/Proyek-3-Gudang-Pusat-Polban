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
        Schema::create('mutasi_barangs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_mutasi', ['masuk','keluar'])->index();
            $table->string('nomor_mutasi')->nullable()->unique();
            $table->timestamp('tanggal_mutasi');
            $table->string('no_surat')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('permintaan_id')->nullable()->constrained('permintaans')->onDelete('set null');
            $table->foreignId('dicatat_oleh_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_barangs');
    }
};
