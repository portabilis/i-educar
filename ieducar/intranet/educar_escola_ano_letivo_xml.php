<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['esc'])) {
        $db = new clsBanco();
        if ($_GET['lim']) {
            $lim = 'limit 5';
        }

        if ($_GET['ano_atual']) {
            $ano_tual = " AND ano >= {$_GET['ano_atual']} ";
        }

        $db->Consulta("
                SELECT
                    ano
                FROM
                    pmieducar.escola_ano_letivo
                WHERE
                    ref_cod_escola = {$_GET['esc']} $ano_tual
                    AND ativo = 1
                ORDER BY
                    ano asc $lim
                ");
        while ($db->ProximoRegistro()) {
            list($ano) = $db->Tupla();
            echo "  <ano>{$ano}</ano>\n";
        }
    }

    echo '</query>';
