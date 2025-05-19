<?php

namespace App\Helpers;

use App\Models\Store;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Session;
use Exception;

class BigCommerceJWT
{
    public static function verifyAndStore(string $jwt): ?object
    {
        try {
            $decoded = JWT::decode($jwt, new Key(env('BC_CLIENT_SECRET'), 'HS256'));

            $storeHash = str_replace('stores/', '', $decoded->sub) ?? null;
            $userId = $decoded->user->id ?? null;

            // Lookup store_id from the DB using store_hash
            $store = Store::where('store_hash', $storeHash)->first();

            // Store decoded values in session
            session([
                'store_hash' => $storeHash,
                'user_id' => $userId ?? null,
                'store_id' => $store?->id, // Null-safe in case store not found
            ]);

            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    /** USAGE

    * use App\Helpers\BigCommerceJWT;

    * public function handle(Request $request){
    *    $jwt = $request->query('signed_payload_jwt');
    *    $decoded = BigCommerceJWT::verifyAndStore($jwt);

    *    if (!$decoded) {
    *        return response('Invalid or expired signed_payload_jwt.', 401);
    *    }
    *    return redirect('/bigc-app/dashboard');
    *}

    */
}


