<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Konselor extends Model
{
    use HasFactory;

    protected $table = 'konselor';

    protected $fillable = [
        'id_user',
        'nip',
        'nama',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan()
    {
        return $this->hasMany(PengajuanKonseling::class);
    }
}
?>