<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legacy Code
    |--------------------------------------------------------------------------
    |
    | This value determines which application is running. Default is the new
    | version using Laravel structure. Another case your application is running
    | using i-Educar legacy code.
    |
    */

    'code' => env('LEGACY_CODE', true),

    /*
    |--------------------------------------------------------------------------
    | Display Errors
    |--------------------------------------------------------------------------
    |
    | This value determines if the errors that legacy code throws is showed for
    | user in debug mode.
    |
    */

    'display_errors' => env('LEGACY_DISPLAY_ERRORS', false),

    /*
    |--------------------------------------------------------------------------
    | Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path of the legacy code.
    |
    */

    'path' => env('LEGACY_PATH', 'ieducar'),

    /*
    |--------------------------------------------------------------------------
    | Migrations
    |--------------------------------------------------------------------------
    |
    | List of paths that contains migrations of the other repositories or
    | packages that works with i-Educar.
    |
    */

    'migrations' => [
        database_path('migrations/extras'),
    ],

    'env' => env('LEGACY_ENV', 'local'),

    'gtm' => env('GOOGLE_TAG_MANAGER'),
];
