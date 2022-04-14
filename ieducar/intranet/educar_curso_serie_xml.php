<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['esc']) && is_numeric($_GET['cur'])) {
        $db = new clsBanco();
        $db->Consulta("SELECT c.cod_curso, c.nm_curso FROM pmieducar.curso c, pmieducar.escola_curso ec WHERE ec.ref_cod_escola = {$_GET['esc']} AND ec.ref_cod_curso = c.cod_curso AND ec.ativo = 1 AND c.ativo = 1 ORDER BY c.nm_curso ASC");
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <item>{$cod}</item>\n";
            echo "  <item>{$nome}</item>\n";
        }
    }
    echo '</query>';
