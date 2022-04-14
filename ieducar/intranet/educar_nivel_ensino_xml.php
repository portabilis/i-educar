<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['ins'])) {
        $db = new clsBanco();
        $db->Consulta("
        SELECT
            cod_nivel_ensino
            , nm_nivel
        FROM
            pmieducar.nivel_ensino
        WHERE
            ativo = 1
            AND ref_cod_instituicao = '{$_GET['ins']}'
        ORDER BY
            nm_nivel ASC
        ");

        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod, $nome) = $db->Tupla();
                echo "  <nivel_ensino cod_nivel_ensino=\"{$cod}\">{$nome}</nivel_ensino>\n";
            }
        }
    }
    echo '</query>';
