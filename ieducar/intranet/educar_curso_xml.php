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
	if( is_numeric( $_GET["ins"] ) && ( $_GET["sem"] == "true" ) )
	{
		$db = new clsBanco();
		$db->Consulta( "
			SELECT
				cod_curso
				, nm_curso
			FROM
				pmieducar.curso
			WHERE
				ref_cod_instituicao = {$_GET["ins"]}
				AND padrao_ano_escolar = 0
				AND ativo = 1
			ORDER BY
				nm_curso ASC
			" );

		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome  ) = $db->Tupla();
			echo "	<curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
		}
	}
	elseif( is_numeric( $_GET["ins"] ) )
	{
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_curso, nm_curso,padrao_ano_escolar FROM pmieducar.curso WHERE ref_cod_instituicao = {$_GET["ins"]} AND ativo = 1 ORDER BY nm_curso ASC" );
		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome,$padrao  ) = $db->Tupla();
			echo "	<curso cod_curso=\"{$cod}\" padrao_ano_escolar=\"{$padrao}\">{$nome}</curso>\n";
		}
	}
	else if( is_numeric( $_GET["esc"] ) )
	{
		$sql_padrao_ano_escolar = "";
		if (is_string($_GET["padrao_ano_escolar"]) && !empty($_GET["padrao_ano_escolar"]))
		{
			if ($_GET["padrao_ano_escolar"] == "nao")
				$sql_padrao_ano_escolar = " AND c.padrao_ano_escolar = 0";
		}
		$db = new clsBanco();
		$db->Consulta( "SELECT
							c.cod_curso
							, c.nm_curso
						FROM
							pmieducar.curso c
							, pmieducar.escola_curso ec
						WHERE
							ec.ref_cod_escola = {$_GET["esc"]}
							AND ec.ref_cod_curso = c.cod_curso
							AND ec.ativo = 1
							AND c.ativo = 1
							{$sql_padrao_ano_escolar}
						ORDER BY
							c.nm_curso ASC" );

		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome) = $db->Tupla();
			echo "	<curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
		}
	}
	echo "</query>";
?>