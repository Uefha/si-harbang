<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetugasHarbang extends Model
{
    use HasFactory;

    protected $table = 'petugas_harbang';

    protected $fillable = ['user_id', 'nip', 'jabatan', 'no_hp', 'spesialisasi'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'petugas_harbang_id');
    }
}
