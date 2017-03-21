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

	if( is_numeric( $_GET["alu"] ) && is_numeric( $_GET["ins"] ) )
	{
		$db = new clsBanco();
		$db->Consulta( "
		SELECT
			m.cod_matricula
			, m.ref_cod_curso
			, c.padrao_ano_escolar
		FROM
			pmieducar.matricula m
			, pmieducar.curso c
		WHERE
			m.ref_cod_aluno = '{$_GET["alu"]}'
			AND m.ultima_matricula = 1
			AND m.ativo = 1
			AND m.ref_cod_curso = c.cod_curso
			AND c.ref_cod_instituicao = '{$_GET["ins"]}'
		ORDER BY
			m.cod_matricula ASC
		");

		// caso o aluno nao tenha nenhuma matricula em determinada instituicao
		if (!$db->numLinhas())
		{
			$db->Consulta( "
			SELECT
				cod_curso
				, nm_curso
			FROM
				pmieducar.curso
			WHERE
				padrao_ano_escolar = 0
				AND ativo = 1
				AND ref_cod_instituicao = '{$_GET["ins"]}'
				AND NOT EXISTS
				(
					SELECT
						ref_cod_curso
					FROM
						pmieducar.serie
					WHERE
						ref_cod_curso = cod_curso
						AND ativo = 1
				)
			ORDER BY
				nm_curso ASC
			");

			if ($db->numLinhas())
			{
				while ( $db->ProximoRegistro() )
				{
					list( $cod, $nome ) = $db->Tupla();
					echo "	<curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
				}
			}
		} // caso o aluno tenha matricula(s) em determinada instituicao
		else
		{
			while ( $db->ProximoRegistro() )
			{
				list( $matricula, $curso, $padrao_ano_escolar ) = $db->Tupla();

				if ( $padrao_ano_escolar == 0 )
				{
					$cursos_matriculado[] = $curso;
				}
			}
//			echo "<pre>"; print_r($cursos_matriculado); die();
			if (is_array($cursos_matriculado))
			{
				$sql = "
				SELECT
					cod_curso
					, nm_curso
				FROM
					pmieducar.curso
				WHERE
					padrao_ano_escolar = 0
					AND ativo = 1
					AND ref_cod_instituicao = '{$_GET["ins"]}'
					AND NOT EXISTS
					(
						SELECT
							ref_cod_curso
						FROM
							pmieducar.serie
						WHERE
							ref_cod_curso = cod_curso
							AND ativo = 1
					)";

				if (is_array($cursos_matriculado))
				{
					foreach ($cursos_matriculado as $cursos)
						$sql .= " AND cod_curso != '{$cursos}' ";
				}

				$sql .= "
				ORDER BY
					nm_curso ASC ";

				$db->Consulta( $sql );
				if ($db->numLinhas())
				{
					while ( $db->ProximoRegistro() )
					{
						list( $cod, $nome ) = $db->Tupla();
						echo "	<curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
					}
				}
			}
		}
	}
	echo "</query>";
?>