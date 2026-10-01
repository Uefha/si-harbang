<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name', 'description'];

    // Konstanta role agar tidak ada "magic string" tersebar di codebase
    public const SUPER_ADMIN = 'super_admin';
    public const HARBANG = 'harbang';
    public const PELAPOR = 'pelapor';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
