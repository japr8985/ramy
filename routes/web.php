<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function ($any = null) {
    return view('app');
})->where('any', '^(?!api|build|favicon|sw\.js|manifest\.webmanifest).*$');