<?php

use Illuminate\Support\Facades\Session;

Session::forget("servant:{$this->cod_servidor}");
Session::save();
Session::start();

echo "";
