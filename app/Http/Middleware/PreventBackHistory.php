<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Setelah logout, tombol "back" browser bisa saja menampilkan halaman
     * dashboard dari cache browser (bfcache) - kelihatan seperti masih
     * login padahal sesinya sudah tidak ada lagi (fitur di dalamnya tidak
     * akan berfungsi). Header di bawah memberi tahu browser untuk TIDAK
     * menyimpan halaman ini di cache sama sekali, jadi tombol "back" akan
     * memaksa permintaan baru ke server - yang otomatis diarahkan ke
     * halaman login oleh middleware "auth" kalau sesinya memang sudah habis.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
