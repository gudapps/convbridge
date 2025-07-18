<?php

namespace App\Jobs\BigCommerce;

use App\Jobs\SendBingConversionJob;
use App\Jobs\SendFacebookConversionJob;
use App\Jobs\SendGoogleConversionJob;
use App\Models\ConversionSetting;
use App\Models\Store;
use App\Models\TrackedCustomer;
use App\Models\TrackedOrder;
use App\Models\TrackedOrderItem;
use App\Services\BigCApp\OrderStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchAndStoreOrderJob implements ShouldQueue
{
    use Queueable;

    protected $storeHash;
    protected $orderId;

    public function __construct($storeHash, $orderId)
    {
        $this->storeHash = $storeHash;
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        $store = Store::where('store_hash', $this->storeHash)->first();

        if (!$store) {
            Log::error("Store not found for hash: " . $this->storeHash);
            return;
        }

        $url = "https://api.bigcommerce.com/stores/{$this->storeHash}/v2/orders/{$this->orderId}";

        $response = Http::withToken($store->access_token)
            ->withHeaders([
                'X-Auth-Token' => $store->access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get($url);

        if (!$response->successful()) {
            Log::error("Failed to fetch order: {$this->orderId}");
            return;
        }

        $orderData = $response->json();

        // Save to tracked_orders
        $trackedOrder = TrackedOrder::updateOrCreate(
            [
                'order_id' => $this->orderId,
                'store_id' => $store->id
            ],
            [
                'status' => config('bigcommerce.order_statuses')[$orderData['status_id']] ?? 'Unknown',
                'status_id' => $orderData['status_id'],
                'order_data' => $orderData
            ]

        );

        // Save customer
        TrackedCustomer::updateOrCreate(
            ['tracked_order_id' => $trackedOrder->id],
            [
                'store_id' => $store->id,
                'customer_id' => $orderData['customer_id'],
                'email' => $orderData['billing_address']['email'] ?? null,
                'first_name' => $orderData['billing_address']['first_name'] ?? null,
                'last_name' => $orderData['billing_address']['last_name'] ?? null,
                'phone' => $orderData['billing_address']['phone'] ?? null,
                'country_code' => $orderData['billing_address']['country_iso2'] ?? null,
                'region' => $orderData['billing_address']['state'] ?? null,
                'city' => $orderData['billing_address']['city'] ?? null,
                'zip' => $orderData['billing_address']['zip'] ?? null,
                'address' => $orderData['billing_address']['street_1'] ?? null,
            ]
        );

        $url = "https://api.bigcommerce.com/stores/{$this->storeHash}/v2/orders/{$this->orderId}/products";

        $response = Http::withToken($store->access_token)
            ->withHeaders([
                'X-Auth-Token' => $store->access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get($url);

        if (!$response->successful()) {
            Log::error("Failed to fetch products for order: {$this->orderId}");
            return;
        }

        $products = $response->json();

        // Save order items
        foreach ($products as $item) {
            TrackedOrderItem::updateOrCreate([
                'tracked_order_id' => $trackedOrder->id,
                'product_id' => $item['product_id'],
            ],
            [
                'sku' => $item['sku'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price_inc_tax'],
                'meta' => json_encode($item),
            ]);
        }

        // If not processable, then do nothing
        if(! OrderStatus::isProcessable($orderData['status_id'])){
            return;
        }

        // Dispatch platform-specific jobs if conversion settings exist
        $settings = ConversionSetting::where('store_id', $store->id)->get()->keyBy('platform');

        if ($settings->has('facebook')) {
            SendFacebookConversionJob::dispatch($trackedOrder->id);
        }

        if ($settings->has('google')) {
            SendGoogleConversionJob::dispatch($trackedOrder->id);
        }

        if ($settings->has('bing')) {
            SendBingConversionJob::dispatch($trackedOrder->id);
        }
        return;
    }
}
