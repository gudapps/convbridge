<?php

namespace App\Http\Controllers\BigCommerce;

use App\Helpers\BigCommerceJWT;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoadController extends Controller
{
    public function handle(Request $request)
    {
        $jwt = $request->query('signed_payload_jwt');

        $decoded = BigCommerceJWT::verifyAndStore($jwt);

        if (!$decoded) {
            return response('Invalid or expired signed_payload_jwt.', 401);
        }

        return redirect('/bigc-app/dashboard');
    }
}
