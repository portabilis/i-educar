<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['bib'])) {
        $db = new clsBanco();
        $sql = "SELECT
                    cod_acervo_colecao, nm_colecao
                FROM
                    pmieducar.acervo_colecao
                WHERE
                    ref_cod_biblioteca = {$_GET['bib']}
                AND
                    ativo = 1";
        $db->Consulta($sql);
        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod_acervo_colecao, $nm_colecao) = $db->Tupla();
                $nm_colecao=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $nm_colecao);
                echo "<acervo_colecao cod_acervo_colecao=\"{$cod_acervo_colecao}\">{$nm_colecao}</acervo_colecao>\n";
            }
        }
    }
    echo '</query>';
