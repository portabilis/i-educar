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
                    cod_escola
                    , fantasia as nome
                FROM
                    pmieducar.escola
                    , cadastro.juridica
                WHERE
                    ref_cod_instituicao = {$_GET["ins"]}
                    AND idpes = ref_idpes
                    AND ativo = 1
            UNION
                SELECT
                    cod_escola
                    , nm_escola
                FROM
                    pmieducar.escola
                    , pmieducar.escola_complemento
                WHERE
                    ref_cod_instituicao = {$_GET["ins"]}
                    AND cod_escola = ref_cod_escola
                    AND escola.ativo = 1
                ORDER BY 2 ASC
            " );
        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
      $nome = htmlspecialchars($nome);
            echo "  <escola cod_escola=\"{$cod}\">{$nome}</escola>\n";
        }
    }
    echo "</query>";
?>
