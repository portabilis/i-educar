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
?>
<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="pt">
	<head>

	   	<title> <!-- #&TITULO&# --> </title>
		<link rel=stylesheet type='text/css' href='styles/styles.css' />
		<link rel=stylesheet type='text/css' href='styles/novo.css' />
		<link rel=stylesheet type='text/css' href='styles/menu.css' />
		<!-- #&ESTILO&# -->

		<script type='text/javascript' src='scripts/padrao.js'></script>
		<script type='text/javascript' src='scripts/novo.js'></script>
		<script type='text/javascript' src='scripts/dom.js'></script>
		<script type='text/javascript' src='scripts/menu.js'></script>

		<!-- #&SCRIPT&# -->

		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<!-- #&REFRESH&# -->

		<meta name='Author' content='Prefeitura de Itajaí' />
		<meta name='Description' content='Portal da Prefeitura de Itajaí' />
		<meta name='Keywords' content='portal, prefeitura, itajaí, serviço, cidadão' />

		<link rel="icon" href="imagens/logo_itajai.ico" type="image/x-icon">
	</head>
	<body onload="parent.EscondeDiv('LoadImprimir');">
<?php
		require_once ("include/clsBase.inc.php");
		require_once ("include/clsCadastro.inc.php");
		require_once ("include/relatorio.inc.php");
		require_once( "include/pmieducar/geral.inc.php" );

		$ref_cod_escola = $_GET['ref_cod_escola'];
		$ref_cod_curso = $_GET['ref_cod_curso'];
		$ref_ref_cod_serie = $_GET['ref_ref_cod_serie'];
		$ano_requisitado = $_GET["ano"];
		
		if (is_numeric($_GET["sem1"]) && $ano_requisitado != 2007 && !$is_padrao)
			$semestre = $_GET["sem1"];
		elseif (is_numeric($_GET["sem2"]) && $ano_requisitado != 2007 && !$is_padrao)
			$semestre = $_GET["sem2"];
		else
			$semestre = null;

		$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
//		$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $ref_cod_escola,$ano_requisitado,null,null,1,null,null,null,null,1 );
		$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $ref_cod_escola,$ano_requisitado,null,null,null,null,null,null,null,1 );
		if (is_array( $lst_escola_ano_letivo ))
		{
			$det_escola_ano_letivo = array_shift( $lst_escola_ano_letivo );
			$ano = $det_escola_ano_letivo['ano'];

			$obj_matricula_turma = new clsPmieducarMatriculaTurma();
			$obj_matricula_turma->setOrderby( "nm_curso, nm_serie, to_ascii(nome) ASC" );
			$lst_matricula_turma = $obj_matricula_turma->dadosAlunosNaoEnturmados( $ref_cod_escola, $ref_ref_cod_serie, $ref_cod_curso, $ano, true, $semestre );
			if ( is_array($lst_matricula_turma) )
			{
				$total = count($lst_matricula_turma);
				$relatorio = new relatorios("RELAÇÃO DOS ALUNOS NÃO ENTURMADOS   -   Ano {$ano}                                    Total de Alunos = {$total}",120,false, "i-Educar", "A4", "Prefeitura COBRA Tecnologia\nSecretaria da Educação\n\n".date("d/m/Y"), "#515151");
				$relatorio->setMargem(20,20,50,50);
				$relatorio->exibe_produzido_por = false;
				$relatorio->novalinha( array( "Cód. Aluno", "Nome do Aluno", "Data Nascimento", "Nome do Responsável" ),0,16,true,"arial",array( 75, 175,100 ),"#515151","#d3d3d3","#FFFFFF",false,true);

				$cod_curso = 0;
				$cod_serie = 0;
				$db = new clsBanco();
				foreach ($lst_matricula_turma as $matriculas)
				{
					
					if ($cod_serie != $matriculas['ref_ref_cod_serie'])
					{
						$cod_curso = $matriculas['ref_cod_curso'];
						$cod_serie = $matriculas['ref_ref_cod_serie'];

						$consulta = "SELECT count(1)
										   FROM pmieducar.matricula m
										  WHERE m.ativo  = 1
										    AND ultima_matricula = 1
										    AND m.aprovado IN (1,2,3)
										    AND ano = {$ano}
											AND ref_cod_curso = {$cod_curso}
											AND ref_ref_cod_escola = {$ref_cod_escola}
											AND ref_ref_cod_serie = {$cod_serie}
											AND NOT EXISTS ( SELECT DISTINCT 1
															   FROM pmieducar.matricula_turma mt
															  WHERE mt.ref_cod_matricula = m.cod_matricula
															    AND mt.ativo = 1 )
										";

						$total_alunos = (int)$db->CampoUnico($consulta);
						$relatorio->novalinha( array( "{$matriculas['nm_curso']}  -  {$matriculas['nm_serie']}              Total Alunos:{$total_alunos}" ),0,16,true,"arial",array( 400 ),"#515151","#d3d3d3","#FFFFFF",false,true);
					}
					else if ($cod_curso != $matriculas['ref_cod_curso'])
					{
						$cod_curso = $matriculas['ref_cod_curso'];
						$cod_serie = $matriculas['ref_ref_cod_serie'];
						$consulta = "SELECT count(1)
										   FROM pmieducar.matricula m
										  WHERE m.ativo  = 1
										    AND ultima_matricula = 1
										    AND m.aprovado IN (1,2,3)
										    AND ano = {$ano}
											AND ref_cod_curso = {$cod_curso}
											AND ref_ref_cod_escola = {$ref_cod_escola}
											AND ref_ref_cod_serie = {$cod_serie}
											AND NOT EXISTS ( SELECT DISTINCT 1
															   FROM pmieducar.matricula_turma mt
															  WHERE mt.ref_cod_matricula = m.cod_matricula
															    AND mt.ativo = 1 )
										";

						$total_alunos = (int)$db->CampoUnico($consulta);
						$relatorio->novalinha( array( "{$matriculas['nm_curso']}  -  {$matriculas['nm_serie']}              Total Alunos:{$total_alunos}" ),0,16,true,"arial",array( 400 ),"#515151","#d3d3d3","#FFFFFF",false,true);
					}
					$obj_aluno = new clsPmieducarAluno($matriculas['cod_aluno']);

					$det_aluno = $obj_aluno->getResponsavelAluno();

					if($matriculas['data_nasc'])
						$matriculas['data_nasc'] = dataFromPgToBr($matriculas['data_nasc']);

					$relatorio->novalinha( array( $matriculas['cod_aluno'], minimiza_capitaliza($matriculas['nome']),$matriculas['data_nasc'], minimiza_capitaliza($det_aluno['nome_responsavel']) ) , 5, 17, false, "arial", array( 60, 200,80 ));
				}
				// pega o link e exibe ele ao usuario
				$link = $relatorio->fechaPdf();

				echo "<center><a target='blank' href='" . $link . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
					<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

					Clique na Imagem para Baixar o instalador<br><br>
					<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
					</span>
					</center><script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$link."'}</script>";
			}
			else
			{
					echo "<center>Não existem alunos não enturmados!</center>"	;
			}
		}

?>
</body>
</html>
