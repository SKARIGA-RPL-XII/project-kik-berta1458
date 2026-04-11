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

        $laporan = PengajuanKonseling::with(['siswa', 'konselor', 'kategori', 'laporan'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        $siswa = Siswa::all();
        $kategori = KategoriPermasalahan::all();
        $konselor = Konselor::all();

        return view('admin.konseling', compact('laporan', 'siswa', 'konselor', 'kategori'));
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
    public function simpanLaporan(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id_pengajuan' => 'required',
                'hasil_catatan' => 'required',
                'pesan_siswa' => 'nullable',
                'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $laporan = LaporanKonseling::where('id_pengajuan', $request->id_pengajuan)->first();

            $path = $laporan->bukti_file ?? null;

            if ($request->hasFile('bukti_file')) {

                if ($laporan && $laporan->bukti_file) {
                    Storage::disk('public')->delete($laporan->bukti_file);
                }

                $path = $request->file('bukti_file')->store('bukti_konseling', 'public');
            }

            if ($laporan) {
                $laporan->update([
                    'hasil_catatan' => $request->hasil_catatan,
                    'pesan_siswa' => $request->pesan_siswa,
                    'bukti_file' => $path
                ]);
            } else {
                LaporanKonseling::create([
                    'id_pengajuan' => $request->id_pengajuan,
                    'hasil_catatan' => $request->hasil_catatan,
                    'pesan_siswa' => $request->pesan_siswa,
                    'bukti_file' => $path
                ]);
            }

            return response()->json([
                'message' => 'Laporan berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateKonselor(Request $request)
    {
        $request->validate([
            'id_pengajuan' => 'required',
            'id_konselor' => 'required'
        ]);

        $pengajuan = PengajuanKonseling::findOrFail($request->id_pengajuan);

        // 🚨 PROTEKSI STATUS
        if ($pengajuan->status !== 'menunggu') {
            return response()->json([
                'message' => 'Hanya pengajuan dengan status menunggu yang bisa diubah!'
            ], 403);
        }

        $pengajuan->id_konselor = $request->id_konselor;
        $pengajuan->save();

        return response()->json([
            'message' => 'Konselor berhasil diganti'
        ]);
    }

    public function deleteKonseling($id)
    {
        $pengajuan = PengajuanKonseling::findOrFail($id);
        $laporan = LaporanKonseling::where('id_pengajuan', $id)->first();
        if ($laporan) {
            if ($laporan->bukti_file) {
                Storage::disk('public')->delete($laporan->bukti_file);
            }
            $laporan->delete();
        }

        $pengajuan->delete();

        return response()->json([
            'message' => 'Data konseling berhasil dihapus'
        ]);
    }
}
