<?php

namespace App\Http\Middleware;

use App\Models\IdpRequestLog;
use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
    public function terminate($request, $response)
    {
        $start_time = round(LARAVEL_START * 1000);
        $end_time = round(microtime(true) * 1000);

        IdpRequestLog::create([
            'url'             => $request->fullUrl(),
            'request_method'  => $request->method(),
            'request_header'  => json_encode($request->header()),
            'request_body'    => json_encode($request->all()),
            'ip'              => $request->ip(),
            'start_time'      => $start_time,
            'end_time'        => $end_time,
            'response_time'   => ($end_time - $start_time),
            'status_code'     => $response->getStatusCode(),
            'response_body'   => json_encode($response)
        ]);

    }
}
