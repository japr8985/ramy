<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Infrastructure\Provider;

use App\Modules\Receivables\Domain\Repositories\AccountsReceivableRepositoryInterface;
use App\Modules\Receivables\Infrastructure\Persistence\Repositories\EloquentAccountsReceivableRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class ReceivablesServiceProvider extends ServiceProvider
{
    /**
     * Bindings del Bounded Context Receivables.
     */
    public array $bindings = [
        AccountsReceivableRepositoryInterface::class => EloquentAccountsReceivableRepository::class,
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
        Route::prefix('api/receivables')
            ->middleware('api')
            ->group(__DIR__ . '/../Http/Routes/api.php');
    }
}