<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['ser'])) {
        $db = new clsBanco();
        
        $sql = "
            SELECT
                r.tipo_presenca
            FROM
                modules.regra_avaliacao_serie_ano s
            JOIN modules.regra_avaliacao r
                ON (s.regra_avaliacao_id = r.id)
            WHERE s.serie_id = {$_GET['ser']}
        ";

        $db->Consulta("{$sql}");

        $db->ProximoRegistro();
        echo "  <div id=\"tipoPresenca\">{$db->Campo('tipo_presenca')}</div>\n";
    }

    echo '</query>';
