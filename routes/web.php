<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('index'));
Route::get('/login', fn () => view('auth.login'));
Route::get('/siswa-dashboard', fn () => view('.siswa/dashboard'));
Route::get('/pengajuan-konseling', fn () => view('.siswa/pengajuan'));
Route::get('/riwayat-konseling', fn () => view('.siswa/riwayat'));
Route::get('/profil-siswa', fn () => view('.siswa/profile'));
Route::get('/konselor-dashboard', fn () => view('.konselor/dashboard'));
Route::get('/permintaan-konseling', fn () => view('.konselor/permintaan'));
Route::get('/jadwal-konseling', fn () => view('.konselor/jadwal'));
Route::get('/laporan-konseling', fn () => view('.konselor/laporan'));
