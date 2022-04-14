<?php

header('Content-type: text/xml; encoding=');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (is_numeric($_GET['esc']) && is_numeric($_GET['ser'])) {
    $db = new clsBanco();
    $db->Consulta(
        '
        SELECT to_char(hora_inicial,\'hh24:mi\'), to_char(hora_final,\'hh24:mi\'), '
        . 'to_char(hora_inicio_intervalo,\'hh24:mi\'), to_char(hora_fim_intervalo,\'hh24:mi\') '
        . "FROM pmieducar.escola_serie WHERE ref_cod_escola = '{$_GET['esc']}' AND "
        . "ref_cod_serie = '{$_GET['ser']}' AND ativo = 1"
    );

    while ($db->ProximoRegistro()) {
        list(
            $hora_inicial,
            $hora_final,
            $hora_inicio_intervalo,
            $hora_fim_intervalo
        ) = $db->Tupla();

        echo "  <item>{$hora_inicial}</item>\n";
        echo "  <item>{$hora_final}</item>\n";
        echo "  <item>{$hora_inicio_intervalo}</item>\n";
        echo "  <item>{$hora_fim_intervalo}</item>\n";
    }
}
echo '</query>';
