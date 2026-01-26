<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('index'));
Route::get('/login', fn () => view('auth.login'));
Route::get('/siswa-dashboard', fn () => view('.siswa/dashboard'));
Route::get('/pengajuan-konseling', fn () => view('.siswa/pengajuan'));
Route::get('/riwayat-konseling', fn () => view('.siswa/riwayat'));
Route::get('/profil-siswa', fn () => view('.siswa/profile'));
