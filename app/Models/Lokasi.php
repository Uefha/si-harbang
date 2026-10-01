<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lokasi';

    protected $fillable = ['nama_lokasi', 'keterangan', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
