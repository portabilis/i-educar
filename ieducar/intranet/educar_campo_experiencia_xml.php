<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    $tur = $_GET['tur'];
    if (is_numeric($tur)) {
        $db = new clsBanco();
        
        $sql = "
            SELECT
                CASE
                    WHEN t.etapa_educacenso = 1 THEN 1
                    WHEN t.etapa_educacenso = 2 THEN 1
                    WHEN t.etapa_educacenso = 3 THEN 1
                    ELSE 0
                END
            FROM
                pmieducar.turma as t
            WHERE t.cod_turma = {$tur}
        ";

        $db->Consulta($sql);
        $db->ProximoRegistro();

        echo " <ce resp=\"{$db->Tupla()[0]}\"></ce>\n";
    }

    echo '</query>';
