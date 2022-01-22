<?php


namespace LaravelDebug\Middleware;

use Closure;
use Symfony\Component\HttpKernel\TerminableInterface;

class RequestMonitoring implements TerminableInterface
{

    public function handle($request, Closure $next)
    {
        app('laraveldebug')->setRequest($request);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        app('laraveldebug')->terminate($request, $response);
    }
}
