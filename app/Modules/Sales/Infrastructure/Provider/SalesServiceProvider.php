<?php

declare(strict_types=1);

namespace App\Modules\Sales\Infrastructure\Provider;

use App\Modules\Sales\Domain\Repositories\SaleRepositoryInterface;
use App\Modules\Sales\Infrastructure\Persistence\Repositories\EloquentSaleRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class SalesServiceProvider extends ServiceProvider
{
    /**
     * Bindings del Bounded Context Sales.
     */
    public array $bindings = [
        SaleRepositoryInterface::class => EloquentSaleRepository::class,
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
        Route::prefix('api/sales')
            ->middleware('api')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}