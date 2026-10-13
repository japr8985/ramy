<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Infrastructure\Provider;

use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;
use App\Modules\CashRegister\Infrastructure\Persistence\Repositories\EloquentCashShiftRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class CashRegisterServiceProvider extends ServiceProvider
{
    /**
     * Bindings del Bounded Context CashRegister.
     */
    public array $bindings = [
        CashShiftRepositoryInterface::class => EloquentCashShiftRepository::class,
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
        Route::prefix('api/cash-register')
            ->middleware('api')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}