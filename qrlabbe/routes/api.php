<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PraktikanController;

// Rute ini mengecek siapa yang sedang login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('praktikan');
});

// --- GRUP KHUSUS ADMIN ---
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::post('/meetings', [AdminController::class, 'createMeeting']);
    Route::get('/meetings/{meeting}/qr', [AdminController::class, 'getQrToken']);
    // Tambah rute lain untuk admin di sini (misal: lihat daftar hadir)
});

// --- GRUP KHUSUS PRAKTIKAN ---
Route::middleware(['auth:sanctum', 'role:praktikan'])->prefix('praktikan')->group(function () {
    Route::post('/attend', [PraktikanController::class, 'scanAttendance']);
    // Tambah rute lain untuk praktikan di sini (misal: lihat riwayat absen)
});