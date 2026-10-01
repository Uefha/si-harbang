<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fasilitas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fasilitas';

    protected $fillable = ['jenis_kerusakan_id', 'nama_fasilitas', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function jenisKerusakan(): BelongsTo
    {
        return $this->belongsTo(JenisKerusakan::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
