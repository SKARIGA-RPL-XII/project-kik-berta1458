<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Konseling extends Model
{
    public function laporan()
    {
        return $this->hasOne(LaporanKonseling::class, 'id_pengajuan');
    }
}
