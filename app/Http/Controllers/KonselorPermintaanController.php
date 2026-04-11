<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonseling;
use App\Models\JadwalKonseling;
use Illuminate\Http\Request;


class KonselorPermintaanController extends Controller
{
    public function index(Request $request)
    {
        $konselor = \App\Models\Konselor::where('id_user', session('id_user'))->first();
        $tanggal = $request->query('tanggal');

        $data = PengajuanKonseling::with(['siswa', 'kategori'])
            ->when($tanggal, fn($q) => $q->whereDate('tanggal_pengajuan', $tanggal))
            ->where('status', 'menunggu')
            ->where('id_konselor', $konselor->id)
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

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

        $user = \App\Models\User::find(session('id_user'));

        $pengajuan->status = 'dijadwalkan';
        $pengajuan->id_konselor = $user->konselor->id; // 🔥 WAJIB
        $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => "Pengajuan berhasil diterima!"
        ]);
    }
}
