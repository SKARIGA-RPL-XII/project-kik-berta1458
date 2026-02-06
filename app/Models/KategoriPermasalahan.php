<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriPermasalahan extends Model
{
    use HasFactory;

    protected $table = 'kategori_permasalahan';

    protected $fillable = [
        'nama_kategori',
    ];

    public function pengajuan()
    {
        return $this->hasMany(PengajuanKonseling::class, 'id_kategori');
    }
}

?>