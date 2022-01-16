<?php


namespace LaravelDebug\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\TerminableInterface;

class RequestMonitoring implements TerminableInterface
{
    private $start;
    private $name;
    private $user;

    private function shouldCapture($request) {
        if(config('laraveldebug.enabled')) {
            foreach (config('laraveldebug.ignore_urls') as $pattern) {
                if ($request->is($pattern)) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function handle($request, Closure $next)
    {
        if ($this->shouldCapture($request)) {
            $method = $request->method();
            $parts = explode('?', $_SERVER["REQUEST_URI"]);
            $part = array_shift($parts);
            $uri = trim($part, '/');
            $this->name = $method . ' /' . $uri;
            $this->start = microtime(true);
            if (Auth::user()) {
                $this->user = Auth::user()->getAuthIdentifier();
            }
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if ($this->shouldCapture($request)) {
            $result = base64_encode(json_encode([
                'name' => $this->name,
                'duration' => round((microtime(true) - $this->start)*1000, 2), // milliseconds
                'user' => $this->user,
                'context' => [],
                'body' => $request->request->all(),
                'status_code' => $response->getStatusCode(),
            ]));
            
            \LaravelDebug::sendResult($result);
            
        }
    }
}
