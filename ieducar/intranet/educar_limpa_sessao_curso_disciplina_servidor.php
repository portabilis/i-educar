<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

foreach (Session::all() as $key => $value) {
    if (Str::startsWith($key, 'servant:')) {
        Session::forget($key);
    }
}

Session::save();
Session::start();

echo '';
