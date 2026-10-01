<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data profil tambahan untuk user dengan role "harbang"
        Schema::create('petugas_harbang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nip', 30)->nullable();
            $table->string('jabatan');
            $table->string('no_hp', 20);
            $table->string('spesialisasi')->nullable(); // mis. "Listrik", "Bangunan", "Jaringan"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas_harbang');
    }
};
