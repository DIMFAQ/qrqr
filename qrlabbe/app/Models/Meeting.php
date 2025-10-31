<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $guarded = []; // Izinkan mass assignment

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}