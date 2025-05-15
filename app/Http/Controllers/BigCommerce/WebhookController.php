<?php

namespace App\Http\Controllers\BigCommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleOrderCreated(Request $request)
    {
        // Log the incoming request to check the payload
        Log::info('Received order.created webhook', $request->all());

        // Example: Process the order data
        // You can store the order data or process it as per your requirements
        $orderData = $request->all();

        // Example: Store order data to the database (you can define your own logic)
        // Order::create($orderData);

        // Respond with a 200 status code to acknowledge the webhook receipt
        return response()->json(['status' => 'success']);
    }
}
