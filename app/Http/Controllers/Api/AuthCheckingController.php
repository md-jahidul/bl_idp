<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 9/24/19
 * Time: 4:56 PM
 */

namespace App\Http\Controllers\Api;

use App\Models\Scope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\TokenRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Guards\TokenGuard;
use App\Http\Requests\TokenCheckRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lcobucci\JWT\Parser;

/**
 * @SWG\Swagger(
 *     basePath="/api/",
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger API",
 *         description="Swagger API description",
 *         @SWG\Contact(
 *             email="arifulislam@bs-23.net"
 *         ),
 *     )
 * )
 */
class AuthCheckingController extends Controller
{

    private $tokenRepository;

    /**
     * AuthCheckingController constructor.
     * @param $tokenRepository TokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }


    /**
     * Checking Client Token
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     *
     * @SWG\Post(
     *      path="/check/client/token",
     *      operationId="checkClient",
     *      tags={"Token Authentication"},
     *      summary="Checking Client Token",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      description="This api checks whether a client token is authenticated or not",
     *      @SWG\Parameter(
     *         description="Body data",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             properties={
     *               @SWG\Property(property="token", type="string", description="XXXXXXX"),
     *             },
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="authorization header token",
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
    public function checkClient(Request $request)
    {
        $validator = Validator::make($request->only('token'),
            ['token' => 'string|required']
        );

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'error' => 'invalid_request',
                'error_description' => 'token field is required string',
                'message' => 'token field is required string'
            ], Response::HTTP_BAD_REQUEST));
        }

        $token = $request->token;
        $data = new \stdClass();
        $data->token = $token;

        $newRequest = Request::create('/api/v1/check-client-token');
        $request->headers->set('Authorization', $token);
        $newRequest->headers->set('Authorization', $token);
        $newRequest->headers->set('Content-Type', 'application/json');
        $newRequest->headers->set('Accept', 'application/json');
        $newRequest->headers->set('FromRequest', 'client_grant');

        return Route::dispatch($newRequest);
    }

    public function checkCusClientAuthorization(Request $request)
    {
        Validator::make($request->all(),
            [
                'client_token' => 'string|required',
                'customer_token' => 'string|required',
                'scope' => 'string|required'
            ]
        )->validate();

        $token = $request->client_token;

        $newRequest = Request::create('/api/v1/check/client/authorization', 'POST', $request->all());
        $request->headers->set('Authorization', $token);
        $newRequest->headers->set('Authorization', $token);
        $newRequest->headers->set('Content-Type', 'application/json');
        $newRequest->headers->set('Accept', 'application/json');
        $newRequest->headers->set('FromRequest', 'client_grant');

        return Route::dispatch($newRequest);
    }

    public function checkClientAuthorization(Request $request)
    {
        $clientToken = $request->get('client_token');
        $scopeStatus = $this->checkClientScope($clientToken, $request->get('scope'));

        if ($scopeStatus['status'] == 'FAIL') {
            return response()->json(['status' => 'FAIL', 'status_code' => "ERR_453", 'scope_status' => 'Invalid',
                'message' => 'Provided scope is not valid for the client'], 200);
        }


        // Request for customer checking
        $newRequest = Request::create('/api/v1/check-authorization');
        $request->headers->set('Authorization', $request->get('customer_token'));
        $newRequest->headers->set('Content-Type', 'application/json');
        $newRequest->headers->set('Accept', 'application/json');
        $newRequest->headers->set('FromRequest', 'password_grant');

        return Route::dispatch($newRequest);
    }

    public function checkClientScope($clientToken, $scope)
    {
        $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $clientToken));
        $token = (new Parser())->parse($jwt);
        $tokenData = $this->tokenRepository->find($token->getClaim('jti'));

        $client = $tokenData->client;

        $scope = Scope::where('scope', $scope)->where('resource_server', 'API Hub')->first();

        if (!$scope) {
            throw new HttpResponseException(response()->json([
                'error' => 'Provided scope is unavailable',
                'error_description' => 'Provided scope is unavailable',
                'message' => 'Provided scope is unavailable'
            ], Response::HTTP_BAD_REQUEST));
        }

        $userId = $client->user_id;

        $clientScope = DB::table('client_scope')->where('client_user_id', $userId)
            ->where('scope_id', $scope->id)->first();


        if ($clientScope) {
           return ['status' => 'SUCCESS', 'clientScope' => $clientScope];
        } else {
            return ['status' => 'FAIL'];
        }
    }


    /**
     * Checking Client Token
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     *
     * @SWG\Post(
     *      path="/check/user/token",
     *      operationId="checkPasswordGrantToken",
     *      tags={"Token Authentication"},
     *      summary="Checking User Token",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      description="This api checks whether a user token is authenticated or not",
     *      @SWG\Parameter(
     *         description="Body data",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             properties={
     *               @SWG\Property(property="token", type="string", description="Bearer XXXXXXX"),
     *             },
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="authorization header token",
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
    public function checkPasswordGrantToken(Request $request)
    {
        $validator = Validator::make($request->only('token'),
            ['token' => 'string|required']
        );

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'error' => 'invalid_request',
                'error_description' => 'token field is required string',
                'message' => 'token field is required string'
            ], Response::HTTP_BAD_REQUEST));
        }

        $token = $request->token;
        $data = new \stdClass();
        $data->token = $token;

        $newRequest = Request::create('/api/v1/check-authorization');
        $request->headers->set('Authorization', $token);
        $newRequest->headers->set('Content-Type', 'application/json');
        $newRequest->headers->set('Accept', 'application/json');
        $newRequest->headers->set('FromRequest', 'password_grant');

        return Route::dispatch($newRequest);

    }

    public function checkPass(Request $request)
    {
        $data = new \stdClass();
        $data->token_status = 'Valid';

        return response()->json($data, 200);
    }
}
