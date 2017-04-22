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

	if( is_numeric( $_GET["tur"] ) )
	{
		if(is_numeric( $_GET["ano"] ))
			$ano = " AND ano = {$_GET['ano']} ";
		$db = new clsBanco();
		$db->Consulta( "
		  SELECT  m.cod_matricula
			   	 ,p.nome
			FROM
				  pmieducar.matricula_turma mt
				 , pmieducar.matricula 	  m
				 , pmieducar.aluno 	  a
				 , cadastro.pessoa	  p
			WHERE
				m.cod_matricula = mt.ref_cod_matricula
				AND mt.ref_cod_turma = {$_GET["tur"]}
				AND m.ref_cod_aluno = a.cod_aluno
				AND a.ref_idpes	     = p.idpes
				AND aprovado IN (1,2,3)
				{$ano}
				AND mt.ativo 	= 1
				AND m.ativo     = 1
			ORDER BY
				to_ascii(p.nome) ASC
		");

		if ($db->numLinhas())
		{
			while ( $db->ProximoRegistro() )
			{
				list( $cod, $nome ) = $db->Tupla();
				echo "	<matricula cod_matricula=\"{$cod}\">{$nome}</matricula>\n";
			}
		}
	}
	echo "</query>";
?>