<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Konselor;
use App\Models\KategoriPermasalahan;
use App\Models\Siswa;
use App\Models\PengajuanKonseling;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalKonselor' => Konselor::count(),
            'totalSiswa' => Siswa::count(),
            'totalPengajuan' => PengajuanKonseling::count(),
        ]);
    }

    public function konselor()
    {
        $konselor = Konselor::with('user')->get();
        return view('admin.konselor', compact('konselor'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:konselor,nip',
            'password' => 'required'
        ]);

        $user = User::create([
            'username' => $request->nip,
            'password' => Hash::make($request->password),
            'role' => 'konselor'
        ]);

        Konselor::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'id_user' => $user->id
        ]);

        return back()->with('success', 'Konselor berhasil ditambahkan');
    }

    public function delete($id)
    {
        $konselor = Konselor::findOrFail($id);
        $konselor->user->delete();
        $konselor->delete();

        return back()->with('success', 'Konselor berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $konselor = Konselor::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:konselor,nip,' . $id,
        ]);

        $konselor->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
        ]);

        $konselor->user->update([
            'username' => $request->nip
        ]);

        if ($request->filled('password')) {
            $konselor->user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return back()->with('success', 'Data berhasil diupdate');
    }


    public function siswa()
    {
        $siswa = Siswa::with('user')->get();
        return view('admin.siswa', compact('siswa'));
    }

    public function storeSiswa(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nis' => 'required|unique:users,username',
            'kelas' => 'required',
            'jurusan' => 'required',
            'password' => 'required'
        ]);

        $user = User::create([
            'username' => $request->nis,
            'password' => Hash::make($request->password),
            'role' => 'siswa'
        ]);

        Siswa::create([
            'id_user' => $user->id,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan
        ]);

        return back()->with('success', 'Siswa berhasil ditambahkan');
    }

    public function deleteSiswa($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->user) {
            $siswa->user->delete();
        }

        $siswa->delete();

        return back()->with('success', 'Siswa berhasil dihapus');
    }


    public function konseling()
    {
        PengajuanKonseling::where('status', 'dijadwalkan')
            ->whereDate('tanggal_pengajuan', Carbon::today())
            ->update(['status' => 'berlangsung']);

        $laporan = PengajuanKonseling::with(['siswa', 'kategori', 'laporan'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        $siswa = Siswa::all();
        $kategori = KategoriPermasalahan::all();

        return view('admin.konseling', compact('laporan', 'siswa', 'kategori'));
    }
    public function storeKonseling(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required',
            'id_kategori' => 'required',
            'tanggal_pengajuan' => 'required|date',
        ]);

        PengajuanKonseling::create([
            'id_siswa' => $request->id_siswa,
            'id_kategori' => $request->id_kategori,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'deskripsi_masalah' => $request->deskripsi_masalah ?? '-',
            'status' => 'dijadwalkan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konseling berhasil ditambahkan'
        ]);
    }
}
