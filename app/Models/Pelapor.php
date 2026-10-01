<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelapor extends Model
{
    use HasFactory;

    protected $table = 'pelapor';

    protected $fillable = ['user_id', 'nip_nik', 'jabatan', 'no_hp', 'unit_kerja'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
