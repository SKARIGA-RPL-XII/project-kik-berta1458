<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PengajuanKonseling;

class JadwalKonseling extends Model
{
    protected $table = 'jadwal_konseling';

    protected $fillable = [
        'id_pengajuan',
        'tanggal_konseling',
        'status',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKonseling::class, 'id_pengajuan');
    }
}