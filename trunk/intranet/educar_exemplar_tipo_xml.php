<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	header( 'Content-type: text/xml' );

	require_once( "include/clsBanco.inc.php" );
	require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

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

				echo "	<exemplar_tipo cod_exemplar_tipo=\"{$cod}\" dias_emprestimo=\"{$dias_emprestimo}\">{$nome}</exemplar_tipo>\n";
			}
		}
	}
	echo "</query>";
?>
