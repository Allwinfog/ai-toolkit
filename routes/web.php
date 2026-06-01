<?php

use Illuminate\Support\Facades\Route;

// SPA - catch all routes and serve Vue app
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
