<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Models\JadwalKonseling;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        $today = Carbon::now()->toDateString();

        // 🔥 UBAH JADI BERLANGSUNG
        JadwalKonseling::whereDate('tanggal_konseling', $today)
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'dijadwalkan');
            })
            ->get()
            ->each(function ($jadwal) {
                $jadwal->pengajuan->update([
                    'status' => 'berlangsung'
                ]);
            });

        JadwalKonseling::whereDate('tanggal_konseling', '<', $today)
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'berlangsung');
            })
            ->get()
            ->each(function ($jadwal) {
                $jadwal->pengajuan->update([
                    'status' => 'selesai'
                ]);
            });
    }
}
