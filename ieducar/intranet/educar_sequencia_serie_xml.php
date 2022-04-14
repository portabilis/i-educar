<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['cur']) && is_numeric($_GET['ser_dif'])) {
        $db = new clsBanco();
        $consulta = 'SELECT cod_serie, nm_serie
                    FROM pmieducar.serie s
                    INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                   WHERE ref_cod_curso = '. $_GET['cur'] . '
                    AND s.ativo = 1
                    AND cod_serie <> '. $_GET['ser_dif'] . '
                    AND es.ref_cod_escola = '. $_GET['escola'] . '
                    AND '. $_GET['ano'] . ' = ANY (es.anos_letivos)
                    ORDER BY nm_serie';
        $db->Consulta($consulta);
        while ($db->ProximoRegistro()) {
            list($serie, $nm_serie) = $db->Tupla();
            echo "  <serie cod_serie=\"$serie\">{$nm_serie}</serie>\n";
        }
    }
    echo '</query>';
