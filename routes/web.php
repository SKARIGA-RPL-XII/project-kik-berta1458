<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KonselorDashboardController;
use App\Http\Controllers\KonselorPermintaanController;
use App\Http\Controllers\KonselorJadwalController;
use App\Http\Controllers\KonselorLaporanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;


Route::get('/', fn() => view('index'));

Route::get('/profil-siswa', fn() => view('siswa/profile'));
Route::get('/riwayat-konseling', fn() => view('siswa/riwayat'));

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.proses');

Route::middleware(['auth.custom', 'role:siswa'])->group(function () {
    Route::get('/dashboard-siswa', function () {return view('siswa.dashboard');});
    Route::get('/pengajuan-konseling', [SiswaController::class, 'pengajuan']);
    Route::post('/siswa/pengajuan/store', [SiswaController::class, 'storePengajuan']);
    Route::get('/riwayat-konseling', [SiswaController::class, 'riwayat']);
});

Route::middleware(['auth.custom', 'role:konselor'])->group(function () {
    Route::get('/dashboard-konselor', [KonselorDashboardController::class, 'index'])->name('konselor.dashboard');
    Route::get('/permintaan-konseling', [KonselorPermintaanController::class, 'index'])->name('konselor.permintaan');
    Route::post('/konselor/permintaan/{id}/tolak', [KonselorPermintaanController::class, 'tolak']);
    Route::post('/konselor/permintaan/{id}/terima', [KonselorPermintaanController::class, 'terima']);
    Route::get('/jadwal-konseling', [KonselorJadwalController::class, 'index']);
    Route::get('/laporan-konseling', [KonselorLaporanController::class, 'index']);
    Route::post('/konselor/laporan/{id}/simpan', [KonselorLaporanController::class, 'simpanLaporan']);
});

Route::middleware(['auth.custom', 'role:admin'])->group(function () {
    Route::get('/dashboard-admin', [AdminController::class, 'dashboard']);
    Route::get('/konselor', [AdminController::class, 'konselor']);
    Route::post('/konselor', [AdminController::class, 'store'])->name('admin.konselor.store');
    Route::delete('/admin/konselor/{id}', [AdminController::class, 'delete'])->name('admin.konselor.delete');
    Route::get('/siswa', [AdminController::class, 'siswa']);
    Route::post('/siswa', [AdminController::class, 'storeSiswa'])->name('admin.siswa.store');
    Route::delete('/admin/siswa/{id}', [AdminController::class, 'deleteSiswa'])->name('admin.siswa.delete');
    Route::put('/admin/konselor/{id}', [AdminController::class, 'update'])->name('admin.konselor.update');
    Route::get('/konseling', [AdminController::class, 'konseling']);
    Route::post('/admin/konseling/store', [AdminController::class, 'storeKonseling']);
    Route::delete('/admin/konseling/{id}', [AdminController::class, 'deleteKonseling']);
    Route::post('/admin/laporan/simpan', [AdminController::class, 'simpanLaporan']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
