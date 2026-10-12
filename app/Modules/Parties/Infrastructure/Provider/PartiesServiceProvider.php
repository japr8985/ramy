<?php

declare(strict_types=1);

namespace App\Modules\Parties\Infrastructure\Provider;

use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;
use App\Modules\Parties\Infrastructure\Persistence\Repositories\EloquentCustomerRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class PartiesServiceProvider extends ServiceProvider
{
    /**
     * Bindings del Bounded Context Parties.
     */
    public array $bindings = [
        CustomerRepositoryInterface::class => EloquentCustomerRepository::class,
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
        Route::prefix('api/parties')
            ->middleware('api')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}