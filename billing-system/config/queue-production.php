<?php

return [
    'default' => env('QUEUE_CONNECTION', 'redis'),

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => env('QUEUE_FAIL_DRIVER', 'default'),
            'retry_after' => 90,
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => false,
        ],
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

    // Production-specific settings
    'production' => [
        'workers' => [
            'default' => [
                'connection' => 'redis',
                'queue' => ['default', 'high', 'medium'],
                'sleep' => 3,
                'tries' => 3,
                'timeout' => 60,
                'memory' => 128,
            ],
            'emails' => [
                'connection' => 'redis',
                'queue' => ['emails'],
                'sleep' => 5,
                'tries' => 5,
                'timeout' => 120,
                'memory' => 256,
            ],
            'billing' => [
                'connection' => 'redis',
                'queue' => ['billing'],
                'sleep' => 10,
                'tries' => 1,
                'timeout' => 300,
                'memory' => 512,
            ],
        ],
    ],
];