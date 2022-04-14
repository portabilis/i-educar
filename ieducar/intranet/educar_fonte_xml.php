<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['bib'])) {
        $db = new clsBanco();

        $db->Consulta("
        SELECT
            cod_fonte
            , nm_fonte
        FROM
            pmieducar.fonte
        WHERE
            ativo = 1
            AND ref_cod_biblioteca = '{$_GET['bib']}'
        ORDER BY
            nm_fonte ASC
        ");

        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod, $nome) = $db->Tupla();
                echo "  <fonte cod_fonte=\"{$cod}\" >{$nome}</fonte>\n";
            }
        }
    }

    echo '</query>';
