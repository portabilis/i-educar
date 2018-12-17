<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv(__DIR__ . '/../', '.env.testing'))->load();
} catch (Throwable $throwable) {
    //
}
