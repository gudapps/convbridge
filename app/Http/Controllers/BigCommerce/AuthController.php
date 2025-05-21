<?php

namespace App\Http\Controllers\BigCommerce;

use App\Services\BigCApp\WebhookRegistrar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Store;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    protected $webhookRegistrar;

    // Inject WebhookRegistrar
    public function __construct(WebhookRegistrar $webhookRegistrar)
    {
        $this->webhookRegistrar = $webhookRegistrar;
    }

    public function callback(Request $request){

        // Step 1: Get OAuth token from BigCommerce
        $response = Http::post('https://login.bigcommerce.com/oauth2/token', [
            'client_id' => env('BC_CLIENT_ID'),
            'client_secret' => env('BC_CLIENT_SECRET'),
            'redirect_uri' => env('BC_APP_URL') . '/auth/callback',
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'scope' => $request->scope,
            'context' => $request->context,
        ]);

        // Step 2: Check for successful response
        if (!$response->successful()) {
            return response()->json(['error' => 'OAuth token exchange failed'], 400);
        }

        // Step 3: Extract store_hash and access_token from the response
        $data = $response->json();
        $storeHash = str_replace('stores/', '', $data['context']);
        $accessToken = $data['access_token'];

        // Step 4: Fetch store URL from BigCommerce Store API
        $storeResponse = Http::withHeaders([
            'X-Auth-Token' => $accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get("https://api.bigcommerce.com/stores/$storeHash/v2/store");

        if (!$storeResponse->successful()) {
            Log::info("Failed to fetch store info: {$storeHash}");
            return response()->json(['error' => 'Failed to fetch store info'], 400);
        }
        $storeData = $storeResponse->json();

        // Step 5: Save the store's information in your database
        Store::updateOrCreate(
            ['store_hash' => $storeHash],
            [
                'access_token' => $accessToken,
                'scope'        => $data['scope'],
                'context'      => $data['context'],
                'store_url'    => $storeData['secure_url'],
                'store_name'   => $storeData['name'],
                'store_data'   => $storeData,
            ]
        );

        // Step 6: Register the webhook after storing the store data
        $this->webhookRegistrar->registerOrderCreated($storeHash, $data['access_token']);
        $this->webhookRegistrar->registerOrderStatusUpdatedWebhook($storeHash, $data['access_token']);

        // Step 7: Redirect to dashboard or another route
        return redirect('bigc-app/dashboard'); // or your Livewire dashboard
    }
}
