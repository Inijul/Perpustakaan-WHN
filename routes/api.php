<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\AktivitasController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route untuk API koleksi
Route::get('/koleksi/{kode}', [KoleksiController::class, 'show']);
Route::delete('/koleksi/{kode}', [KoleksiController::class, 'destroy']);

// Route untuk API aktivitas
Route::get('/aktivitas/{id}', [AktivitasController::class, 'show']);
