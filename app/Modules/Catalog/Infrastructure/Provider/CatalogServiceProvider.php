<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Provider;

use App\Modules\Catalog\Domain\Repositories\UnitRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Persistence\Repositories\EloquentUnitRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class CatalogServiceProvider extends ServiceProvider
{
    /**
     * Bindings de dependencias.
     */
    public array $bindings = [
        UnitRepositoryInterface::class => EloquentUnitRepository::class,
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
        Route::prefix('api/catalog')
            ->middleware('api')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}