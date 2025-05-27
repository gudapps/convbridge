<?php

namespace App\Livewire\BigCApp;

use App\Models\ConversionSetting;
use App\Models\Store;
use App\Models\StoreScript;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FacebookSettings extends Component
{
    public $pixel_id;
    public $access_token;

    public $scriptStatus = [
        'main' => false,
        'pageview' => false,
        'viewcontent' => false,
        'addtocart' => false,
        'purchase' => false,
    ];

    public function mount()
    {
        $setting = ConversionSetting::where('store_id', session('store_id'))
            ->where('platform', 'facebook')->first();

        $this->pixel_id = $setting->settings['pixel_id'] ?? '';
        $this->access_token = $setting->settings['access_token'] ?? '';

        $this->scriptStatus['main'] = StoreScript::where('store_id', $setting->store_id)
                                        ->where('provider', 'Facebook')
                                        ->where('event_type', 'main_pixel')
                                        ->exists();
    }

    public function save()
    {
        ConversionSetting::updateOrCreate(
            ['store_id' => session('store_id'), 'platform' => 'facebook'],
            ['settings' => [
                'pixel_id' => $this->pixel_id,
                'access_token' => $this->access_token,
            ]]
        );

        session()->flash('success', 'Facebook settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.big-c-app.facebook-settings');
    }

    public function injectMainPixel(){

        if (empty($this->pixel_id)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pixel ID is required']); //TODO: display this on FE
            return;
        }

        $store = Store::where('id',session('store_id'))->first();

        if (!$store) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Store not found']); //TODO: display this on FE
            return;
        }

        $scriptContent = "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '{$this->pixel_id}');fbq('track', 'PageView');</script>";
        $url = "https://api.bigcommerce.com/stores/{$store->store_hash}/v3/content/scripts";

        $response = Http::withHeaders([
            'X-Auth-Token' => $store->access_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($url, [
                'name' => 'Facebook Pixel Main Script',
                'description' => 'Inject Facebook Pixel main tracking script',
                'html' => $scriptContent,
                'auto_uninstall' => true,
                'load_method' => 'default',
                'location' => 'head',
                'visibility' => 'storefront',
                "kind"=> "script_tag",
                "consent_category"=> "analytics"
            ]);


        Log::info($response);

        if ($response->successful()) {
            $data = $response->json('data');

            // Save or update the script ID in store_scripts table
            StoreScript::updateOrCreate(
                [
                    'store_id' => $store->id,
                    'provider' => 'Facebook',
                    'event_type' => 'main_pixel'
                ],
                [
                    'script_uuid' => $data['uuid'],
                ]
            );

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Facebook Pixel script injected successfully']);//TODO: display this on FE
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to inject Facebook Pixel script']);//TODO: display this on FE
        }
    }
}
