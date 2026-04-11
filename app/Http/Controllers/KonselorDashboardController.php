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
        $user = \App\Models\User::find(session('id_user'));

        // VALIDASI
        if (!$user || !$user->konselor) {
            return redirect('/login');
        }

        $konselorId = $user->konselor->id;
        $tahun = Carbon::now()->year;

        // PERMINTAAN BARU (MENUNGGU)
        $permintaanBaru = PengajuanKonseling::where('id_konselor', $konselorId)
            ->where('status', 'menunggu')
            ->whereYear('tanggal_pengajuan', $tahun)
            ->count();

        // KONSELING AKTIF
        $konselingAktif = PengajuanKonseling::where('id_konselor', $konselorId)
            ->whereIn('status', ['dijadwalkan', 'berlangsung'])
            ->whereYear('tanggal_pengajuan', $tahun)
            ->count();

        // KONSELING SELESAI
        $konselingSelesai = PengajuanKonseling::where('id_konselor', $konselorId)
            ->where('status', 'selesai')
            ->whereYear('tanggal_pengajuan', $tahun)
            ->count();

        // HIGHLIGHT KALENDER (HANYA MILIK KONSELOR INI)
        $jadwalHighlight = JadwalKonseling::whereHas('pengajuan', function ($q) use ($konselorId) {
            $q->where('id_konselor', $konselorId)
              ->whereIn('status', ['dijadwalkan', 'berlangsung']);
        })
            ->pluck('tanggal_konseling')
            ->map(fn($tgl) => Carbon::parse($tgl)->format('Y-m-d'))
            ->toArray();

        // JADWAL HARI INI
        $jadwalHariIni = JadwalKonseling::with(['pengajuan.siswa', 'pengajuan.kategori'])
            ->whereDate('tanggal_konseling', Carbon::today())
            ->whereHas('pengajuan', function ($q) use ($konselorId) {
                $q->where('id_konselor', $konselorId)
                  ->where('status', 'berlangsung');
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
