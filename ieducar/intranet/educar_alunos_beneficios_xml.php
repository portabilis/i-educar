<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaï¿½								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Pï¿½blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaï¿½			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  ï¿½  software livre, vocï¿½ pode redistribuï¿½-lo e/ou	 *
	*	modificï¿½-lo sob os termos da Licenï¿½a Pï¿½blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versï¿½o 2 da	 *
	*	Licenï¿½a   como  (a  seu  critï¿½rio)  qualquer  versï¿½o  mais  nova.	 *
	*																		 *
	*	Este programa  ï¿½ distribuï¿½do na expectativa de ser ï¿½til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implï¿½cita de COMERCIALI-	 *
	*	ZAï¿½ï¿½O  ou  de ADEQUAï¿½ï¿½O A QUALQUER PROPï¿½SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licenï¿½a  Pï¿½blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Vocï¿½  deve  ter  recebido uma cï¿½pia da Licenï¿½a Pï¿½blica Geral GNU	 *
	*	junto  com  este  programa. Se nï¿½o, escreva para a Free Software	 *
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

	if( is_numeric( $_GET["inst"] ) )
	{
		if(is_numeric( $_GET["ano"] ))
			$ano = " AND ano = {$_GET['ano']} ";
		$where = "";
		if (is_numeric($_GET['esc']))
			$where .= "AND ref_ref_cod_escola = {$_GET['esc']} ";
		if (is_numeric($_GET['curso']))
			$where .= "AND m.ref_cod_curso = {$_GET['curso']} ";
		if (is_numeric($_GET['serie']))
			$where .= "AND m.ref_ref_cod_serie = {$_GET['serie']} ";
		if (is_numeric($_GET['turma']))
			$where = "AND mt.ref_cod_turma = {$_GET["turma"]} ";
		$db = new clsBanco();
		$db->Consulta( "
		  SELECT  m.cod_matricula
			   	 ,p.nome
			FROM
				  pmieducar.matricula_turma mt
				 , pmieducar.matricula 	  m
				 , pmieducar.aluno 	  a
				 , cadastro.pessoa	  p
				 , pmieducar.instituicao i
				 , pmieducar.escola e
			WHERE
				i.cod_instituicao = {$_GET['inst']}
				AND i.cod_instituicao = e.ref_cod_instituicao
				AND e.cod_escola = m.ref_ref_cod_escola
				AND m.cod_matricula = mt.ref_cod_matricula
				AND m.ref_cod_aluno = a.cod_aluno
				AND a.ref_idpes	     = p.idpes
				AND mt.ativo 	= 1
				AND m.ativo     = 1
				AND a.ref_cod_aluno_beneficio IS NOT NULL
				{$where}
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