<?php

declare(strict_types=1);

use App\Modules\Catalog\Infrastructure\Http\Controllers\CategoryController;
use App\Modules\Catalog\Infrastructure\Http\Controllers\ProductController;
use App\Modules\Catalog\Infrastructure\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Unidades
    Route::get('/units', [UnitController::class, 'index'])->name('catalog.units.index');
    Route::post('/units', [UnitController::class, 'store'])->name('catalog.units.store');

    Route::get('/categories', [CategoryController::class, 'index'])->name('catalog.categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('catalog.categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('catalog.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('catalog.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('catalog.categories.destroy');

    // Productos
    Route::get('/products/kpis', [ProductController::class, 'kpis'])->name('catalog.products.kpis');
    Route::get('/products/lookup-barcode/{barcode}', [ProductController::class, 'lookupBarcode'])->name('catalog.products.lookup_barcode');
    Route::get('/products', [ProductController::class, 'index'])->name('catalog.products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('catalog.products.show');
    Route::post('/products', [ProductController::class, 'store'])->name('catalog.products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('catalog.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('catalog.products.destroy');
});