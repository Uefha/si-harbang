<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data profil tambahan untuk user dengan role "pelapor"
        // (guru, pamong, tenaga kependidikan, petugas, dst.)
        Schema::create('pelapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nip_nik', 30)->nullable();
            $table->string('jabatan');
            $table->string('no_hp', 20);
            $table->string('unit_kerja')->nullable(); // mis. "Bagian Akademik", "Asrama Graha Putra"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelapor');
    }
};
