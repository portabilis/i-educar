<?php

    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

    if( is_numeric( $_GET["esc"] ) )
    {
        $db = new clsBanco();

        // INFRA COMODO FUNCAO
        $db->Consulta( "
        SELECT
            cod_infra_comodo_funcao
            , nm_funcao
        FROM
            pmieducar.infra_comodo_funcao
        WHERE
            ativo = 1
            AND ref_cod_escola = '{$_GET["esc"]}'
        ORDER BY
            nm_funcao ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome ) = $db->Tupla();
                echo "  <infra_comodo_funcao cod_infra_comodo_funcao=\"{$cod}\">{$nome}</infra_comodo_funcao>\n";
            }
        }
    }
    echo "</query>";
?>
