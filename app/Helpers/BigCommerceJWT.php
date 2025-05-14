<?php

namespace App\Helpers;

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

            // Store decoded values in session
            session([
                'store_hash' => $decoded->sub ?? null,
                'user_id' => $decoded->user->id ?? null,
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


