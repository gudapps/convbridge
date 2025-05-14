<?php

use App\Http\Controllers\BigCommerce\AuthController;
use App\Http\Controllers\BigCommerce\LoadController;
use App\Livewire\BigCApp\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.splash');
});



// Bigcommerce App related routes
Route::prefix('bigc-app')->group(function () {

    // Open routes (no auth middleware)
    Route::get('auth/callback', [AuthController::class, 'callback']);
    Route::get('load', [LoadController::class, 'handle']);
    
    // Routes that require session validation
    Route::middleware(['bigc.auth', 'allowiframe'])->group(function () {
        Route::get('dashboard', Dashboard::class);

    });
});
