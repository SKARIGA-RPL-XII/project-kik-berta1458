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
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $konselorTerbaik = Konselor::withCount('pengajuan')
            ->orderBy('pengajuan_count', 'desc')
            ->take(5)
            ->get();


        $dataPerBulan = PengajuanKonseling::select(
            DB::raw('MONTH(tanggal_pengajuan) as bulan'),
            DB::raw('COUNT(*) as total')
        )
            ->where('status', 'selesai')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // total semua
        $totalSemua = array_sum($dataPerBulan->toArray());

        // ubah ke persen
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $jumlah = $dataPerBulan[$i] ?? 0;

            $persen = $totalSemua > 0 ? ($jumlah / $totalSemua) * 100 : 0;

            $chartData[] = round($persen, 2); // 2 angka desimal
        }

        $jadwalHariIni = PengajuanKonseling::with(['siswa', 'kategori'])
            ->whereDate('tanggal_pengajuan', Carbon::today())
            ->whereIn('status', ['dijadwalkan', 'berlangsung'])
            ->get();

        return view('admin.dashboard', [
            'totalKonselor' => Konselor::count(),
            'totalSiswa' => Siswa::count(),
            'totalPengajuan' => PengajuanKonseling::count(),
            'chartData' => $chartData,
            'konselorTerbaik' => $konselorTerbaik,
            'jadwalHariIni' => $jadwalHariIni
        ]);
    }

    public function konselor(Request $request)
    {
        $query = Konselor::with('user');

        // SEARCH
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('nip', 'like', '%' . $request->search . '%');
        }

        $konselor = $query->get();

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

    public function siswa(Request $request)
    {
        $query = Siswa::with('user');
        $jurusanList = Siswa::select('jurusan')->distinct()->pluck('jurusan');
        // FILTER KELAS (FIX)
        if ($request->kelas) {
            $query->where(function ($q) use ($request) {
                $q->where('kelas', 'like', $request->kelas . '-%')
                    ->orWhere('kelas', $request->kelas);
            });
        }

        // FILTER JURUSAN
        if ($request->jurusan) {
            $query->where('jurusan', $request->jurusan);
        }

        // SEARCH
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $siswa = $query->get();

        return view('admin.siswa', compact('siswa', 'jurusanList'));
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

    public function konseling(Request $request)
    {
        PengajuanKonseling::where('status', 'dijadwalkan')
            ->whereDate('tanggal_pengajuan', Carbon::today())
            ->update(['status' => 'berlangsung']);

        $query = PengajuanKonseling::with(['siswa', 'konselor', 'kategori', 'laporan']);

        // FILTER TANGGAL
        if ($request->tanggal) {
            $query->whereDate('tanggal_pengajuan', $request->tanggal);
        }

        // FILTER KATEGORI
        if ($request->kategori) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_kategori', $request->kategori);
            });
        }

        // FILTER KONSELOR
        if ($request->konselor) {
            $query->where('id_konselor', $request->konselor);
        }

        // SEARCH (nama siswa)
        if ($request->search) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        $laporan = $query->latest()->get();

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
