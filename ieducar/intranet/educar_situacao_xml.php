<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if( is_numeric( $_GET["bib"] ) )
    {
        $db = new clsBanco();

        // SITUACAO
        $db->Consulta( "
        SELECT
            cod_situacao
            , nm_situacao
            , situacao_padrao
            , situacao_emprestada
        FROM
            pmieducar.situacao
        WHERE
            ativo = 1
            AND ref_cod_biblioteca = '{$_GET["bib"]}'
        ORDER BY
            nm_situacao ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome, $padrao, $emprestada ) = $db->Tupla();
                echo "  <situacao cod_situacao=\"{$cod}\" situacao_padrao=\"{$padrao}\" situacao_emprestada=\"{$emprestada}\">{$nome}</situacao>\n";
            }
        }
    }

    echo "</query>";
?>
