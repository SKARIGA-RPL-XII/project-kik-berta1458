<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesanKonseling extends Model
{
    protected $table = 'pesan_konseling';

    protected $fillable = [
        'id_pengajuan',
        'isi_pesan',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKonseling::class, 'id_pengajuan');
    }
}
