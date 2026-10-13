<?php

declare(strict_types=1);

use App\Modules\Receivables\Infrastructure\Http\Controllers\AccountsReceivableController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/receivables/stats', [AccountsReceivableController::class, 'stats'])->name('receivables.stats');
    Route::get('/receivables', [AccountsReceivableController::class, 'index'])->name('receivables.index');
    Route::get('/receivables/{id}', [AccountsReceivableController::class, 'show'])->name('receivables.show');
    Route::post('/receivables/{id}/payments', [AccountsReceivableController::class, 'recordPayment'])->name('receivables.payments.store');
});