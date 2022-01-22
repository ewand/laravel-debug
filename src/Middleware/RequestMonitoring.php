<?php


namespace LaravelDebug\Middleware;

use Closure;
use LaravelDebug\LaravelDebug;

class RequestMonitoring
{

    public function handle($request, Closure $next)
    {
        app('inspector')->setRequest($request);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        app('inspector')->terminate($request, $response);
    }
}
