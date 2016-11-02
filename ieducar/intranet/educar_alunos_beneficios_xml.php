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

	require_once( "include/clsBanco.inc.php" );
	require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

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