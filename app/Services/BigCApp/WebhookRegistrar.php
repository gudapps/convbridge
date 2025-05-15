<?php

namespace App\Services\BigCApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookRegistrar
{
    public function registerOrderCreated(string $storeHash, string $accessToken): bool
    {
        // BigCommerce API URL for registering webhooks
        $endpoint = "https://api.bigcommerce.com/stores/{$storeHash}/v3/hooks";

        // Send a POST request to register the order.created webhook
        $response = Http::withHeaders([
            'X-Auth-Token' => $accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($endpoint, [
            'scope' => 'store/order/created',
            'destination' => route('bigc.webhook.order.created'),  // URL for the webhook
            'is_active' => true, // Make it active immediately
        ]);

        // Check if the registration was successful
        if ($response->successful()) {
            Log::info("Webhook successfully registered for store: {$storeHash}");
            return true;
        }

        // Log any errors if the webhook registration fails
        Log::error("Failed to register order.created webhook: " . $response->body());
        return false;
    }
}
