<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if (is_numeric($_GET['bib'])) {
        $db = new clsBanco();
        $sql = "SELECT
                    cod_acervo_editora, nm_editora
                FROM
                    pmieducar.acervo_editora
                WHERE
                    ref_cod_biblioteca = {$_GET['bib']}
                AND
                    ativo = 1";
        $db->Consulta($sql);
        if ($db->numLinhas()) {
            while ($db->ProximoRegistro()) {
                list($cod_acervo_editora, $nm_editora) = $db->Tupla();
                $cod_acervo_editora = htmlspecialchars($cod_acervo_editora);
                $nm_editora = htmlspecialchars($nm_editora);

                echo "<acervo_editora cod_acervo_editora=\"{$cod_acervo_editora}\">{$nm_editora}</acervo_editora>\n";
            }
        }
    }
    echo '</query>';
