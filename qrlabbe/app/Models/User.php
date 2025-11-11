<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne; // <-- Tambahkan ini
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- PERBAIKAN 1: Tambahkan ini

class User extends Authenticatable
{
    // PERBAIKAN 2: Tambahkan HasApiTokens
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Pastikan 'role' ada di fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // PERBAIKAN 3: Pastikan ini ada untuk hashing otomatis
        'password' => 'hashed', 
    ];

    /**
     * Definisikan relasi one-to-one ke Praktikan
     */
    public function praktikan(): HasOne
    {
        return $this->hasOne(Praktikan::class);
    }
}