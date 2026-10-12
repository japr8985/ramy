<?php

namespace App\Modules\Identity\Infrastructure\Provider;

use Illuminate\Support\Facades\Route; // <-- Facade correcta
use Illuminate\Support\ServiceProvider;

class IdentityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registro de bindings de interfaces/repositorios si los necesitas
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api/identity')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}