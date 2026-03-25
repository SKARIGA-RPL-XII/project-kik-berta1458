<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKonseling;
use App\Models\PengajuanKonseling;

class JadwalKonselingSeeder extends Seeder
{
    public function run(): void
    {
        $pengajuan = PengajuanKonseling::firstOrFail();

        JadwalKonseling::create([
            'id_pengajuan'     => $pengajuan->id,
            'tanggal_konseling'=> now()->addDays(3),
            'status'           => 'dijadwalkan',
        ]);
    }
}
