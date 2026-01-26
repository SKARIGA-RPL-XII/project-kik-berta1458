<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('index'));
Route::get('/login', fn () => view('auth.login'));
Route::get('/siswa-dashboard', fn () => view('.siswa/dashboard'));
Route::get('/pengajuan-konseling', fn () => view('.siswa/pengajuan'));
