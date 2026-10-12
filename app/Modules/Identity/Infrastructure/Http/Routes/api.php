<?php

use App\Modules\Identity\Infrastructure\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login'])->name('identity.login');



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [LoginController::class,'me'])->name('identity.me');
    Route::post('/logout', [LoginController::class, 'logout'])->name('identity.logout');
});