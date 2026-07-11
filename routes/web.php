<?php

use App\Livewire\App\Dashboard;
use App\View\Components\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', \App\Livewire\Auth\Login::class)->name('login');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/products', App\Livewire\App\Product\Index::class)->name('products');
    Route::get('/products/{product}/edit', \App\Livewire\App\Product\Edit::class)->name('products.edit');
    Route::get('/sales', \App\Livewire\App\Sale\Index::class)->name('sales');
    Route::get('/customers', \App\Livewire\App\Customer\Index::class)->name('customers');
    Route::get('/suppliers', \App\Livewire\App\Supplier\Index::class)->name('suppliers');
    Route::get('/accounts-receivable', \App\Livewire\App\AccountsReceivable\Index::class)->name('accounts-receivable');
    Route::get('/settings', \App\Livewire\App\Settings\Index::class)->name('settings');
    Route::get('/categories', \App\Livewire\App\Category\Index::class)->name('categories');
    Route::get('/receivables', \App\Livewire\App\AccountsReceivable\Index::class)->name('receivables');
});