<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBackHistory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
            'nocache' => PreventBackHistory::class,
        ]);

        // Kalau user yang SUDAH login coba buka halaman khusus tamu (login,
        // lupa password, dst.) - baik lewat URL langsung maupun tombol back
        // browser - arahkan ke dashboard, bukan tampilkan form login lagi.
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // "Page Expired" (419) bawaan Laravel itu layar error yang polos dan
        // bikin bingung - kalau sesi/token CSRF sudah tidak berlaku (dibiarkan
        // lama, ganti perangkat, dll.), arahkan balik ke halaman login dengan
        // pesan yang jelas, supaya user tinggal login lagi tanpa bingung.
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
        });
    })->create();
