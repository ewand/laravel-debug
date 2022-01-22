<?php


namespace LaravelDebug\Middleware;

use Closure;
use Symfony\Component\HttpKernel\TerminableInterface;

class RequestMonitoring implements TerminableInterface
{

    public function handle($request, Closure $next)
    {
        \LaravelDebug::setRequest($request);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        \LaravelDebug::terminate($request, $response);
    }
}
