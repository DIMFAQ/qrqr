<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PraktikanController;
use App\Http\Controllers\Api\AuthController; // <-- TAMBAHKAN INI

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Rute ini mengecek siapa yang sedang login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', function (Request $request) {
        return $request->user()->load('praktikan'); // <-- Ini lebih baik
    });
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

// --- RUTE AUTENTIKASI (YANG HILANG) ---
