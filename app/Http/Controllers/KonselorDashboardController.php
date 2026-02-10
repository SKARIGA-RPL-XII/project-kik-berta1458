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
        $tahun = Carbon::now()->year;

        $permintaanBaru = PengajuanKonseling::whereYear('created_at', $tahun)
            ->where('status', 'menunggu')
            ->count();

        $konselingAktif = JadwalKonseling::whereYear('tanggal_konseling', $tahun)
            ->where('status', 'berlangsung')
            ->count();

        $konselingSelesai = JadwalKonseling::whereYear('tanggal_konseling', $tahun)
            ->where('status', 'selesai')
            ->count();

        $jadwalHariIni = JadwalKonseling::with(['pengajuan.siswa'])
            ->whereDate('tanggal_konseling', Carbon::today())
            ->get();
        return view('konselor.dashboard', compact(
            'permintaanBaru',
            'konselingAktif',
            'konselingSelesai',
            'jadwalHariIni'
        ));
    }
}
?>