<?php

use App\Exceptions\RedirectException;
use Illuminate\Support\Facades\Session;

session_start();
$_SESSION = array();
session_destroy();

Session::flush();

throw new RedirectException(url('intranet/index.php'));
