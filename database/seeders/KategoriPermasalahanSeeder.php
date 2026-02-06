<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPermasalahan;

class KategoriPermasalahanSeeder extends Seeder
{
    public function run(): void
    {
        KategoriPermasalahan::insert([
            [
                'nama_kategori' => 'Akademik',
            ],
            [
                'nama_kategori' => 'Pribadi',
            ],
            [
                'nama_kategori' => 'Sosial',
            ],
            [
                'nama_kategori' => 'Karier',
            ],
        ]);
    }
}
