<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => '23999', 
            'password' => Hash::make('12345678'),
            'role'     => 'siswa',
        ]);

        User::create([
            'username' => '20444', 
            'password' => Hash::make('87654321'),
            'role'     => 'konselor',
        ]);
        User::create([
            'username' => 'admin',
            'password' => Hash::make('9876543'),
            'role'     => 'admin',
        ]);
    }
}
