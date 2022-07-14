<?php

return [

    'frontier' => [

        'endpoint' => env('FRONTIER_ENDPOINT', 'frontier'),

        'view' => env('FRONTIER_VIEW', 'frontier::index'),

        // https://laravel.com/docs/packages#views
        'views' => [
            'frontier' => env('FRONTIER_VIEWS_PATH') ? base_path(env('FRONTIER_VIEWS_PATH')) : __DIR__ . '/../resources/html',
        ],

        // https://laravel.com/docs/packages#publishing-views
        // https://laravel.com/docs/packages#publishing-file-groups
        'publishes' => [
            'frontier' => [
                __DIR__ . '/../config/frontier.php' => config_path('frontier.php'),
            ],
        ],

        // https://laravel.com/docs/middleware
        'middleware' => [
            'web',
            'ieducar.suspended',
            'auth',
            'ieducar.checkresetpassword',
        ],

        'replaces' => [
            env('FRONTIER_FIND') => env('FRONTIER_REPLACE_WITH'),
        ],

    ],

];
