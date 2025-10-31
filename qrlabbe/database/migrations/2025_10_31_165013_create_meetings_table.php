<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi'); // Misal: "Praktikum Modul 1"
            $table->integer('pertemuan_ke');
            $table->date('tanggal');
            $table->time('start_at'); // Jam mulai sesi
            $table->time('end_at');   // Jam selesai sesi
            $table->integer('qr_window_menit')->default(5); // Waktu toleransi scan
            $table->boolean('is_open')->default(false); // Status sesi (dibuka/ditutup)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
