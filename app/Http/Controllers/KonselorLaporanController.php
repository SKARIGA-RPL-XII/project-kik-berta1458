<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonseling;
use App\Models\LaporanKonseling;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KonselorLaporanController extends Controller
{
    public function index()
    {
        PengajuanKonseling::where('status', 'dijadwalkan')
            ->whereDate('tanggal_pengajuan', Carbon::today())
            ->update(['status' => 'berlangsung']);

        $laporan = PengajuanKonseling::with(['siswa','kategori','laporan'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('konselor.laporan', compact('laporan'));
    }

    public function simpanLaporan(Request $request, $id)
    {
        $request->validate([
            'hasil_catatan' => 'required|string',
        ]);

        LaporanKonseling::create([
            'id_pengajuan' => $id,
            'hasil_catatan' => $request->hasil_catatan,
        ]);

        $pengajuan = PengajuanKonseling::findOrFail($id);
        $pengajuan->status = 'selesai';
        $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil disimpan!',
        ]);
    }
}
