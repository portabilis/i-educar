<?php

return [
    'api' => [
        'max_attempts' => env('RATE_LIMIT_API_MAX_ATTEMPTS', 60),
    ],
];
