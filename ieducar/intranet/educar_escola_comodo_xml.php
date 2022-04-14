<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['esc'])) {
        $db = new clsBanco();
        $db->Consulta("
        SELECT
            cod_infra_predio_comodo,
            i.nm_predio || ' - ' || c.nm_comodo
        FROM
            pmieducar.infra_predio i,
            pmieducar.infra_predio_comodo c
        WHERE
            i.ref_cod_escola = '{$_GET['esc']}'
            AND i.cod_infra_predio = c.ref_cod_infra_predio
            AND i.ativo = 1
            AND c.ativo = 1
        ORDER BY nm_predio ASC, nm_comodo ASC
        ");
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <item>{$cod}</item>\n";
            echo "  <item>{$nome}</item>\n";
        }
    }
    echo '</query>';
