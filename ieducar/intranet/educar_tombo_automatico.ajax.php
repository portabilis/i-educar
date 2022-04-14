<?php

if (is_numeric($_GET['biblioteca'])) {
    $db = new clsBanco();
    $tombo = $db->CampoUnico("SELECT tombo_automatico FROM pmieducar.biblioteca WHERE cod_biblioteca = {$_GET['biblioteca']} AND ativo = 1");
    if (dbBool($tombo)) {
        echo 1;
    } else {
        echo 0;
    }
    die();
}
echo 0;
