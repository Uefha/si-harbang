<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Membatasi akses route berdasarkan role user yang login.
     * Daftar di routes/web.php:
     *   Route::middleware('role:super_admin')->group(...)
     *   Route::middleware('role:super_admin,harbang')->group(...)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if(! $user, 403);
        abort_unless($user->hasRole(...$roles), 403, 'Anda tidak memiliki akses ke halaman ini.');
        abort_unless($user->is_active, 403, 'Akun Anda dinonaktifkan. Hubungi Super Admin.');

        return $next($request);
    }
}
