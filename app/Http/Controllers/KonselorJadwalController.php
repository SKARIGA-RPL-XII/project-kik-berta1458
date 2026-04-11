<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;
use App\Models\PengajuanKonseling;
use Carbon\Carbon;

class KonselorJadwalController extends Controller
{
    public function index()
    {
        $this->updateStatusOtomatis();

        $jadwal = JadwalKonseling::with(['pengajuan.siswa', 'pengajuan.kategori'])
            ->whereHas('pengajuan', function ($query) {
                $query->whereIn('status', ['dijadwalkan', 'berlangsung']);
            })
            ->orderBy('tanggal_konseling', 'asc')
            ->get();

        return view('konselor.jadwal', compact('jadwal'));
    }

    private function updateStatusOtomatis()
    {
        $today = Carbon::today();

        // 🔥 jadi berlangsung
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

        // 🔥 jadi selesai jika lewat
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
