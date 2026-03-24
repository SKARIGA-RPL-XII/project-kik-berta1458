<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pengajuan_konseling MODIFY status 
        ENUM('menunggu','dijadwalkan','berlangsung','ditolak','selesai') 
        DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengajuan_konseling MODIFY status 
        ENUM('menunggu','dijadwalkan','ditolak','selesai') 
        DEFAULT 'menunggu'");
    }
};
