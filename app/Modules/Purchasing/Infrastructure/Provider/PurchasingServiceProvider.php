<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Provider;

use App\Modules\Purchasing\Domain\Repositories\PurchaseRepositoryInterface;
use App\Modules\Purchasing\Domain\Repositories\SupplierRepositoryInterface;
use App\Modules\Purchasing\Infrastructure\Persistence\Repositories\EloquentPurchaseRepository;
use App\Modules\Purchasing\Infrastructure\Persistence\Repositories\EloquentSupplierRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class PurchasingServiceProvider extends ServiceProvider
{
    /**
     * Inyección de dependencias de repositorios.
     */
    public array $bindings = [
        SupplierRepositoryInterface::class => EloquentSupplierRepository::class,
        PurchaseRepositoryInterface::class => EloquentPurchaseRepository::class,
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
        Route::prefix('api/purchasing')
            ->middleware('api')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}