<?php

namespace LaravelDebug;

use Illuminate\Support\ServiceProvider;
use LaravelDebug\Middleware\RequestMonitoring;
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
        $this->app->singleton('laraveldebug', function ($app) {
            $ld = new LaravelDebug();
            $ld->setupErrorHandling();
            return $ld;
        });
    }

}
