<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

    require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn($xmlns = 'colecoes');

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"colecoes\">\n";

    if( is_numeric( $_GET["bib"] ) )
    {
        $db = new clsBanco();
        $db->Consulta( "
        SELECT
            cod_acervo_colecao
            , nm_colecao
        FROM
            pmieducar.acervo_colecao
        WHERE
            ativo = 1
            AND ref_cod_biblioteca = '{$_GET["bib"]}'
        ORDER BY
            nm_colecao ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome) = $db->Tupla();
                $nome = str_replace('&', 'e', $nome);
                echo "  <acervo_colecao cod_colecao=\"{$cod}\" >{$nome}</acervo_colecao>\n";
            }
        }
    }
    echo "</query>";
?>
