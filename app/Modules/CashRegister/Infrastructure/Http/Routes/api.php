<?php

declare(strict_types=1);

use App\Modules\CashRegister\Infrastructure\Http\Controllers\CashShiftController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/shifts', [CashShiftController::class, 'index'])->name('cash-register.shifts.index');
    Route::get('/shifts/current', [CashShiftController::class, 'current'])->name('cash-register.shifts.current');
    Route::post('/shifts/open', [CashShiftController::class, 'open'])->name('cash-register.shifts.open');
    Route::get('/shifts/{shift}/summary', [CashShiftController::class, 'summary'])->name('cash-register.shifts.summary');
    Route::post('/shifts/{shift}/movements', [CashShiftController::class, 'recordMovement'])->name('cash-register.shifts.movements');
    Route::post('/shifts/{shift}/close', [CashShiftController::class, 'close'])->name('cash-register.shifts.close');
});