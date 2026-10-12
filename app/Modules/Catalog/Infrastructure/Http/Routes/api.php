<?php

declare(strict_types=1);

use App\Modules\Catalog\Infrastructure\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Unidades
    Route::get('/units', [UnitController::class, 'index'])->name('catalog.units.index');
    Route::post('/units', [UnitController::class, 'store'])->name('catalog.units.store');
});