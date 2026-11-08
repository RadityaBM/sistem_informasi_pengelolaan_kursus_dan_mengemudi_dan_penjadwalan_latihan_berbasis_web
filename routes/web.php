<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PaketKursusController;
use App\Http\Controllers\SesiController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\NoCache;
use Illuminate\Support\Facades\Route;


Route::get('/', fn() => redirect()->route('dashboard'));

// Publik
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');

// Wajib login (authentication)
Route::middleware(['auth', NoCache::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin saja (BR-06)
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('paket', PaketKursusController::class)->except('show');
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/baru', [SiswaController::class, 'create'])->name('siswa.create');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    });

    // Owner saja (UC-09, UC-10)
    Route::middleware(RoleMiddleware::class . ':owner')->prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/ekspor', [LaporanController::class, 'ekspor'])->name('laporan.ekspor');
        Route::get('/produktivitas', [LaporanController::class, 'produktivitas'])->name('laporan.produktivitas');
    });

    // Siswa saja (UC-03, UC-04, UC-05)
    Route::middleware(RoleMiddleware::class . ':siswa')->prefix('siswa')->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/booking/berhasil/{id}', [BookingController::class, 'berhasil'])->name('booking.berhasil');
        Route::get('/kuota', [BookingController::class, 'kuota'])->name('kuota');
        Route::get('/booking-saya', [BookingController::class, 'saya'])->name('booking.saya');
        Route::post('/booking/{id}/batal', [BookingController::class, 'batal'])->name('booking.batal');
    });

    // Pelatih dan Admin (UC-07, UC-08, BR-04)
    Route::middleware(RoleMiddleware::class . ':pelatih,admin')->group(function () {
        Route::get('/pelatih/jadwal', [SesiController::class, 'index'])->name('sesi.index');
        Route::post('/sesi/{id}/selesai', [SesiController::class, 'selesai'])->name('sesi.selesai');
    });
});
