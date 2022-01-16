<?php

return [
    'enabled' => env('LARAVEL_DEBUG_ENABLED', true),
    'client' => env('LARAVEL_DEBUG_CLIENT', 'NOT_SET'),
    'url' => env('LARAVEL_DEBUG_URL', 'https://uptime.tonic.com.au/capture'),

    //'query' => env('INSPECTOR_QUERY', true),
    //'bindings' => env('INSPECTOR_QUERY_BINDINGS', true),

    //'email' => env('INSPECTOR_EMAIL', true),
    //'notifications' => env('INSPECTOR_NOTIFICATIONS', true),
    //'views' => env('INSPECTOR_VIEWS', true),
    //'job' => env('INSPECTOR_JOB', true),
    //'unhandled_exceptions' => env('INSPECTOR_UNHANDLED_EXCEPTIONS', true),
    //'http_client' => env('INSPECTOR_HTTP_CLIENT', true),

    'ignore_urls' => [],
    //'ignore_jobs' => [],
];
