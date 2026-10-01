<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengaturan aplikasi berbentuk key-value sederhana - dipakai
     * untuk nilai yang perlu bisa diubah Super Admin lewat UI kapan saja
     * (mis. kontak admin untuk lupa password), tanpa perlu ubah kode atau
     * .env dan deploy ulang.
     */
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
