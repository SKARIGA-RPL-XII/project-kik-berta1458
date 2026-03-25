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

        $laporan = PengajuanKonseling::with(['siswa', 'kategori', 'laporan'])
            ->whereIn('status', ['berlangsung', 'selesai', 'ditolak', 'dijadwalkan'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('konselor.laporan', compact('laporan'));
    }

    public function simpanLaporan(Request $request, $id)
    {
        try {

            $request->validate([
                'hasil_catatan' => 'required',
                'bukti_foto' => 'required',
                'pesan_siswa' => 'nullable',
            ]);

            $hasil = $request->input('hasil_catatan');
            $foto  = $request->input('bukti_foto');
            $pesan = $request->input('pesan_siswa');

            LaporanKonseling::updateOrCreate(
                ['id_pengajuan' => $id],
                [
                    'hasil_catatan' => $hasil,
                    'bukti_foto'    => $foto,
                    'pesan_siswa'   => $pesan
                ]
            );

            $pengajuan = PengajuanKonseling::findOrFail($id);
            $pengajuan->status = 'selesai';
            $pengajuan->save();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil disimpan!',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
