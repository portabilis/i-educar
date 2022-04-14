<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['ins'])) {
        $db = new clsBanco();

        // ESCOLA REDE ENSINO
        $db->Consulta("
        SELECT
            cod_escola_rede_ensino
            , nm_rede
        FROM
            pmieducar.escola_rede_ensino
        WHERE
            ativo = 1
            AND ref_cod_instituicao = '{$_GET['ins']}'
        ORDER BY
            nm_rede ASC
        ");

        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod, $nome) = $db->Tupla();
                echo "  <escola_rede_ensino cod_escola_rede_ensino=\"{$cod}\">{$nome}</escola_rede_ensino>\n";
            }
        }
    }
    echo '</query>';
