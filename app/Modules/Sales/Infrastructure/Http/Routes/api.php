<?php

declare(strict_types=1);

use App\Modules\Sales\Infrastructure\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
});