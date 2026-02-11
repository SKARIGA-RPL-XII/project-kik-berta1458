<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KonselorDashboardController;
use App\Http\Controllers\KonselorPermintaanController;
use App\Http\Controllers\KonselorJadwalController;
use App\Http\Controllers\KonselorLaporanController;


Route::get('/', fn() => view('index'));

Route::get('/profil-siswa', fn() => view('siswa/profile'));
Route::get('/pengajuan-konseling', fn() => view('siswa/pengajuan'));
Route::get('/riwayat-konseling', fn() => view('siswa/riwayat'));

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
    Route::get('/jadwal-konseling', [KonselorJadwalController::class, 'index']);
    Route::get('/laporan-konseling',[KonselorLaporanController::class, 'index']);
    Route::post('/konselor/laporan/{id}/simpan', [KonselorLaporanController::class, 'simpanLaporan']);
});
