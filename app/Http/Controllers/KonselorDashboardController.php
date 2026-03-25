<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonseling;
use Carbon\Carbon;
use App\Models\PengajuanKonseling;
use Illuminate\Http\Request;

class KonselorDashboardController extends Controller
{
    public function index()
    {
        $permintaanBaru = PengajuanKonseling::where('status', 'menunggu')
            ->whereYear('tanggal_pengajuan', Carbon::now()->year)
            ->count();
        $konselingAktif = PengajuanKonseling::whereIn('status', ['dijadwalkan', 'berlangsung'])
            ->whereYear('tanggal_pengajuan', Carbon::now()->year)
            ->count();
        $konselingSelesai = PengajuanKonseling::where('status', 'selesai')
            ->whereYear('tanggal_pengajuan', Carbon::now()->year)
            ->count();
        $jadwalHighlight = JadwalKonseling::whereHas('pengajuan', function ($q) {
            $q->whereIn('status', ['dijadwalkan', 'berlangsung']);
        })
            ->pluck('tanggal_konseling')
            ->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->format('Y-m-d'))
            ->toArray();
        $jadwalHariIni = JadwalKonseling::with(['pengajuan.siswa', 'pengajuan.kategori'])
            ->whereDate('tanggal_konseling', Carbon::today())
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'berlangsung');
            })
            ->get();

        return view('konselor.dashboard', compact(
            'permintaanBaru',
            'konselingAktif',
            'konselingSelesai',
            'jadwalHighlight',
            'jadwalHariIni'
        ));
    }
}
