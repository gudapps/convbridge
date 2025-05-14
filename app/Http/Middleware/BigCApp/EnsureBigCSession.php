<?php

namespace App\Http\Middleware\BigCApp;

use Closure;
use Illuminate\Http\Request;

class EnsureBigCSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('store_hash')) {
            return abort(403, 'Unauthorized access. Please launch the app from your BigCommerce dashboard.');
        }

        return $next($request);
    }
}