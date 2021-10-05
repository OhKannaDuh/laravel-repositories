<?php

return [
    // Cache config
    'cache' => [
        // Lifetime of the cache in seconds
        'ttl'=> 86400,
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
        'repository' => 'App\\Repositories\\',
        'model' => 'App\\Model\\',
    ],

    // Repository container regsiter (interface => implementation)
    'repositories' => [],
];
