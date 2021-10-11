<?php

return [
    // Cache config
    'cache' => [
        // Lifetime of the cache in seconds
        'ttl'=> env('REPOSITORY_CACHE_TTL', 86400),
        // Methods to cache the results of
        'methods' => [
            'all',
            'count',
            'find',
        ],
        'clear' => [
            // on action => [clear these caches]
            'create' => [
                'all',
                'count',
            ],
            'update' => [
                'all',
                'count',
                'find.{identifier}',
            ],
        ]
    ],

    'namespaces' => [
        'repository' => env('REPOSITORY_NAMESPACE', 'App\\Repositories\\'),
        'model' => env('MODEL_NAMESPACE', 'App\\Model\\'),
    ],

    'autobind' => [
        'enabled' => env('REPOSITORY_AUTOBIND', true),
        'cache' => [
            'enabled' => env('REPOSITORY_AUTOBIND_CACHE', true),
            'ttl' => env('REPOSITORY_AUTOBIND_CACHE_TTL', 86400),
        ],
    ],
];
