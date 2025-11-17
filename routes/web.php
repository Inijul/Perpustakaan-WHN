<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

// Routes yang tidak memerlukan authentication
Route::middleware(['web', 'guest.auth'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes yang memerlukan authentication
Route::middleware(['web', 'admin.auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/aktivitas', [AktivitasController::class, 'index'])->name('aktivitas.index');
    Route::post('/aktivitas', [AktivitasController::class, 'store'])->name('aktivitas.store');
    Route::get('/aktivitas/{id_aktivitas}', [AktivitasController::class, 'show'])->name('aktivitas.show');
    Route::post('/aktivitas/{id_aktivitas}/status', [AktivitasController::class, 'updateStatus'])->name('aktivitas.update-status');
    Route::patch('/aktivitas/{id_aktivitas}/status', [AktivitasController::class, 'updateStatus'])->name('aktivitas.update-status-patch');

    Route::get('/koleksi', [KoleksiController::class, 'index'])->name('koleksi.index');
    Route::post('/koleksi', [KoleksiController::class, 'store'])->name('koleksi.store');
    Route::get('/koleksi/{kode}/edit', [KoleksiController::class, 'edit'])->name('koleksi.edit');
    Route::put('/koleksi/{kode}', [KoleksiController::class, 'update'])->name('koleksi.update');
    Route::delete('/koleksi/{kode}', [KoleksiController::class, 'destroy'])->name('koleksi.destroy');
});

// API Routes untuk koleksi
Route::get('/api/koleksi/{kode}', [KoleksiController::class, 'show']);
Route::delete('/api/koleksi/{kode}', [KoleksiController::class, 'destroy']);

// API Routes untuk mahasiswa
Route::get('/api/mahasiswa', function () {
    try {
        $response = Http::timeout(30)->get('http://backend:5000/api/mahasiswa');
        
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Failed to fetch mahasiswa data'], 500);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
    }
});

