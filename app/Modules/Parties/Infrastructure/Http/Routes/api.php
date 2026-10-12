<?php

declare(strict_types=1);

use App\Modules\Parties\Infrastructure\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Clientes
    Route::get('/customers', [CustomerController::class, 'index'])->name('parties.customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('parties.customers.show');
    Route::post('/customers', [CustomerController::class, 'store'])->name('parties.customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('parties.customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('parties.customers.destroy');
});