<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id', 'name', 'email', 'phone', 'avatar', 'password', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function pelapor(): HasOne
    {
        return $this->hasOne(Pelapor::class);
    }

    public function petugasHarbang(): HasOne
    {
        return $this->hasOne(PetugasHarbang::class);
    }

    // ---- Helper pengecekan role, dipakai di middleware & Blade ----
    public function isSuperAdmin(): bool
    {
        return $this->role?->name === Role::SUPER_ADMIN;
    }

    public function isHarbang(): bool
    {
        return $this->role?->name === Role::HARBANG;
    }

    public function isPelapor(): bool
    {
        return $this->role?->name === Role::PELAPOR;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role?->name, $roles, true);
    }

    protected static function booted(): void
    {
        // Kolom `email` punya UNIQUE INDEX di level database (bukan cuma
        // validasi Laravel), dan index itu TIDAK peduli soft-delete - jadi
        // kalau akun dihapus (soft-delete) lalu ada yang daftar lagi pakai
        // email yang sama, insert-nya akan gagal di database meski validasi
        // Laravel sendiri sudah lolos (lihat whereNull('deleted_at') di
        // Form Request). Solusinya: begitu akun di-soft-delete, "bebaskan"
        // emailnya dengan menambah akhiran unik, supaya alamat aslinya bisa
        // dipakai lagi oleh akun baru. Riwayat aslinya tetap terlacak lewat
        // akhiran ini (bukan hilang begitu saja).
        static::deleting(function (User $user) {
            if (! $user->isForceDeleting() && ! str_contains($user->email, '::deleted-')) {
                $user->forceFill([
                    'email' => $user->email.'::deleted-'.$user->id.'-'.now()->timestamp,
                ])->saveQuietly();
            }
        });
    }
}
