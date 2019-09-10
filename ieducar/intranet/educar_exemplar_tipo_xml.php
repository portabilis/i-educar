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
        $db2 = new clsBanco();

        $db->Consulta("SELECT
                            DISTINCT cod_exemplar_tipo
                        FROM
                            pmieducar.exemplar_tipo
                        WHERE
                            ativo = '1'
                        AND
                            ref_cod_biblioteca = '{$_GET['bib']}'
                    ");

        if ($db->numLinhas())
        {
            while ( $db->ProximoRegistro() )
            {
                list($cod) = $db->Tupla();
                $nome = $db2->CampoUnico("SELECT nm_tipo FROM pmieducar.exemplar_tipo WHERE ativo = '1' AND ref_cod_biblioteca = '{$_GET['bib']}' AND cod_exemplar_tipo = '$cod'");

        if (is_numeric($_GET['cod_tipo_cliente'])) {
                $dias_emprestimo = $db2->CampoUnico("SELECT dias_emprestimo FROM pmieducar.cliente_tipo_exemplar_tipo, pmieducar.exemplar_tipo WHERE ativo = '1' AND cod_exemplar_tipo = ref_cod_exemplar_tipo AND ref_cod_biblioteca = '{$_GET['bib']}' AND cod_exemplar_tipo = '$cod' $cliente_tipo AND ref_cod_cliente_tipo = '{$_GET['cod_tipo_cliente']}'");
        }
        else
          $dias_emprestimo = '';

                echo "  <exemplar_tipo cod_exemplar_tipo=\"{$cod}\" dias_emprestimo=\"{$dias_emprestimo}\">{$nome}</exemplar_tipo>\n";
            }
        }
    }
    echo "</query>";
?>
