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
        $db->Consulta( "
        SELECT
            cod_material_tipo
            , nm_tipo
        FROM
            pmieducar.material_tipo
        WHERE
            ativo = 1
            AND ref_cod_instituicao = '{$_GET["ins"]}'
        ORDER BY
            nm_tipo ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome ) = $db->Tupla();
                echo "  <material_tipo cod_material_tipo=\"{$cod}\">{$nome}</material_tipo>\n";
            }
        }
    }
    echo "</query>";
?>
