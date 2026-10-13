<?php

declare(strict_types=1);

use App\Modules\Purchasing\Infrastructure\Http\Controllers\PurchaseController;
use App\Modules\Purchasing\Infrastructure\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Proveedores
    Route::get('/suppliers/all', [SupplierController::class, 'all'])->name('suppliers.all');
    Route::get('/suppliers/stats', [SupplierController::class, 'stats'])->name('suppliers.stats');
    Route::apiResource('/suppliers', SupplierController::class);

    // Órdenes de compra y recepción
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
});