<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 40); // Baru, Diverifikasi, Menunggu Perbaikan, Sedang Dikerjakan, Menunggu Material, Selesai, Ditolak
            $table->string('warna_badge', 20)->default('secondary');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->boolean('is_final')->default(false); // true untuk Selesai & Ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_laporan');
    }
};
