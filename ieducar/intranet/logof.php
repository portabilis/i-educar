<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

@session_start();
$_SESSION = array();
@session_destroy();

Auth::logout();
Session::flush();

throw new HttpResponseException(
    new RedirectResponse(url('intranet/index.php'))
);
