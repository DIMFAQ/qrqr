<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QrToken;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PraktikanController extends Controller
{
    // Praktikan melakukan scan absensi
    public function scanAttendance(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        
        $user = Auth::user();
        $praktikan = $user->praktikan; // Ambil profil praktikan

        if (!$praktikan) {
            return response()->json(['message' => 'Profil praktikan tidak ditemukan.'], 404);
        }

        // 1. Cari token di database
        $qrToken = QrToken::where('token', $request->token)->first();

        // 2. Validasi Token
        if (!$qrToken || $qrToken->expired_at <= now()) {
            return response()->json(['message' => 'QR Code tidak valid atau kedaluwarsa.'], 400);
        }

        $meeting = $qrToken->meeting;
        if (!$meeting->is_open) {
            return response()->json(['message' => 'Sesi presensi ini sudah ditutup.'], 400);
        }

        // 3. Cek apakah sudah absen
        $alreadyAttended = Attendance::where('meeting_id', $meeting->id)
                                     ->where('praktikan_id', $praktikan->id)
                                     ->exists();
        if ($alreadyAttended) {
            return response()->json(['message' => 'Anda sudah melakukan presensi.'], 400);
        }

        // 4. Tentukan Status (Hadir / Terlambat)
        $scannedAt = now();
        $startTime = Carbon::parse($meeting->tanggal . ' ' . $meeting->start_at);
        $windowTime = $startTime->addMinutes($meeting->qr_window_menit);

        $status = 'terlambat'; // Default
        if ($scannedAt <= $windowTime) {
            $status = 'hadir';
        }

        // 5. Simpan Absensi
        $attendance = Attendance::create([
            'meeting_id' => $meeting->id,
            'praktikan_id' => $praktikan->id,
            'scanned_at' => $scannedAt,
            'status' => $status,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Presensi berhasil dicatat!',
            'status' => $status,
            'scanned_at' => $scannedAt,
        ], 201);
    }
}