<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKonseling extends Model
{
    use HasFactory;

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
