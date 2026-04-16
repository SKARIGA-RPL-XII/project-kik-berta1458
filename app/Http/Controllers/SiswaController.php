<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Konselor;
use App\Models\KategoriPermasalahan;
use App\Models\Siswa;
use App\Models\LaporanKonseling;
use App\Models\PengajuanKonseling;
use App\Models\PesanKonseling;
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
                'id_kategori'       => 'required',
                'id_konselor'       => 'required',
                'tanggal_pengajuan' => 'required|date',
                'deskripsi_masalah' => 'nullable'
            ]);

            $user = User::find(session('id_user'));

            PengajuanKonseling::create([
                'id_siswa'          => $user->siswa->id,
                'id_kategori'       => $request->id_kategori,
                'id_konselor'       => $request->id_konselor,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'deskripsi_masalah' => $request->deskripsi_masalah ?? '-',
                'status'            => 'menunggu'
            ]);

            return response()->json(['message' => 'Pengajuan berhasil dikirim!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function pengajuan()
    {
        $kategori = KategoriPermasalahan::all();
        $user     = User::find(session('id_user'));
        $konselor = Konselor::all();
        $query    = PengajuanKonseling::with('kategori')
            ->where('id_siswa', $user->siswa->id);

        if (request('tanggal')) {
            $query->whereDate('tanggal_pengajuan', request('tanggal'));
        }

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
        $kategori = KategoriPermasalahan::all();
        $user     = User::find(session('id_user'));

        $query = PengajuanKonseling::with(['kategori', 'laporan', 'konselor', 'pesan'])
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

    // Ambil riwayat pesan untuk siswa (read-only)
    public function ambilPesan($id)
    {
        $user = User::find(session('id_user'));

        // Pastikan pengajuan ini milik siswa yang login
        PengajuanKonseling::where('id', $id)
            ->where('id_siswa', $user->siswa->id)
            ->firstOrFail();

        $pesan = PesanKonseling::where('id_pengajuan', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'isi_pesan' => $p->isi_pesan,
                    'waktu'     => Carbon::parse($p->created_at)->format('d M Y, H:i'),
                ];
            });

        return response()->json($pesan);
    }

    public function profile()
    {
        $user = User::find(session('id_user'));

        if (!$user || !$user->siswa) {
            return redirect('/login');
        }

        $siswa = $user->siswa;
        return view('siswa.profile', compact('siswa'));
    }

    public function dashboard()
    {
        $user = User::find(session('id_user'));

        if (!$user || !$user->siswa) {
            return redirect('/login');
        }

        $siswaId = $user->siswa->id;

        $total  = PengajuanKonseling::where('id_siswa', $siswaId)->count();
        $aktif  = PengajuanKonseling::where('id_siswa', $siswaId)
            ->whereIn('status', ['menunggu', 'berlangsung', 'dijadwalkan'])->count();
        $selesai = PengajuanKonseling::where('id_siswa', $siswaId)
            ->where('status', 'selesai')->count();
        $bulanIni = PengajuanKonseling::with('kategori')
            ->where('id_siswa', $siswaId)
            ->whereMonth('tanggal_pengajuan', now()->month)
            ->latest()->get();

        return view('siswa.dashboard', compact('total', 'aktif', 'selesai', 'bulanIni'));
    }
}
