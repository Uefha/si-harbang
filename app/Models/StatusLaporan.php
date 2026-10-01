<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusLaporan extends Model
{
    use HasFactory;

    protected $table = 'status_laporan';

    protected $fillable = ['nama', 'warna_badge', 'urutan', 'is_final'];

    // Nama status baku dipakai sebagai referensi di kode (bukan hard-code id)
    public const BARU = 'Baru';
    public const DIVERIFIKASI = 'Diverifikasi';
    public const MENUNGGU_PERBAIKAN = 'Menunggu Perbaikan';
    public const SEDANG_DIKERJAKAN = 'Sedang Dikerjakan';
    public const MENUNGGU_MATERIAL = 'Menunggu Material';
    public const SELESAI = 'Selesai';
    public const DITOLAK = 'Ditolak';

    protected function casts(): array
    {
        return ['is_final' => 'boolean'];
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'status_id');
    }
}
