<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasLog extends Model
{
    protected $table = 'aktivitas_log';

    public $timestamps = false;

    protected $fillable = ['user_id', 'laporan_id', 'aktivitas', 'deskripsi', 'ip_address'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    /**
     * Helper singkat untuk mencatat aktivitas dari mana saja di aplikasi.
     * Contoh: AktivitasLog::catat('memverifikasi laporan', $laporan);
     */
    public static function catat(string $aktivitas, ?Laporan $laporan = null, ?string $deskripsi = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'laporan_id' => $laporan?->id,
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'ip_address' => request()?->ip(),
        ]);
    }
}
