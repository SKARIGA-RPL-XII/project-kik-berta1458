<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;

class KonselorJadwalController extends Controller
{
    public function index()
    {
        $jadwal = JadwalKonseling::with(['pengajuan.siswa', 'pengajuan.kategori'])
            ->whereIn('status', ['dijadwalkan', 'berlangsung'])
            ->orderBy('tanggal_konseling', 'asc')
            ->get();

        return view('konselor.jadwal', compact('jadwal'));
    }
}
