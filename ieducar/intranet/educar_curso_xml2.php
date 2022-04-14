<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['ins'])) {
        $db = new clsBanco();

        // CURSO
        $db->Consulta("
        SELECT
            cod_curso
            , nm_curso
        FROM
            pmieducar.curso
        WHERE
            ativo = 1
            AND ref_cod_instituicao = '{$_GET['ins']}'
        ORDER BY
            nm_curso ASC
        ");

        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod, $nome) = $db->Tupla();
                echo "  <curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
            }
        }
    } elseif (is_numeric($_GET['cur'])) {
        $db = new clsBanco();
        $db->Consulta("
        SELECT
            cod_curso
            , nm_curso
            , qtd_etapas
        FROM
            pmieducar.curso
        WHERE
            cod_curso = {$_GET['cur']}
        ");

        while ($db->ProximoRegistro()) {
            list($cod, $nome, $qtd_etapas) = $db->Tupla();
            echo "  <curso cod_curso=\"{$cod}\" qtd_etapas=\"{$qtd_etapas}\">{$nome}</curso>\n";
        }
    }
    echo '</query>';
