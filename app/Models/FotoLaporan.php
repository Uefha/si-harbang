<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoLaporan extends Model
{
    use HasFactory;

    protected $table = 'foto_laporan';

    protected $fillable = ['laporan_id', 'tipe', 'path', 'keterangan', 'uploaded_by'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }

    /**
     * Sejak Fase 7, foto_laporan juga bisa menyimpan file video (dokumentasi
     * langsung dari kamera). Tidak perlu kolom baru — cukup dideteksi dari
     * ekstensi file, karena path selalu berasal dari nama file asli yang diunggah.
     */
    public function getIsVideoAttribute(): bool
    {
        return in_array(strtolower(pathinfo($this->path, PATHINFO_EXTENSION)), ['mp4', 'mov', 'webm', 'avi']);
    }

    protected static function booted(): void
    {
        static::deleting(function (FotoLaporan $foto) {
            if ($foto->path) {
                \App\Services\UploadService::delete($foto->path);
            }
        });
    }
}
