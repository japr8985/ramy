<?php

declare(strict_types=1);

namespace App\Modules\Settings\Infrastructure\Provider;

use App\Modules\Settings\Domain\Repositories\SettingRepositoryInterface;
use App\Modules\Settings\Infrastructure\Persistence\Repositories\EloquentSettingRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Bindings de dependencias.
     */
    public array $bindings = [
        SettingRepositoryInterface::class => EloquentSettingRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::prefix('api/settings')
            ->middleware('api')
            ->group(__DIR__ . '/../../Infrastructure/Http/Routes/api.php');
    }
}