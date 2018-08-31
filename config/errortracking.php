<?php

return [
    'track_errors' => filter_var(env('APP_ERROR_TRACKING', false),FILTER_VALIDATE_BOOLEAN),
    'tracker_name' => env('APP_ERROR_TRACKER'),

];