<?php

use Illuminate\Support\Facades\Session;

Session::forget([
    'cursos_disciplina',
    'cursos_servidor',
    'cod_servidor',
]);
Session::save();
Session::start();

echo "";
