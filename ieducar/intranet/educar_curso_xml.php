<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['ins']) && ($_GET['sem'] == 'true')) {
        $db = new clsBanco();
        $db->Consulta("
            SELECT
                cod_curso
                , nm_curso
            FROM
                pmieducar.curso
            WHERE
                ref_cod_instituicao = {$_GET['ins']}
                AND padrao_ano_escolar = 0
                AND ativo = 1
            ORDER BY
                nm_curso ASC
            ");

        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
        }
    } elseif (is_numeric($_GET['ins'])) {
        $db = new clsBanco();
        $db->Consulta("SELECT cod_curso, nm_curso,padrao_ano_escolar FROM pmieducar.curso WHERE ref_cod_instituicao = {$_GET['ins']} AND ativo = 1 ORDER BY nm_curso ASC");
        while ($db->ProximoRegistro()) {
            list($cod, $nome, $padrao) = $db->Tupla();
            echo "  <curso cod_curso=\"{$cod}\" padrao_ano_escolar=\"{$padrao}\">{$nome}</curso>\n";
        }
    } elseif (is_numeric($_GET['esc'])) {
        $sql_padrao_ano_escolar = '';
        if (is_string($_GET['padrao_ano_escolar']) && !empty($_GET['padrao_ano_escolar'])) {
            if ($_GET['padrao_ano_escolar'] == 'nao') {
                $sql_padrao_ano_escolar = ' AND c.padrao_ano_escolar = 0';
            }
        }
        $db = new clsBanco();
        $db->Consulta("SELECT
                            c.cod_curso
                            , c.nm_curso
                        FROM
                            pmieducar.curso c
                            , pmieducar.escola_curso ec
                        WHERE
                            ec.ref_cod_escola = {$_GET['esc']}
                            AND ec.ref_cod_curso = c.cod_curso
                            AND ec.ativo = 1
                            AND c.ativo = 1
                            {$sql_padrao_ano_escolar}
                        ORDER BY
                            c.nm_curso ASC");

        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
        }
    }
    echo '</query>';
