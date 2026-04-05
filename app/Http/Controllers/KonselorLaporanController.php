<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonseling;
use App\Models\LaporanKonseling;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class KonselorLaporanController extends Controller
{
    public function index()
    {
        // Update status otomatis jadi berlangsung
        PengajuanKonseling::where('status', 'dijadwalkan')
            ->whereDate('tanggal_pengajuan', Carbon::today())
            ->update(['status' => 'berlangsung']);

        // Ambil data laporan
        $laporan = PengajuanKonseling::with(['siswa', 'kategori', 'laporan'])
            ->whereIn('status', ['berlangsung', 'selesai', 'ditolak', 'dijadwalkan'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('konselor.laporan', compact('laporan'));
    }

    public function simpanLaporan(Request $request, $id)
    {
        try {
            // Ambil laporan lama (kalau ada)
            $laporanLama = LaporanKonseling::where('id_pengajuan', $id)->first();

            // Validasi
            $request->validate([
                'hasil_catatan' => 'required',
                'bukti_file' => $laporanLama 
                    ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240' 
                    : 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'pesan_siswa' => 'nullable',
            ]);

            $hasil = $request->input('hasil_catatan');
            $pesan = $request->input('pesan_siswa');

            // Gunakan path lama jika tidak ada upload baru
            $path = $laporanLama->bukti_file ?? null;

            if ($request->hasFile('bukti_file')) {
                // Hapus file lama kalau ada
                if ($laporanLama && $laporanLama->bukti_file) {
                    Storage::disk('public')->delete($laporanLama->bukti_file);
                }
                $file = $request->file('bukti_file');
                $path = $file->store('bukti_konseling', 'public');
            }

            // Simpan laporan
            LaporanKonseling::updateOrCreate(
                ['id_pengajuan' => $id],
                [
                    'hasil_catatan' => $hasil,
                    'bukti_file'    => $path,
                    'pesan_siswa'   => $pesan
                ]
            );

            // Update status pengajuan jadi selesai
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