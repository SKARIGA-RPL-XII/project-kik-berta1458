<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_konseling', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_siswa')
                ->constrained('siswa')
                ->onDelete('cascade');

            $table->foreignId('id_konselor')
                ->nullable()
                ->constrained('konselor')
                ->onDelete('set null');

            $table->foreignId('id_kategori')
                ->constrained('kategori_permasalahan');

            $table->date('tanggal_pengajuan');
            $table->text('deskripsi_masalah');

            $table->enum('status', [
                'menunggu',
                'dijadwalkan',
                'ditolak',
                'berlangsung',
                'selesai'
            ])->default('menunggu');

            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
