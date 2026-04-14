<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KonselorJadwalController extends Controller
{
    public function index(Request $request)
    {
        $idUser = session('id_user');

        $konselor = DB::table('konselor')->where('id_user', $idUser)->first();

        if (!$konselor) {
            return view('konselor.jadwal', [
                'jadwal'           => collect(),
                'tanggalHighlight' => collect(),
            ]);
        }

        $jadwal = DB::table('jadwal_konseling')
            ->join('pengajuan_konseling', 'jadwal_konseling.id_pengajuan', '=', 'pengajuan_konseling.id')
            ->join('siswa', 'pengajuan_konseling.id_siswa', '=', 'siswa.id')
            ->join('kategori_permasalahan', 'pengajuan_konseling.id_kategori', '=', 'kategori_permasalahan.id')
            ->select(
                'jadwal_konseling.tanggal_konseling',
                'jadwal_konseling.status',
                'siswa.nama as nama_siswa',
                'kategori_permasalahan.nama_kategori',
                'pengajuan_konseling.deskripsi_masalah'
            )
            ->where('pengajuan_konseling.id_konselor', $konselor->id)
            ->whereIn('jadwal_konseling.status', ['dijadwalkan', 'berlangsung'])
            ->get();

        $tanggalHighlight = $jadwal
            ->pluck('tanggal_konseling')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->unique()
            ->values();

        return view('konselor.jadwal', compact('jadwal', 'tanggalHighlight'));
    }
}