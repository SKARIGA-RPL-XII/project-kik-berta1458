<?php

namespace App\Http\Controllers;

use App\Models\LaporanKonseling;
use App\Models\PengajuanKonseling;
use App\Models\JadwalKonseling;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KonselorLaporanController extends Controller
{
    public function index(Request $request)
    {
        $this->updateStatusOtomatis();

        $user = \App\Models\User::find(session('id_user'));

        $query = PengajuanKonseling::with(['siswa', 'kategori', 'laporan'])
            ->where('id_konselor', $user->konselor->id);

        //  FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        //  FILTER TANGGAL
        if ($request->tanggal) {
            $query->whereDate('tanggal_pengajuan', $request->tanggal);
        }

        //  FILTER KATEGORI
        if ($request->kategori) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_kategori', $request->kategori);
            });
        }

        //  SEARCH (nama siswa)
        if ($request->search) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }
        $kategori = \App\Models\KategoriPermasalahan::all();
        $laporan = $query->latest()->get();
        return view('konselor.laporan', compact('laporan', 'kategori'));
    }

    public function simpanLaporan(Request $request, $id)
    {
        try {

            $request->validate([
                'hasil_catatan' => 'required',
            ]);

            $filePath = null;

            if ($request->hasFile('bukti_file')) {
                $filePath = $request->file('bukti_file')->store('bukti', 'public');
            }

            $laporan = LaporanKonseling::where('id_pengajuan', $id)->first();

            if ($laporan) {
                $laporan->update([
                    'hasil_catatan' => $request->hasil_catatan,
                    'pesan_siswa' => $request->pesan_siswa,
                    'bukti_file' => $filePath ?? $laporan->bukti_file
                ]);
            } else {
                LaporanKonseling::create([
                    'id_pengajuan' => $id,
                    'hasil_catatan' => $request->hasil_catatan,
                    'pesan_siswa' => $request->pesan_siswa,
                    'bukti_file' => $filePath
                ]);
            }

            PengajuanKonseling::where('id', $id)->update([
                'status' => 'selesai'
            ]);

            return response()->json([
                'message' => 'Laporan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function updateStatusOtomatis()
    {
        $today = Carbon::today();

        // berlangsung
        JadwalKonseling::whereDate('tanggal_konseling', $today)
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'dijadwalkan');
            })
            ->get()
            ->each(function ($jadwal) {
                $jadwal->pengajuan->update([
                    'status' => 'berlangsung'
                ]);
            });

        // selesai (lewat tanggal)
        JadwalKonseling::whereDate('tanggal_konseling', '<', $today)
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'berlangsung');
            })
            ->get()
            ->each(function ($jadwal) {
                $jadwal->pengajuan->update([
                    'status' => 'selesai'
                ]);
            });
    }
}
