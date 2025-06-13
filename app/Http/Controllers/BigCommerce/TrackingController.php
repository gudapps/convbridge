<?php

namespace App\Http\Controllers\BigCommerce;

use App\Http\Controllers\Controller;
use App\Models\TrackedOrderAddition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function handleOrderTracking(Request $request){
        $data = $request->only([
            'order_id',
            'fbp',
            'fbc',
            'user_agent',
        ]);

        // Save to DB, queue to Facebook CAPI, or whatever
        Log::info('FB Order Tracking', array_merge($data));

        // Save to DB
        TrackedOrderAddition::create($data);

        return response()->json(['success' => true]);
    }
}
