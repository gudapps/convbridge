<?php

use App\Http\Controllers\BigCommerce\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.splash');
});



// Bigcommerce App related routes
Route::get('auth/callback', [AuthController::class, 'callback']);
