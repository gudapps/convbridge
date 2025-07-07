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
            'store_id',
            'order_id',
            'fbp',
            'fbc',
            'user_agent',
        ]);

        $data['ip'] = $request->ip(); // ← This gets the user's IP

        // Save to DB, queue to Facebook CAPI, or whatever
        Log::info('FB Order Tracking', array_merge($data));

        // Save to DB
        TrackedOrderAddition::updateOrCreate(
            ['store_id' => $data['store_id'], 'order_id' => $data['order_id']],
            $data
        );

        return response()->json(['success' => true]);
    }
}
