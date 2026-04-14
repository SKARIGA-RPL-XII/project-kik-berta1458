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
                'id_konselor' => 'required',
                'tanggal_pengajuan' => 'required|date',
                'deskripsi_masalah' => 'nullable'
            ]);
            $user = \App\Models\User::find(session('id_user'));


            PengajuanKonseling::create([
                'id_siswa' => $user->siswa->id,
                'id_kategori' => $request->id_kategori,
                'id_konselor' => $request->id_konselor,
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
        $konselor = \App\Models\Konselor::all();
        $query = \App\Models\PengajuanKonseling::with('kategori')
            ->where('id_siswa', $user->siswa->id);

        //  FILTER TANGGAL
        if (request('tanggal')) {
            $query->whereDate('tanggal_pengajuan', request('tanggal'));
        }

        //  FILTER KATEGORI
        if (request('kategori')) {
            $query->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', request('kategori'));
            });
        }

        $pengajuan = $query->latest()->get();
        return view('siswa.pengajuan', compact('kategori', 'pengajuan', 'konselor'));
    }

    public function riwayat()
    {
        $kategori = \App\Models\KategoriPermasalahan::all();

        $user = \App\Models\User::find(session('id_user'));

        $query = \App\Models\PengajuanKonseling::with(['kategori', 'laporan', 'konselor'])
            ->where('id_siswa', $user->siswa->id)
            ->whereIn('status', ['selesai', 'menunggu', 'ditolak', 'berlangsung', 'dijadwalkan']);

        if (request('tanggal')) {
            $query->whereDate('tanggal_pengajuan', request('tanggal'));
        }

        if (request('kategori')) {
            $query->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', request('kategori'));
            });
        }

        if (request('search')) {
            $search = request('search');

            $query->whereHas('konselor', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        $pengajuan = $query->latest()->get();
        return view('siswa.riwayat', compact('kategori', 'pengajuan', 'user'));
    }

    public function profile()
    {
        $user = \App\Models\User::find(session('id_user'));

        if (!$user || !$user->siswa) {
            return redirect('/login');
        }

        $siswa = $user->siswa;

        return view('siswa.profile', compact('siswa'));
    }

    public function dashboard()
    {
        $user = \App\Models\User::find(session('id_user'));

        if (!$user || !$user->siswa) {
            return redirect('/login');
        }

        $siswaId = $user->siswa->id;

        // total pengajuan
        $total = \App\Models\PengajuanKonseling::where('id_siswa', $siswaId)->count();

        // aktif (menunggu + berlangsung + dijadwalkan)
        $aktif = \App\Models\PengajuanKonseling::where('id_siswa', $siswaId)
            ->whereIn('status', ['menunggu', 'berlangsung', 'dijadwalkan'])
            ->count();

        // selesai
        $selesai = \App\Models\PengajuanKonseling::where('id_siswa', $siswaId)
            ->where('status', 'selesai')
            ->count();

        // data bulan ini
        $bulanIni = \App\Models\PengajuanKonseling::with('kategori')
            ->where('id_siswa', $siswaId)
            ->whereMonth('tanggal_pengajuan', now()->month)
            ->latest()
            ->get();

        return view('siswa.dashboard', compact(
            'total',
            'aktif',
            'selesai',
            'bulanIni'
        ));
    }
}
