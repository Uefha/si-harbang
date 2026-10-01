<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatus extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status';

    public $timestamps = false;

    protected $fillable = ['laporan_id', 'status_id', 'catatan', 'changed_by'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusLaporan::class, 'status_id');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
