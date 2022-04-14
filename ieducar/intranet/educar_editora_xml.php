<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn('colecoes');

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"colecoes\">\n";

    if (is_numeric($_GET['bib'])) {
        $db = new clsBanco();
        $db->Consulta("
        SELECT
            cod_acervo_editora,
            nm_editora
        FROM pmieducar.acervo_editora
       WHERE
            ativo = 1
            AND ref_cod_biblioteca = '{$_GET['bib']}'
        ORDER BY
            nm_editora ASC
        ");

        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod, $nome) = $db->Tupla();
                $nome = str_replace('&', 'e', $nome);
                echo "  <acervo_editora cod_editora=\"{$cod}\" >{$nome}</acervo_editora>\n";
            }
        }
    }
    echo '</query>';
