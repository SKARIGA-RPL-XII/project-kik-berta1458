<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Konselor;
use App\Models\KategoriPermasalahan;
use App\Models\Siswa;
use App\Models\LaporanKonseling;
use App\Models\PengajuanKonseling;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    public function storePengajuan(Request $request)
    {
        try {

            $request->validate([
                'id_kategori' => 'required',
                'tanggal_pengajuan' => 'required|date',
                'deskripsi_masalah' => 'nullable'
            ]);
            $user = \App\Models\User::find(session('id_user'));


            PengajuanKonseling::create([
                'id_siswa' => $user->siswa->id,
                'id_kategori' => $request->id_kategori,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'deskripsi_masalah' => $request->deskripsi_masalah ?? '-',
                'status' => 'menunggu'
            ]);

            return response()->json([
                'message' => 'Pengajuan berhasil dikirim!'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function pengajuan()
    {
        $kategori = \App\Models\KategoriPermasalahan::all();

        $user = \App\Models\User::find(session('id_user'));

        $pengajuan = \App\Models\PengajuanKonseling::with('kategori')
            ->where('id_siswa', $user->siswa->id)
            ->latest()
            ->get();
        return view('siswa.pengajuan', compact('kategori', 'pengajuan'));
    }

    public function riwayat()
    {
        $kategori = \App\Models\KategoriPermasalahan::all();

        $user = \App\Models\User::find(session('id_user'));

        $pengajuan = \App\Models\PengajuanKonseling::with(['kategori','laporan'])
            ->where('id_siswa', $user->siswa->id)
            ->whereIn('status', ['selesai', 'menunggu', 'ditolak', 'berlangsung', 'dijadwalkan'])
            ->latest()
            ->get();
        return view('siswa.riwayat', compact('kategori', 'pengajuan', 'user'));
    }
}
