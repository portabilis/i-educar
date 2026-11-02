<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'passport' => [
        'enabled' => env('PASSPORT_ENABLED', false),
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET'),
        'redirect' => env('PASSPORT_REDIRECT_URI'),
        'host' => env('PASSPORT_HOST'),
        'authorize_uri' => env('PASSPORT_AUTHORIZE_URI', 'oauth/authorize'),
        'token_uri' => env('PASSPORT_TOKEN_URI', 'oauth/token'),
        'userinfo_uri' => env('PASSPORT_USERINFO_URI', 'api/user'),
        'guzzle' => [
            'verify' => env('PASSPORT_GUZZLE_VERIFY', true),
        ],
        'label' => env('PASSPORT_LABEL', 'Single sign-on (SSO)'),
    ],

];
