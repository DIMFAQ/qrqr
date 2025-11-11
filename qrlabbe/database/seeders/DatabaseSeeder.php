<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Praktikan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@lab.com'],
            [
                'name' => 'Admin',
                'password' => 'admin123', // PERBAIKAN: Plain text
                'role' => 'admin',
            ]
        );

        // 2. Buat Akun Praktikan Tes
        DB::transaction(function () {
            $praktikanUser = User::updateOrCreate(
                ['email' => 'praktikan@lab.com'],
                [
                    'name' => 'Praktikan Tes',
                    'password' => 'praktikan123', // PERBAIKAN: Plain text
                    'role' => 'praktikan',
                ]
            );

            // Buat data Praktikan-nya dan hubungkan
            $praktikanUser->praktikan()->updateOrCreate(
                ['user_id' => $praktikanUser->id],
                [
                    'npm' => '1234567890',
                ]
            );
        });
    }
}