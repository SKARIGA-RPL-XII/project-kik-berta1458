<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKonseling extends Model
{
    use HasFactory;

    protected $table = 'laporan_konseling';

    protected $fillable = [
        'id_pengajuan',
        'hasil_catatan',
        'bukti_file',
        'pesan_siswa'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKonseling::class);
    }
}
