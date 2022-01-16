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
        \Log::info('1');
        \Log::info(config('laraveldebug'));
        if(config('laraveldebug.enabled')) {
            \Log::info('2');
            foreach (config('laraveldebug.ignore_urls') as $pattern) {
                if ($request->is($pattern)) {
                    \Log::info('4');
                    return false;
                }
            }
            \Log::info('3');
            return true;
        } else {
            \Log::info('5');
            return false;
        }
        \Log::info('6');
    }

    public function handle($request, Closure $next)
    {
        if ($this->shouldCapture($request)) {
            $this->name = $request->method() . ' /' . trim(array_shift(explode('?', $_SERVER["REQUEST_URI"])), '/');
            $this->start = microtime(true);
            $this->user = Auth::user()->getAuthIdentifier();
        }
        \Log::info('handle');
        return $next($request);
    }

    public function terminate($request, $response)
    {
        \Log::info('terminate');
        if ($this->shouldCapture($request)) {
            $result = base64_encode(json_encode([
                'name' => $this->name,
                'duration' => round((microtime(true) - $this->start)*1000, 2), // milliseconds
                'user' => $this->user,
                'context' => [],
                'body' => $request->request->all(),
                'headers' => $request->headers->all(),
                'status_code' => $response->getStatusCode(),
            ]));
            \Log::info('terminate');
            $this->sendResult($result);
            
        }
    }

    private function sendResult($data) {
        $curl = config("laraveldebug.url")." -X POST --ipv4 --max-time 5";
        $curl .= " --header \"Content-Type: application/json\"";
        $curl .= " --header \"Accept: application/json\"";
        $curl .= " --header \"LaravelDebugClient: ".config("laraveldebug.client")."\"";
        $curl .= " --data {$data} {$this->config->getUrl()}";
        $cmd = "({$curl} > /dev/null 2>&1";
        $cmd .= ')&';
        proc_close(proc_open($cmd, [], $pipes));
    }
}
