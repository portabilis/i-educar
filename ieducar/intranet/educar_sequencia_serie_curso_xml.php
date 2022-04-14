<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['cur'])) {
        $db = new clsBanco();
        $consulta = "SELECT s.cod_serie
                            , s.nm_serie
                       FROM  pmieducar.serie s
                      WHERE
                            s.ativo = 1
                            AND s.ref_cod_curso = {$_GET['cur']}
                            ORDER BY 1 ASC
                        ";

        $db->Consulta($consulta);
        while ($db->ProximoRegistro()) {
            list($serie, $nm_serie) = $db->Tupla();
            if ($_GET['ser_dif'] != $serie) {
                echo "  <serie cod_serie=\"$serie\">{$nm_serie}</serie>\n";
            }
        }
    }
    echo '</query>';
