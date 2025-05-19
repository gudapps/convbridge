<?php

use App\Http\Controllers\BigCommerce\AuthController;
use App\Http\Controllers\BigCommerce\LoadController;
use App\Http\Controllers\BigCommerce\WebhookController;
use App\Livewire\BigCApp\Dashboard;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.splash');
});



// Bigcommerce App related routes
Route::prefix('bigc-app')->group(function () {

    // Open routes (no auth middleware)
    Route::get('auth/callback', [AuthController::class, 'callback']);
    Route::get('load', [LoadController::class, 'handle']);

    // Webhook route to handle the 'order.created' event
    Route::post('/webhooks/order-created', [WebhookController::class, 'handleOrderCreated'])->name('bigc.webhook.order.created')->withoutMiddleware(VerifyCsrfToken::class);
    // Webhook route to handle the 'order.statusUpdated' event
    Route::post('/webhooks/order-statusUpdated', [WebhookController::class, 'handleOrderStatusUpdated'])->name('bigc.webhook.order.statusUpdated')->withoutMiddleware(VerifyCsrfToken::class);

    // Routes that require session validation
    Route::middleware(['bigc.auth', 'allowiframe'])->group(function () {
        Route::get('dashboard', Dashboard::class);

    });
});
