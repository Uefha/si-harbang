<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomentarLaporan extends Model
{
    use HasFactory;

    protected $table = 'komentar_laporan';

    protected $fillable = ['laporan_id', 'user_id', 'pesan'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
