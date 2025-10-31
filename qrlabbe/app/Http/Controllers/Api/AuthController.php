<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Praktikan;

class AuthController extends Controller
{
    /**
     * Logika Registrasi Praktikan (BARU)
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'npm' => 'required|string|max:20|unique:praktikans',
            'password' => 'required|string|min:8|confirmed', // butuh 'password_confirmation'
        ]);

        try {
            // 2. Gunakan Transaction, agar jika salah satu gagal, semua di-rollback
            $user = DB::transaction(function () use ($request) {
                // 3. Buat data User
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'praktikan', // <-- OTOMATIS JADI PRAKTIKAN
                ]);

                // 4. Buat data Praktikan yang terhubung
                $user->praktikan()->create([
                    'npm' => $request->npm,
                ]);

                return $user;
            });

            // 5. Langsung loginkan user
            Auth::login($user);

            // 6. Kembalikan data user (plus relasi praktikan)
            return response()->json($user->load('praktikan'), 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Registrasi gagal.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logika Login (PINDAHAN)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user()->load('praktikan'); // 'praktikan' adalah relasi
            return response()->json($user);
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Logika Logout (PINDAHAN)
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }
}