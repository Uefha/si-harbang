<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisKerusakan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_kerusakan';

    protected $fillable = ['nama_jenis', 'icon', 'deskripsi', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function fasilitas(): HasMany
    {
        return $this->hasMany(Fasilitas::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
