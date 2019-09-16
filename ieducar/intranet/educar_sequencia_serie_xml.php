<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

    require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if(is_numeric($_GET["cur"]) && is_numeric($_GET["ser_dif"]))
    {
        $db = new clsBanco();
        $consulta = "SELECT cod_serie, nm_serie
                       FROM pmieducar.serie
                      WHERE ref_cod_curso = ". $_GET["cur"] .
                      " AND ativo = 1
                      AND cod_serie <>" .
                      $_GET['ser_dif'] .
                      " ORDER BY nm_serie" ;
        $db->Consulta( $consulta );
        while ( $db->ProximoRegistro() )
        {
            list( $serie,$nm_serie) = $db->Tupla();
            echo "  <serie cod_serie=\"$serie\">{$nm_serie}</serie>\n";
        }
    }
    echo "</query>";
?>
