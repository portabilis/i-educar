<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['esc'])) {
        $db = new clsBanco();

        // INFRA PREDIO
        $db->Consulta("
        SELECT
            cod_infra_predio
            , nm_predio
        FROM
            pmieducar.infra_predio
        WHERE
            ativo = 1
            AND ref_cod_escola = '{$_GET['esc']}'
        ORDER BY
            nm_predio ASC
        ");

        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod, $nome) = $db->Tupla();
                echo "  <infra_predio cod_infra_predio=\"{$cod}\">{$nome}</infra_predio>\n";
            }
        }
    }
    echo '</query>';
