<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKonseling extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_konseling';

    protected $fillable = [
        'id_siswa',
        'id_konselor',
        'id_kategori',
        'tanggal_pengajuan',
        'deskripsi_masalah',
        'status',
        'alasan_penolakan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function konselor()
    {
        return $this->belongsTo(Konselor::class, 'id_konselor');
    }

    public function kategori()
    {
        return $this->belongsTo(\App\Models\KategoriPermasalahan::class, 'id_kategori');
    }

    public function jadwal()
    {
        return $this->hasOne(JadwalKonseling::class, 'id_pengajuan');
    }

    public function laporan()
    {
        return $this->hasOne(\App\Models\LaporanKonseling::class, 'id_pengajuan');
    }
}
