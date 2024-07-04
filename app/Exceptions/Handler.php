<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof AuthenticationException) {
                $forwardFor = $request->header('FromRequest');

                if ($forwardFor == 'password_grant') {
                    return response()->json(['status' => 'FAIL', 'status_code' => "ERR_452", 'token_status' => 'Invalid', 'token_user' => 'User', 'message' => 'Invalid user token'], 200);
                }
                if ($forwardFor == 'client_grant') {
                    return response()->json(['status' => 'FAIL', 'status_code' => "ERR_451", 'token_status' => 'Invalid', 'token_user' => 'Client', 'message' => 'Invalid client token'], 200);
                }
            }
            if ($exception instanceof AuthorizationException) {
                return response()->json((['status' => 403, 'message' => 'Insufficient privileges to perform this action']), 403);
            }
        }
        return parent::render($request, $exception);
    }
}
