<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if( is_numeric( $_GET["esc"] ) && is_numeric( $_GET["cur"] ) )
    {
        $db = new clsBanco();
        $db->Consulta( "
            SELECT
                cod_serie
                , nm_serie
            FROM
                pmieducar.serie
            WHERE ref_cod_curso = {$_GET["cur"]}
            AND ativo = 1
            AND cod_serie NOT IN ( SELECT
                                        ref_cod_serie
                                    FROM
                                        pmieducar.escola_serie
                                    WHERE
                                        ref_cod_escola = '{$_GET["esc"]}'
                                        AND ativo = 1
                                )
            ORDER BY
                nm_serie ASC
            " );
        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
        }
    }
    echo "</query>";
?>
