<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if( is_numeric( $_GET["ins"] ) )
    {
        $db = new clsBanco();
        $db->Consulta( "SELECT cod_turma_tipo, nm_tipo FROM pmieducar.turma_tipo WHERE ref_cod_instituicao = '{$_GET["ins"]}' AND ativo = 1 ORDER BY nm_tipo ASC" );
        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <item>{$cod}</item>\n";
            echo "  <item>{$nome}</item>\n";
        }
    }
    echo "</query>";
?>
