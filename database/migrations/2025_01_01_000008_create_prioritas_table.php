<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prioritas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 30); // Rendah | Sedang | Tinggi | Darurat
            $table->unsignedSmallInteger('sla_hari'); // batas waktu penanganan dalam hari
            $table->string('warna_badge', 20)->default('secondary'); // kelas warna Bootstrap
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prioritas');
    }
};
