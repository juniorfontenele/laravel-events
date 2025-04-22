<?php

declare(strict_types = 1);

return [
    'table_prefix' => env('EVENTS_TABLE_PREFIX', 'laravel_events_'),

    'max_days' => 180,
    'rate-limit' => [
        'enabled' => env('EVENTS_RATE_LIMIT_ENABLED', true),
        'key' => env('EVENTS_RATE_LIMIT_KEY', 'events'),
        'max_events' => env('EVENTS_RATE_LIMIT_MAX_EVENTS', 10),
        'decay_seconds' => env('EVENTS_RATE_LIMIT_DECAY_SECONDS', 60),
    ],

    'default_module' => 'core',
    'use_namespaces' => true,
    'namespace_depth' => 1,
];
