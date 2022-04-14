<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

$file = __DIR__ . '/public' . $uri;

$except = ['/', '/intranet', '/intranet/'];

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if (!in_array($uri, $except) && file_exists($file)) {
    return false;
}

require_once __DIR__.'/public/index.php';
