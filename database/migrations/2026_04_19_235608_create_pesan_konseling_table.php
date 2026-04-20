<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesan_konseling', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pengajuan')
                ->constrained('pengajuan_konseling')
                ->onDelete('cascade');

            $table->text('isi_pesan');
            $table->enum('pengirim', ['konselor', 'admin']); // opsional tapi penting
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_konseling');
    }
};
