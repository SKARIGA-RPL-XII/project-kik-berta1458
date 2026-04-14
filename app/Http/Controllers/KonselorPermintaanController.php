<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonseling;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KonselorPermintaanController extends Controller
{
    public function index(Request $request)
    {
        $konselor = \App\Models\Konselor::where('id_user', session('id_user'))->first();
        $kategori = \App\Models\KategoriPermasalahan::all();

        $query = PengajuanKonseling::with(['siswa', 'kategori'])
            ->where('status', 'menunggu')
            ->where('id_konselor', $konselor->id);

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

        $data = $query->orderBy('tanggal_pengajuan', 'asc')->get();

        return view('konselor.permintaan', compact('data', 'kategori'));
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = PengajuanKonseling::findOrFail($id);

        $pengajuan->status = 'ditolak';
        $pengajuan->alasan_penolakan = $request->alasan_penolakan;
        $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil ditolak.'
        ]);
    }

    public function terima($id)
    {
        $pengajuan = PengajuanKonseling::findOrFail($id);

        $user = \App\Models\User::find(session('id_user'));
        $idKonselor = $user->konselor->id;

        // Update status pengajuan
        $pengajuan->status = 'dijadwalkan';
        $pengajuan->id_konselor = $idKonselor;
        $pengajuan->save();

        $sudahAda = DB::table('jadwal_konseling')
            ->where('id_pengajuan', $id)
            ->exists();

        if (!$sudahAda) {
            DB::table('jadwal_konseling')->insert([
                'id_pengajuan'      => $id,
                'tanggal_konseling' => $pengajuan->tanggal_pengajuan,
                'status'            => 'dijadwalkan',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Pengajuan berhasil diterima!"
        ]);
    }
}