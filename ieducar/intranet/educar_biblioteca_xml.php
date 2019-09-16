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
            cod_biblioteca
            , nm_biblioteca
        FROM
            pmieducar.biblioteca
        WHERE
            ativo = 1
            AND ref_cod_instituicao = '{$_GET["ins"]}'
        ORDER BY
            nm_biblioteca ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome ) = $db->Tupla();
                echo "  <biblioteca cod_biblioteca=\"{$cod}\">{$nome}</biblioteca>\n";
            }
        }
    }
    elseif( is_numeric( $_GET["esc"] ) )
    {
        $db = new clsBanco();
        $db->Consulta( "
        SELECT
            cod_biblioteca
            , nm_biblioteca
        FROM
            pmieducar.biblioteca
        WHERE
            ativo = 1
            AND ref_cod_escola = '{$_GET["esc"]}'
        ORDER BY
            nm_biblioteca ASC
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome ) = $db->Tupla();
                echo "  <biblioteca cod_biblioteca=\"{$cod}\">{$nome}</biblioteca>\n";
            }
        }
    }
    elseif( is_numeric( $_GET["bib"] ) )
    {
        $db = new clsBanco();
        $db->Consulta( "
        SELECT
            cod_biblioteca
            , nm_biblioteca
            , requisita_senha
        FROM
            pmieducar.biblioteca
        WHERE
            ativo = 1
            AND cod_biblioteca = '{$_GET["bib"]}'
        ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list( $cod, $nome, $senha ) = $db->Tupla();
                echo "  <biblioteca cod_biblioteca=\"{$cod}\" requisita_senha=\"{$senha}\">{$nome}</biblioteca>\n";
            }
        }
    }
    echo "</query>";
?>
