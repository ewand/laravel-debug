<?php

namespace LaravelDebug;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use LaravelDebug\Middleware\RequestMonitoring;
use Illuminate\Log\Events\MessageLogged;
use LaravelDebug\LaravelDebug;

class LaravelDebugServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/laraveldebug.php', 'laraveldebug');
        $this->app->singleton(RequestMonitoring::class);
        $this->app->singleton('inspector', function ($app) {
            return new LaravelDebug();
        });

        /*if (class_exists(MessageLogged::class)) {
            $this->app['events']->listen(MessageLogged::class, function (MessageLogged $log) {
                if ($log->level == 'error') {
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
            });
        } else {
            $this->app['events']->listen('events.*', function ($level, $message, $context) {
                \Log::info($message);
                \Log::info($context);
            });
        }*/
    }

}
