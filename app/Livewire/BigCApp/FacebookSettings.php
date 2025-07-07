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

        if($setting != null){
            $this->scriptStatus['main'] = StoreScript::where('store_id', $setting->store_id)
                                        ->where('provider', 'Facebook')
                                        ->where('event_type', 'main')
                                        ->exists();

            $this->scriptStatus['viewcontent'] = StoreScript::where('store_id', $setting->store_id)
                                        ->where('provider', 'Facebook')
                                        ->where('event_type', 'viewContent')
                                        ->exists();

            $this->scriptStatus['addtocart'] = StoreScript::where('store_id', $setting->store_id)
                                        ->where('provider', 'Facebook')
                                        ->where('event_type', 'addToCart')
                                        ->exists();

            $this->scriptStatus['purchase'] = StoreScript::where('store_id', $setting->store_id)
                                        ->where('provider', 'Facebook')
                                        ->where('event_type', 'purchase')
                                        ->exists();
        }

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

    private function injectPixel($pixelType){
        if (empty($this->pixel_id)) {
            session()->flash('mainPixelFail', 'Pixel ID is required.');
            return;
        }

        $store = Store::where('id',session('store_id'))->first();

        if (!$store) {
            session()->flash('mainPixelFail', 'Store not found.');
            return;
        }

        switch($pixelType){
            case "main":
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
                break;

            case "viewContent":
                $scriptContent = "<script>document.addEventListener('DOMContentLoaded',function(){if(window.BCData&&window.BCData.product_attributes){var bcProduct=BCData.product_attributes;if(typeof fbq==='function'){fbq('track','ViewContent',{content_ids:[bcProduct.sku||bcProduct.id],content_type:'product',value:bcProduct.price.sale_price_without_tax.value||bcProduct.price.without_tax.value,currency:bcProduct.price.sale_price_without_tax.currency||bcProduct.price.without_tax.currency,content_name:document.title||''});}}});</script>";
                $url = "https://api.bigcommerce.com/stores/{$store->store_hash}/v3/content/scripts";
                $response = Http::withHeaders([
                    'X-Auth-Token' => $store->access_token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($url, [
                        'name' => 'FB Pixel - ViewContent',
                        'description' => 'Triggers FB ViewContent event on product detail pages',
                        'html' => $scriptContent,
                        'auto_uninstall' => true,
                        'load_method' => 'default',
                        'location' => 'footer',
                        'visibility' => 'storefront',
                        "kind"=> "script_tag",
                        "consent_category"=> "analytics"
                    ]);
                break;

            case "addToCart":
                $scriptContent = "<script>document.addEventListener('DOMContentLoaded',function(){var btn=document.getElementById('form-action-addToCart');if(btn){btn.addEventListener('click',function(){var bcProduct=BCData.product_attributes;var qty=parseInt(document.querySelector('input[name=\\\"qty[]\\\"]')?.value||'1');fbq('track','AddToCart',{content_ids:[bcProduct.sku||bcProduct.id],content_type:'product',value:bcProduct.price.sale_price_without_tax.value||bcProduct.price.without_tax.value,currency:bcProduct.price.sale_price_without_tax.currency||bcProduct.price.without_tax.currency,content_name:document.title||'',quantity:qty});});}});</script>";
                $url = "https://api.bigcommerce.com/stores/{$store->store_hash}/v3/content/scripts";
                $response = Http::withHeaders([
                    'X-Auth-Token' => $store->access_token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($url, [
                        'name' => 'FB Pixel - Add to cart',
                        'description' => 'Triggers FB Add to cart event on product detail pages',
                        'html' => $scriptContent,
                        'auto_uninstall' => true,
                        'load_method' => 'default',
                        'location' => 'footer',
                        'visibility' => 'storefront',
                        "kind"=> "script_tag",
                        "consent_category"=> "analytics"
                    ]);
                break;

            case "purchase":
                $scriptContent = "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{$this->pixel_id}');fbq('track','PageView');(function(){try{function getCookie(name){const match=document.cookie.match(new RegExp('(^| )'+name+'=([^;]+)'));return match?match[2]:null;}setTimeout(function(){const orderId=document.querySelector('[data-test=\\\"order-confirmation-order-number-text\\\"] strong')?.innerText;const fbp=getCookie('_fbp');const fbc=getCookie('_fbc');const userAgent=navigator.userAgent;const pageUrl=window.location.href;if(orderId){fetch('https://convbridge.com/bigc-app/track',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({store_id:" .$store->id . ",order_id:orderId,fbp:fbp,fbc:fbc,user_agent:userAgent,page_url:pageUrl})});}},2000);}catch(e){console.error('ConvBridge tracking failed',e);}})();</script>";
                $url = "https://api.bigcommerce.com/stores/{$store->store_hash}/v3/content/scripts";
                $response = Http::withHeaders([
                    'X-Auth-Token' => $store->access_token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($url, [
                        'name' => 'FB Pixel - Purchase',
                        'description' => 'Triggers FB purchase event on product detail pages',
                        'html' => $scriptContent,
                        'auto_uninstall' => true,
                        'load_method' => 'default',
                        'location' => 'footer',
                        'visibility' => 'order_confirmation',
                        "kind"=> "script_tag",
                        "consent_category"=> "analytics"
                    ]);
                break;

            }

        if ($response->successful()) {
            $data = $response->json('data');

            // Save or update the script ID in store_scripts table
            StoreScript::updateOrCreate(
                [
                    'store_id' => $store->id,
                    'provider' => 'Facebook',
                    'event_type' => $pixelType
                ],
                [
                    'script_uuid' => $data['uuid'],
                ]
            );

            session()->flash($pixelType . 'PixelSuccess', 'Facebook Pixel script injected successfully.');

        } else {
            session()->flash($pixelType . 'mainPixelFail', 'Failed to inject Facebook Pixel script.');
        }
    }

    private function deletePixel($pixelType){
        $store = Store::where('id',session('store_id'))->first();
        $script = StoreScript::where('store_id', $store->id)->where('provider', 'Facebook')->where('event_type', $pixelType)->first();

        if (!$script) {
            session()->flash($pixelType . 'PixelSuccess', 'Pixel script not found.');
            return;
        }

        $url = "https://api.bigcommerce.com/stores/{$store->store_hash}/v3/content/scripts/{$script->script_uuid}";

        $response = Http::withHeaders([
            'X-Auth-Token' => $store->access_token,
            'Accept' => 'application/json',
        ])->delete($url);

        if ($response->successful()) {
            $script->delete();
            $this->scriptStatus['main'] = false;
            session()->flash($pixelType . 'PixelSuccess', 'Pixel script deleted.');
            return;
        } else {
            session()->flash($pixelType . 'PixelFail', 'Failed to delete script.');
            return;
        }
    }

    public function injectMainPixel(){
        return $this->injectPixel("main");
    }

    public function injectViewContent(){
        return $this->injectPixel("viewContent");
    }

    public function deleteMainPixel(){
        return $this->deletePixel("main");
    }

    public function deleteViewContent(){
        return $this->deletePixel("viewContent");
    }

    public function injectAddToCart(){
        return $this->injectPixel("addToCart");
    }

    public function deleteAddToCart(){
        return $this->deletePixel("addToCart");
    }

    public function injectPurchase(){
        return $this->injectPixel("purchase");
    }

    public function deletePurchase(){
        return $this->deletePixel("purchase");
    }
}
