<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['key', 'value'];

    // Kunci baku yang dipakai aplikasi - biar tidak ada "magic string" tersebar
    public const KONTAK_ADMIN_NAMA = 'kontak_admin_nama';
    public const KONTAK_ADMIN_TELEPON = 'kontak_admin_telepon';
    public const KONTAK_ADMIN_PESAN = 'kontak_admin_pesan';

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("pengaturan:{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("pengaturan:{$key}");
    }

    /**
     * Format nomor telepon Indonesia (mis. "0812..." atau "+62812...")
     * menjadi format yang dipakai link wa.me (62812..., tanpa + atau 0 di depan).
     */
    public static function nomorWhatsApp(): ?string
    {
        $nomor = static::get(self::KONTAK_ADMIN_TELEPON);

        if (! $nomor) {
            return null;
        }

        $digit = preg_replace('/\D/', '', $nomor);

        if (str_starts_with($digit, '0')) {
            $digit = '62'.substr($digit, 1);
        } elseif (! str_starts_with($digit, '62')) {
            $digit = '62'.$digit;
        }

        return $digit;
    }
}
