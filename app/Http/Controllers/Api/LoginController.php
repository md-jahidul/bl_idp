<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    
    /**
    * Logout Customer
    * 
    * @return \Illuminate\Http\Response
    *
    * 
    * @SWG\Post(
    *      path="/logout",
    *      operationId="logout",
    *      tags={"Customers"},
    *      summary="Cusomer Logout Api",
    *      consumes={"application/json"},
    *      produces={"application/json"},
    *      description="This api revoke customer token to make him logged out from the app.",
    *      @SWG\Parameter(
    *          name="Authorization",
    *          description="Bearer XXXXX",
    *          required=true,
    *          type="string",
    *          in="header"
    *      ),
    *
    *      @SWG\Response(response=200, description="Successful Operation"),
    *      @SWG\Response(response=400, description="Bad Request"),
    *      @SWG\Response(response=401, description="Unauthorized"),
    *      @SWG\Response(response=404, description="Resource Not Found"),
    *      @SWG\Response(response=500, description="Server Error"),
    * )
    */
    public function logout()
    {
        auth()->user()->token()->revoke();

        return response()->json([
            'message' => 'Customer logged out successfully.'
        ], 200);
    }
}
