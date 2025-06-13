<?php

use App\Http\Controllers\BigCommerce\AuthController;
use App\Http\Controllers\BigCommerce\LoadController;
use App\Http\Controllers\BigCommerce\TrackingController;
use App\Http\Controllers\BigCommerce\WebhookController;
use App\Livewire\BigCApp\BingSettings;
use App\Livewire\BigCApp\Dashboard;
use App\Livewire\BigCApp\FacebookSettings;
use App\Livewire\BigCApp\GoogleSettings;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
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

    // Custom tracking urls
    Route::options('/track', function (Request $request) {
        $origin = $request->header('Origin');

        // You can apply validation logic here if needed.
        return response('', 204)
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });
    Route::post('/track', [TrackingController::class, 'handleOrderTracking'])->name('bigc.track.handleOrderTracking')->withoutMiddleware(VerifyCsrfToken::class);


    // Routes that require session validation
    Route::middleware(['bigc.auth', 'allowiframe'])->group(function () {
        Route::get('dashboard', Dashboard::class);

        // Routes for Conversion Settings
        Route::get('/settings/facebook', FacebookSettings::class)->name('settings.facebook');
        Route::get('/settings/google', GoogleSettings::class)->name('settings.google');
        Route::get('/settings/bing', BingSettings::class)->name('settings.bing');

    });
});
