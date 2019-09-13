<?php



    header( 'Content-type: text/xml' );

    require_once( "include/clsBanco.inc.php" );
    require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if( is_numeric( $_GET["esc"] ) && is_numeric( $_GET["ser"] ) )
    {
        $anoLetivo = $_GET["ano"] ? $_GET["ano"] : "NULL";

        $db = new clsBanco();

        $db->Consulta( "SELECT cod_turma,
                               nm_turma || ' - ' || turma.ano::varchar AS nm_turma
                         FROM pmieducar.turma
                        INNER JOIN pmieducar.escola_ano_letivo ON (escola_ano_letivo.ref_cod_escola = turma.ref_ref_cod_escola
                                                                   AND escola_ano_letivo.ano = turma.ano)
                        WHERE ref_ref_cod_escola = {$_GET["esc"]}
                          AND (ref_ref_cod_serie = {$_GET["ser"]} OR ref_ref_cod_serie_mult = {$_GET["ser"]})
                          AND turma.ativo = 1
                          AND escola_ano_letivo.andamento = 1
                          AND (CASE WHEN {$anoLetivo} IS NULL THEN TRUE ELSE escola_ano_letivo.ano = {$anoLetivo} END)
                        ORDER BY nm_turma ASC");
        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <turma cod_turma=\"{$cod}\">{$nome}</turma>\n";
        }
    }elseif (is_numeric( $_GET["ins"] ) && is_numeric( $_GET["cur"] ) ) {
        $db = new clsBanco();
        $db->Consulta( "SELECT cod_turma, nm_turma FROM pmieducar.turma WHERE ref_cod_instituicao = {$_GET["ins"]} AND ref_cod_curso = {$_GET["cur"]} AND ref_ref_cod_escola is null AND ref_ref_cod_serie is null AND ativo = 1 ORDER BY nm_turma ASC" );
        while ( $db->ProximoRegistro() )
        {
            list( $cod, $nome ) = $db->Tupla();
            echo "  <turma cod_turma=\"{$cod}\">{$nome}</turma>\n";
        }
    }
    echo "</query>";

?>
