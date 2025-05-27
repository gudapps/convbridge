<?php

namespace App\Jobs;

use App\Models\ConversionLog;
use App\Models\ConversionSetting;
use App\Models\Store;
use App\Models\TrackedOrder;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFacebookConversionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $trackedOrderId){
        $this->onQueue('fb');
    }

    public function handle(): void
    {
        $trackedOrder = TrackedOrder::where('id', $this->trackedOrderId)->first();

        if(! $trackedOrder || ! $trackedOrder->customer){
            Log::warning("Order or customer not found for FB CAPI: store={$trackedOrder->store_id}, order={$trackedOrder->id}");
            return;
        }

        // Loading store table data
        $store = Store::where('id', $trackedOrder->store_id)->first();
        if(!$store){
            Log::warning("Store settings not found for storeId {$trackedOrder->store_id}");
            return;
        }

        // Loading conversion settings table data
        $settings = ConversionSetting::where([
            'store_id' => $trackedOrder->store_id,
            'platform' => 'facebook',
        ])->first();

        if(! $settings){
            Log::warning("Facebook settings not found for store {$trackedOrder->store_id}");
            return;
        }

        $pixelId = $settings->settings['pixel_id'] ?? null;
        $accessToken = $settings->settings['access_token'] ?? null;

        if(!$pixelId || !$accessToken){
            Log::error("Missing FB Pixel ID or access token for conversion setting id {$settings->id}");
            return;
        }

        $customer = $trackedOrder->customer;

        $payload = [
            'data' => [[
                'event_name' => 'Purchase',
                'event_time' => Carbon::parse($trackedOrder->order_data['date_created'])->timestamp,
                'event_id' => 'order_' . $trackedOrder->order_id,
                'action_source' => 'website',
                'event_source_url' => $store->store_url . '/checkout/order-confirmation',
                'user_data' => [
                    'em' => [hash('sha256', strtolower($customer->email ?? ''))],
                    'ph' => [hash('sha256', preg_replace('/\D/', '', $customer->phone ?? ''))],
                    'fn' => [hash('sha256', strtolower($customer->first_name ?? ''))],
                    'ln' => [hash('sha256', strtolower($customer->last_name ?? ''))],
                    'zip' => [hash('sha256', $customer->zip ?? '')],
                    'ct' => [hash('sha256', strtolower($customer->city ?? ''))],
                    'st' => [hash('sha256', strtolower($customer->region ?? ''))],
                    'country' => [hash('sha256', strtolower($customer->country_code ?? ''))],
                    'client_ip_address' => $trackedOrder->order_data['ip_address'],
                    'external_id' => $trackedOrder->id,
                ],
                'custom_data' => [
                    'currency' => 'USD', // You may want to make this dynamic
                    'value' => $trackedOrder->order_data['total_inc_tax'] ?? 0,
                    'order_id' => $trackedOrder->order_id,
                    'content_type' => 'product',
                    'content_ids' => $trackedOrder->items->pluck('product_id')->toArray(),
                    'contents' => $trackedOrder->items->map(function ($item) {
                                        return [
                                            'id'       => $item->product_id,
                                            'quantity' => $item->quantity,
                                            'item_price' => $item->price,
                                        ];
                                    })->toArray(),
                ]
            ]],
            // 'test_event_code' => "TEST88997",
        ];

        $response = Http::post("https://graph.facebook.com/v17.0/{$pixelId}/events?access_token={$accessToken}", $payload);

        ConversionLog::create([
            'store_id' => $trackedOrder->store_id,
            'order_id' => $trackedOrder->order_id,
            'platform' => 'facebook',
            'status' => $response->successful() ? 'success' : 'failed',
            'response' => $response->body(),
            'sent_at' => now(),
        ]);


    }
}
