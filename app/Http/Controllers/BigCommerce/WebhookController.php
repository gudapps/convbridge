<?php

namespace App\Http\Controllers\BigCommerce;

use App\Http\Controllers\Controller;
use App\Jobs\BigCommerce\FetchAndStoreOrderJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleOrderCreated(Request $request)
    {
        // Log the incoming request to check the payload
        Log::info('Received order.created webhook', $request->all()); //TODO: Remove this log after everything is tested multiple times

        $payload = $request->all();
        $storeHash = str_replace('stores/', '', $payload['producer']);
        $orderId = $payload['data']['id'];

        FetchAndStoreOrderJob::dispatch($storeHash, $orderId);

        // Respond with a 200 status code to acknowledge the webhook receipt
        return response()->json(['status' => 'success']);
    }

    public function handleOrderStatusUpdated(Request $request)
    {
        // Log the incoming request to check the payload
        Log::info('Received order.statusUpdated webhook', $request->all()); //TODO: Remove this log after everything is tested multiple times

        $payload = $request->all();
        $storeHash = str_replace('stores/', '', $payload['producer']);
        $orderId = $payload['data']['id'];

        FetchAndStoreOrderJob::dispatch($storeHash, $orderId);

        // Respond with a 200 status code to acknowledge the webhook receipt
        return response()->json(['status' => 'success']);
    }
}
