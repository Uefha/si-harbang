<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan', 30)->unique(); // format: HRB-YYYYMMDD-0001

            // Pelapor (relasi akun + snapshot data pada saat lapor, karena
            // jabatan/no_hp bisa saja berbeda dari profil tetap pelapor)
            $table->foreignId('pelapor_id')->constrained('pelapor')->restrictOnDelete();
            $table->string('nama_pelapor');
            $table->string('nip_nik', 30)->nullable();
            $table->string('jabatan');
            $table->string('no_hp', 20);

            // Lokasi & objek kerusakan
            $table->foreignId('gedung_id')->constrained('gedung')->restrictOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasi')->restrictOnDelete();
            $table->foreignId('jenis_kerusakan_id')->constrained('jenis_kerusakan')->restrictOnDelete();
            $table->foreignId('fasilitas_id')->nullable()->constrained('fasilitas')->nullOnDelete();
            $table->string('nama_fasilitas_lainnya')->nullable();
            $table->text('deskripsi_kerusakan');

            // Prioritas: usulan awal dari pelapor vs penetapan resmi oleh Harbang
            $table->enum('tingkat_urgensi_pelapor', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            $table->foreignId('prioritas_id')->nullable()->constrained('prioritas')->nullOnDelete();

            // Status & penugasan
            $table->foreignId('status_id')->constrained('status_laporan')->restrictOnDelete();
            $table->foreignId('petugas_harbang_id')->nullable()->constrained('petugas_harbang')->nullOnDelete();

            // Waktu kejadian & lokasi peta
            $table->date('tanggal_kejadian');
            $table->time('waktu_kejadian');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Catatan & penanganan
            $table->text('keterangan_tambahan')->nullable();
            $table->text('catatan_harbang')->nullable();
            $table->date('estimasi_selesai')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_mulai_perbaikan')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('qr_code_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status_id', 'prioritas_id']);
            $table->index('tanggal_kejadian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
