<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonseling;
use App\Models\JadwalKonseling;
use Illuminate\Http\Request;


class KonselorPermintaanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal');

        $data = PengajuanKonseling::with(['siswa', 'kategori'])
            ->when($tanggal, fn($q) => $q->whereDate('tanggal_pengajuan', $tanggal))
            ->orderBy('tanggal_pengajuan', 'asc')->get();

        return view('konselor.permintaan', compact('data'));
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = PengajuanKonseling::findOrFail($id);

        $pengajuan->status = 'ditolak';
        $pengajuan->alasan_penolakan = $request->alasan_penolakan;
        $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil ditolak.'
        ]);
    }


    public function terima($id)
    {
        $pengajuan = PengajuanKonseling::findOrFail($id);

        $pengajuan->status = 'dijadwalkan';
        $pengajuan->save();

        $tanggal = $pengajuan->tanggal_pengajuan;
        $tanggalHariIni = \Carbon\Carbon::now()->toDateString();

        $statusJadwal = ($tanggal == $tanggalHariIni) ? 'berlangsung' : 'dijadwalkan';

        JadwalKonseling::create([
            'id_pengajuan' => $pengajuan->id,
            'tanggal_konseling' => $tanggal,
            'status' => $statusJadwal
        ]);

        return response()->json([
            'success' => true,
            'status_jadwal' => $statusJadwal,
            'message' => "Pengajuan berhasil diterima!"
        ]);
    }
}
