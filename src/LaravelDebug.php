<?php

namespace LaravelDebug;
use Illuminate\Support\Facades\Auth;
use Illuminate\Log\Events\MessageLogged;

class LaravelDebug {

    public $enabled = true;
    private $start;
    private $name;
    private $user;

    public function __construct()
    {
        if (!config('laravel-debug.enabled')) {
            $this->enabled = false;
        }
    }

    public function setRequest($request) {
        foreach (config('laraveldebug.ignore_urls') as $pattern) {
            if ($request->is($pattern)) {
                $this->enabled = false;
                return false;
            }
        }
        $this->start = microtime(true);
        if (Auth::user()) {
            $this->user = Auth::user()->getAuthIdentifier();
        }
        $method = $request->method();
        $parts = explode('?', $_SERVER["REQUEST_URI"]);
        $part = array_shift($parts);
        $uri = trim($part, '/');
        $this->name = $method . ' /' . $uri;
    }

    public function terminate($request, $response) {
        if ($this->enabled) {
            $result = base64_encode(json_encode([
                'name' => $this->name,
                'duration' => round((microtime(true) - $this->start)*1000, 2), // milliseconds
                'user' => $this->user,
                'context' => [],
                'body' => $request->request->all(),
                'status_code' => $response->getStatusCode(),
            ]));
            $this->sendResult($result);
        }
    }

    private function sendResult($data) {
        $curl = "curl -X POST --ipv4 --max-time 5";
        $curl .= " --header \"Content-Type: application/json\"";
        $curl .= " --header \"Accept: application/json\"";
        $curl .= " --header \"LaravelDebugClient: ".config("laraveldebug.client")."\"";
        $curl .= " --data {$data} {".config("laraveldebug.url")."}";
        $cmd = "({$curl} > /dev/null 2>&1";
        $cmd .= ')&';
        proc_close(proc_open($cmd, [], $pipes));
    }

    public function setupErrorHandling() {
        if (class_exists(MessageLogged::class)) {
            $this->app['events']->listen(MessageLogged::class, function (MessageLogged $log) {
                if ($log->level == 'error') {
                    $result = base64_encode(json_encode([
                        'name' => $this->name,
                        'duration' => round((microtime(true) - $this->start)*1000, 2), // milliseconds
                        'user' => $this->user,
                        'context' => [],
                    ]));
                    $this->sendResult($result);
                }
            });
        } else {
            $this->app['events']->listen('events.*', function ($level, $message, $context) {
                \Log::info($message);
                \Log::info($context);
            });
        }
    }
}