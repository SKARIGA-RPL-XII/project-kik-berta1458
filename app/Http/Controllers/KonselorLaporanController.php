<?php

namespace App\Http\Controllers;

use App\Models\LaporanKonseling;
use App\Models\PengajuanKonseling;
use App\Models\JadwalKonseling;
use App\Models\PesanKonseling;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KonselorLaporanController extends Controller
{
    public function index(Request $request)
    {
        $this->updateStatusOtomatis();

        $user = \App\Models\User::find(session('id_user'));

        $query = PengajuanKonseling::with(['siswa', 'kategori', 'laporan', 'pesan'])
            ->where('id_konselor', $user->konselor->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tanggal) {
            $query->whereDate('tanggal_pengajuan', $request->tanggal);
        }

        if ($request->kategori) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_kategori', $request->kategori);
            });
        }

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
                    'bukti_file' => $filePath ?? $laporan->bukti_file
                ]);
            } else {
                LaporanKonseling::create([
                    'id_pengajuan' => $id,
                    'hasil_catatan' => $request->hasil_catatan,
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

    // Kirim pesan baru — disimpan ke tabel pesan_konselor (riwayat tidak hilang)
    public function simpanPesan(Request $request, $id)
    {
        $request->validate([
            'pesan_siswa' => 'required'
        ]);

        // Verifikasi pengajuan milik konselor ini
        $user = \App\Models\User::find(session('id_user'));
        PengajuanKonseling::where('id', $id)
            ->where('id_konselor', $user->konselor->id)
            ->firstOrFail();

        PesanKonseling::create([
            'id_pengajuan' => $id,
            'isi_pesan'    => $request->pesan_siswa,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim'
        ]);
    }

    // Ambil semua riwayat pesan untuk ditampilkan di chat
    public function ambilPesan($id)
    {
        // Verifikasi konselor hanya bisa ambil pesan pengajuannya sendiri
        $user = \App\Models\User::find(session('id_user'));
        PengajuanKonseling::where('id', $id)
            ->where('id_konselor', $user->konselor->id)
            ->firstOrFail();

        $pesan = PesanKonseling::where('id_pengajuan', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'isi_pesan'  => $p->isi_pesan,
                    'waktu'      => \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i'),
                ];
            });

        return response()->json($pesan);
    }

    private function updateStatusOtomatis()
    {
        $today = Carbon::today();

        JadwalKonseling::whereDate('tanggal_konseling', $today)
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'dijadwalkan');
            })
            ->get()
            ->each(function ($jadwal) {
                $jadwal->pengajuan->update(['status' => 'berlangsung']);
            });

        JadwalKonseling::whereDate('tanggal_konseling', '<', $today)
            ->whereHas('pengajuan', function ($q) {
                $q->where('status', 'berlangsung');
            })
            ->get()
            ->each(function ($jadwal) {
                $jadwal->pengajuan->update(['status' => 'selesai']);
            });
    }
}
