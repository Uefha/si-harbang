<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prioritas extends Model
{
    use HasFactory;

    protected $table = 'prioritas';

    protected $fillable = ['nama', 'sla_hari', 'warna_badge', 'urutan'];

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
