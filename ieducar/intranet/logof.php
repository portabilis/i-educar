<?php

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

event(new Logout('web', auth()->user()));
Session::flush();

throw new HttpResponseException(
    new RedirectResponse(url('intranet/index.php'))
);
