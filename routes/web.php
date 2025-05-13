<?php

use App\Http\Controllers\BigCommerce\AuthController;
use App\Http\Controllers\BigCommerce\LoadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.splash');
});



// Bigcommerce App related routes
Route::prefix('bigc-app')->group(function () {
    Route::get('auth/callback', [AuthController::class, 'callback']);
    Route::get('/load', [LoadController::class, 'handle']);
});
