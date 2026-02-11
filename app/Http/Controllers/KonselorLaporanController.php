<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonseling;
use Illuminate\Http\Request;

class KonselorLaporanController extends Controller
{
    public function index()
    {
        $laporan = PengajuanKonseling::with(['pengajuan.siswa', 'pengajuan.kategori'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        return view('konselor.laporan', compact('laporan'));
    }
    public function simpanLaporan(Request $request, $id)
{
    $request->validate([
        'hasil_catatan' => 'required|string',
    ]);

    \App\Models\LaporanKonseling::create([
        'id_pengajuan' => $id,
        'hasil_catatan' => $request->hasil_catatan,
    ]);

    $pengajuan = \App\Models\PengajuanKonseling::find($id);
    $pengajuan->status = 'selesai';
    $pengajuan->save();

    return response()->json([
        'success' => true,
        'message' => 'Laporan berhasil disimpan!',
    ]);
}

}
