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
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
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
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
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
		WHERE
			u.ref_cod_escola = {$_GET["esc"]}
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
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
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
			u.ref_cod_escola = {$_GET["esc"]}
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
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
		}
	}
	echo "</query>";
?>