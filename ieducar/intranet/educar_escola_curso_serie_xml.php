<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['esc']) && is_numeric($_GET['cur'])) {
        $db = new clsBanco();
        $db->Consulta("
        SELECT
            s.cod_serie,
            s.nm_serie
        FROM
            pmieducar.serie s,
            pmieducar.escola_serie es
        WHERE
            s.ref_cod_curso = '{$_GET['cur']}'
            AND es.ref_cod_serie = s.cod_serie
            AND es.ref_cod_escola = '{$_GET['esc']}'
            AND es.ativo = 1
            AND s.ativo = 1
        ORDER BY s.nm_serie ASC
        ");
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
        }
    }
    echo '</query>';
