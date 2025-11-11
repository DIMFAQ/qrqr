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
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'npm' => 'required|string|max:20|unique:praktikans',
            'password' => 'required|string|min:3|confirmed', // Sesuai permintaan Anda
        ]);

        try {
            $user = DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password, // PERBAIKAN: Plain text
                    'role' => 'praktikan',
                ]);

                $user->praktikan()->create([
                    'npm' => $request->npm,
                ]);

                return $user;
            });

            Auth::login($user);

            $freshUser = User::with('praktikan')->find($user->id);
            
            return response()->json($freshUser, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registrasi gagal.', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            if ($user->role === 'praktikan') {
                $user->load('praktikan');
            }
            
            return response()->json($user);
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }
}