<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Directive Blade: @role('super_admin') ... @endrole
        // atau @role('super_admin', 'harbang') ... @endrole untuk beberapa role.
        Blade::if('role', function (string ...$roles) {
            return auth()->check() && auth()->user()->hasRole(...$roles);
        });
    }
}
