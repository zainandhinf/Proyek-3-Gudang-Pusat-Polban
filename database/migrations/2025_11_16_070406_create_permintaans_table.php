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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan')->unique();
            $table->enum('jenis_keperluan', [
                'Jurusan',
                'Program_Studi',
                'Laboratorium',
                'Bengkel',
                'Bagian',
                'Sub_Bagian',
                'Unit',
                'Urusan'
            ]);
            $table->foreignId('pemohon_user_id')->constrained('users');
            $table->foreignId('approval_user_id')->nullable()->constrained('users');
            $table->foreignId('operator_user_id')->nullable()->constrained('users');
            $table->enum('status', ['Pending', 'Processed', 'Approved', 'Rejected', 'Selesai'])->default('Pending');
            $table->timestamp('tanggal_pengajuan');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->string('file_path')->nullable(); // Untuk menyimpan path file yg di-upload
            $table->boolean('is_transcribed')->default(false); // Penanda untuk Operator
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaans');
    }
};
