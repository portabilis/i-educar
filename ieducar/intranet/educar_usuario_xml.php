<?php

    header( 'Content-type: text/xml charset=utf-8' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if( is_numeric( $_GET["ins"] ) )
    {
        $db = new clsBanco();

        // USUARIO ESCOLA
        $db->Consulta( "
        SELECT
            u.cod_usuario
            , p.nome
        FROM
            pmieducar.usuario u
            , pmieducar.tipo_usuario tu
            , cadastro.pessoa p
        WHERE
            u.ref_cod_instituicao = {$_GET["ins"]}
            AND u.cod_usuario = p.idpes
            AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
            AND u.ativo = 1
            AND tu.nivel = 4
        ORDER BY
            p.nome ASC"
        );

        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
        }

        // USUARIO BIBLIOTECA
        $db->Consulta( "
        SELECT
            u.cod_usuario
            , p.nome
        FROM
            pmieducar.usuario u
            , pmieducar.tipo_usuario tu
            , cadastro.pessoa p
        WHERE
            u.ref_cod_instituicao = {$_GET["ins"]}
            AND u.cod_usuario = p.idpes
            AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
            AND u.ativo = 1
            AND tu.nivel = 8
        ORDER BY
            p.nome ASC"
        );

        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
        }
    }
    elseif( is_numeric( $_GET["esc"] ) )
    {
        $db = new clsBanco();

        // USUARIO ESCOLA
        $db->Consulta( "
        SELECT
            u.cod_usuario
            , p.nome
        FROM
            pmieducar.usuario u
            , pmieducar.tipo_usuario tu
            , cadastro.pessoa p
            , pmieducar.escola_usuario eu
        WHERE
            eu.ref_cod_escola = {$_GET["esc"]}
            AND eu.ref_cod_usuario = u.cod_usuario
            AND u.cod_usuario = p.idpes
            AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
            AND u.ativo = 1
            AND tu.nivel = 4
        ORDER BY
            p.nome ASC"
        );

        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
        }

        // USUARIO BIBLIOTECA
        $db->Consulta( "
        SELECT
            u.cod_usuario
            , p.nome
        FROM
            pmieducar.usuario u
            , pmieducar.tipo_usuario tu
            , cadastro.pessoa p
            , pmieducar.escola_usuario eu
        WHERE
            eu.ref_cod_escola = {$_GET["esc"]}
            AND eu.ref_cod_usuario = u.cod_usuario
            AND u.cod_usuario = p.idpes
            AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
            AND u.ativo = 1
            AND tu.nivel = 8
        ORDER BY
            p.nome ASC"
        );

        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
        }
    }
    echo "</query>";
?>
