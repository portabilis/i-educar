<?php

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
if (is_numeric($_GET['inst']) && is_numeric($_GET['esc']) && is_numeric($_GET['com']) && is_numeric($_GET['cur'])&& is_numeric($_GET['ser'])) {
    if (is_numeric($_GET['not_tur'])) {
        $not_turma = " AND t.cod_turma != {$_GET['not_tur']} ";
    }

    $db = new clsBanco();
    $consulta = "SELECT to_char(hora_inicial,'hh24:mm') as hora_inicial
                        ,to_char(hora_final,'hh24:mm')  as hora_final
                   FROM pmieducar.turma t
                  WHERE t.ref_cod_instituicao = {$_GET['inst']}
                    AND t.ref_ref_cod_escola = {$_GET['esc']}
                    AND t.ref_ref_cod_serie = {$_GET['ser']}
                    AND t.ref_cod_infra_predio_comodo = {$_GET['com']}
                    AND t.ref_cod_curso = {$_GET['cur']}
                    $not_turma
                    AND t.ativo = 1
                  ORDER BY 2
                    ";

    $db->Consulta($consulta);
    while ($db->ProximoRegistro()) {
        list($hora_inicial, $hora_final) = $db->Tupla();
        echo "  <item>{$hora_inicial}</item>\n";
        echo "  <item>{$hora_final}</item>\n";
    }
}
echo '</query>';
