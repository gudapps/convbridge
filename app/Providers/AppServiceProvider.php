<?php

namespace App\Providers;

use App\Http\Middleware\AllowIframe;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\BigCApp\EnsureBigCSession;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register BigCommerce-specific middleware
        Route::aliasMiddleware('bigc.auth', EnsureBigCSession::class);
        Route::aliasMiddleware('allowiframe', AllowIframe::class);
    }
}
