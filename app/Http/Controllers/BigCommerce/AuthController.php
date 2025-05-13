<?php

namespace App\Http\Controllers\BigCommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Store;

class AuthController extends Controller
{
    public function callback(Request $request){
        $response = Http::post('https://login.bigcommerce.com/oauth2/token', [
            'client_id' => env('BC_CLIENT_ID'),
            'client_secret' => env('BC_CLIENT_SECRET'),
            'redirect_uri' => env('BC_APP_URL') . '/auth/callback',
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'scope' => $request->scope,
            'context' => $request->context,
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'OAuth token exchange failed'], 400);
        }

        $data = $response->json();

        $storeHash = str_replace('stores/', '', $data['context']);

        Store::updateOrCreate(
            ['store_hash' => $storeHash],
            [
                'access_token' => $data['access_token'],
                'scope'        => $data['scope'],
                'context'      => $data['context'],
            ]
        );

        return redirect('/dashboard'); // or your Livewire dashboard
    }
}
