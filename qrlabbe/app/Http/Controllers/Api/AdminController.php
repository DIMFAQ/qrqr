<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\QrToken;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Admin membuat sesi/meeting baru
    public function createMeeting(Request $request)
    {
        $request->validate([
            'nama_sesi' => 'required|string',
            'pertemuan_ke' => 'required|integer',
            'tanggal' => 'required|date',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i',
        ]);

        $meeting = Meeting::create([
            'nama_sesi' => $request->nama_sesi,
            'pertemuan_ke' => $request->pertemuan_ke,
            'tanggal' => $request->tanggal,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'is_open' => true, // Langsung buka sesi
        ]);

        // Buat token QR pertama
        $token = $this->generateNewToken($meeting);

        return response()->json(['meeting' => $meeting, 'token' => $token->token], 201);
    }

    // Admin mengambil token QR (untuk di-refresh di frontend)
    public function getQrToken(Meeting $meeting)
    {
        // Aturan: QR rotasi cepat (misal 30 detik)
        $currentToken = $meeting->qrTokens()->latest()->first();
        
        // Jika token masih valid (kurang dari 30 detik), kembalikan token yg sama
        if ($currentToken && $currentToken->expired_at > now()) {
            return response()->json(['token' => $currentToken->token]);
        }
        
        // Jika tidak, buat token baru
        $token = $this->generateNewToken($meeting);
        return response()->json(['token' => $token->token]);
    }

    // Fungsi helper untuk generate token baru
    private function generateNewToken(Meeting $meeting)
    {
        // Expire token lama
        $meeting->qrTokens()->update(['expired_at' => now()]);

        // Buat token baru
        return QrToken::create([
            'meeting_id' => $meeting->id,
            'token' => Str::random(40),
            'expired_at' => now()->addSeconds(30), // Kadaluwarsa dalam 30 detik
        ]);
    }
}