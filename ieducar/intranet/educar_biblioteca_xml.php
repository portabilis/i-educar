<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
header( 'Content-type: text/xml' );

require_once '../autoload.php';
require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

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
				echo "	<biblioteca cod_biblioteca=\"{$cod}\">{$nome}</biblioteca>\n";
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
				echo "	<biblioteca cod_biblioteca=\"{$cod}\">{$nome}</biblioteca>\n";
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
				echo "	<biblioteca cod_biblioteca=\"{$cod}\" requisita_senha=\"{$senha}\">{$nome}</biblioteca>\n";
			}
		}
	}
	echo "</query>";
?>