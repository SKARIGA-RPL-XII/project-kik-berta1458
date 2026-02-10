<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KonselorDashboardController;
use App\Http\Controllers\KonselorPermintaanController;


Route::get('/', fn() => view('index'));

Route::get('/profil-siswa', fn() => view('siswa/profile'));
Route::get('/pengajuan-konseling', fn() => view('siswa/pengajuan'));
Route::get('/riwayat-konseling', fn() => view('siswa/riwayat'));

Route::get('/jadwal-konseling', fn() => view('konselor/jadwal'));
Route::get('/laporan-konseling', fn() => view('konselor/laporan'));

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.proses');

Route::middleware(['auth.custom', 'role:siswa'])->group(function () {
    Route::get('/dashboard-siswa', function () {
        return view('siswa.dashboard');
    });
});

Route::middleware(['auth.custom', 'role:konselor'])->group(function () {
    Route::get('/dashboard-konselor', [KonselorDashboardController::class, 'index'])->name('konselor.dashboard');
    Route::get('/permintaan-konseling', [KonselorPermintaanController::class, 'index'])->name('konselor.permintaan');
    Route::post('/konselor/permintaan/{id}/tolak', [KonselorPermintaanController::class, 'tolak']);
    Route::post('/konselor/permintaan/{id}/terima', [KonselorPermintaanController::class, 'terima']);
});
