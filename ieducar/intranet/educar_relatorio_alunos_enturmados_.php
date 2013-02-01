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
		$ref_cod_turma = $_GET['ref_cod_turma'];
		$ref_cod_instituicao = $_GET['ref_cod_instituicao'];
		$ano_requisitado = $_GET["ano"];
		
		$ano_requisitado = $_GET["ano"];
		$is_padrao       = $_GET["is_padrao"];

		if (is_numeric($_GET["sem1"]) && $ano_requisitado != 2007 && !$is_padrao)
			$semestre = $_GET["sem1"];
		elseif (is_numeric($_GET["sem2"]) && $ano_requisitado != 2007 && !$is_padrao)
			$semestre = $_GET["sem2"];
		else
			$semestre = null;

		@session_start();
		$pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$obj_escolas = new clsPmieducarEscola();
		$lst_escolas = $obj_escolas->lista($ref_cod_escola,null,null,$ref_cod_instituicao,null,null,null,null,null,null,1);
		if($lst_escolas)
		{

			//$relatorio = new relatorios("RELAÇÃO DOS ALUNOS ENTURMADOS   -   Ano {$ano}                                    Total de Alunos = {$lst_matricula_turma[0]['_total']}",120,false, "i-Educar", "A4", "Prefeitura COBRA Tecnologia\n\nSecretaria da Educação", "#515151");
			$relatorio_criado = false;
			//$relatorio->novaPagina();
			foreach ($lst_escolas as $key => $escola) {

				$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
				$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $escola['cod_escola'],$ano_requisitado,null,null,1,null,null,null,null,1 );
				if (is_array( $lst_escola_ano_letivo ))
				{
					$det_escola_ano_letivo = array_shift( $lst_escola_ano_letivo );
					$ano = $det_escola_ano_letivo['ano'];

					$obj_matricula_turma = new clsPmieducarMatriculaTurma();
					$obj_matricula_turma->setOrderby( "nm_curso, nm_serie, nm_turma, to_ascii(p.nome) ASC" );

					$lst_matricula_turma = $obj_matricula_turma->lista3( null,$ref_cod_turma,null,null,null,null,null,null, 1, $ref_ref_cod_serie, $ref_cod_curso, $escola['cod_escola'], null, array( 1, 2, 3 ), null, $ano, 1, true, $semestre );
					
					if ( is_array($lst_matricula_turma) )
					{
						if(!$relatorio_criado)
						{
							$relatorio = new relatorios("RELAÇÃO DOS ALUNOS ENTURMADOS ANO - {$ano}    ",120,false, "i-Educar", "A4", "Prefeitura COBRA Tecnologia\nSecretaria da Educação\n\n".date("d/m/Y"), "#515151");
							$relatorio->setMargem(20,20,50,50);
							$relatorio->exibe_produzido_por = false;
							$relatorio_criado = true;
						}

						$relatorio->novalinha( array( "{$escola['nome']}" ),0,16,true,"arial",array( 400 ),"#515151","#d3d3d3","#FFFFFF",false,true);
						$relatorio->novalinha( array( "Cód. Aluno", "Nome do Aluno", "Raça", "Sexo", "Data Nascimento", "Nome do Responsável" ),0,16,true,"arial",array( 75, 170,50,35,100 ),"#515151","#d3d3d3","#FFFFFF",false,true);
						$cod_curso = 0;
						$cod_serie = 0;
						$cod_turma = 0;

						$db = new clsBanco();

						foreach ($lst_matricula_turma as $matriculas)
						{
							if ($cod_turma != $matriculas['ref_cod_turma'])
							{
								$cod_curso = $matriculas['ref_cod_curso'];
								$cod_serie = $matriculas['ref_ref_cod_serie'];
								$cod_turma = $matriculas['ref_cod_turma'];

								$consulta = "SELECT count(1)
											   FROM pmieducar.matricula_turma mt
											        ,pmieducar.matricula m
											  WHERE mt.ref_cod_matricula = m.cod_matricula
											    AND mt.ativo = 1
											    AND m.ativo  = 1
											    AND mt.ref_cod_turma = {$cod_turma}
											    AND ultima_matricula = 1
											    AND m.aprovado IN (1,2,3)
											    AND ano = {$ano}
												AND ref_cod_curso = {$cod_curso}
												AND ref_ref_cod_escola = {$ref_cod_escola}
												AND ref_ref_cod_serie = {$cod_serie}
											";

								$total_alunos = (int)$db->CampoUnico($consulta);

								$relatorio->novalinha( array( "{$matriculas['nm_curso']}  -  {$matriculas['nm_serie']}  -  {$matriculas['nm_turma']}              Total Alunos:{$total_alunos}" ),0,16,true,"arial",array( 400 ),"#515151","#d3d3d3","#FFFFFF",false,true);
							}
							else if ($cod_serie != $matriculas['ref_ref_cod_serie'])
							{
								$cod_curso = $matriculas['ref_cod_curso'];
								$cod_serie = $matriculas['ref_ref_cod_serie'];
								$cod_turma = $matriculas['ref_cod_turma'];

								$consulta = "SELECT count(1)
											   FROM pmieducar.matricula_turma mt
											        ,pmieducar.matricula m
											  WHERE mt.ref_cod_matricula = m.cod_matricula
											    AND mt.ativo = 1
											    AND m.ativo  = 1
											    AND mt.ref_cod_turma = {$cod_turma}
											    AND ultima_matricula = 1
											    AND m.aprovado IN (1,2,3)
											    AND ano = {$ano}
												AND ref_cod_curso = {$cod_curso}
												AND ref_ref_cod_escola = {$ref_cod_escola}
												AND ref_ref_cod_serie = {$cod_serie}
											";

								$total_alunos = (int)$db->CampoUnico($consulta);

								$relatorio->novalinha( array( "{$matriculas['nm_curso']}  -  {$matriculas['nm_serie']}  -  {$matriculas['nm_turma']}              Total Alunos:{$total_alunos}" ),0,16,true,"arial",array( 400 ),"#515151","#d3d3d3","#FFFFFF",false,true);
							}
							else if ($cod_curso != $matriculas['ref_cod_curso'])
							{
								$cod_curso = $matriculas['ref_cod_curso'];
								$cod_serie = $matriculas['ref_ref_cod_serie'];
								$cod_turma = $matriculas['ref_cod_turma'];

								$consulta = "SELECT count(1)
											   FROM pmieducar.matricula_turma mt
											        ,pmieducar.matricula m
											  WHERE mt.ref_cod_matricula = m.cod_matricula
											    AND mt.ativo = 1
											    AND m.ativo  = 1
											    AND mt.ref_cod_turma = {$cod_turma}
											    AND ultima_matricula = 1
											    AND m.aprovado IN (1,2,3)
											    AND ano = {$ano}
												AND ref_cod_curso = {$cod_curso}
												AND ref_ref_cod_escola = {$ref_cod_escola}
												AND ref_ref_cod_serie = {$cod_serie}
											";

								$total_alunos = (int)$db->CampoUnico($consulta);

								$relatorio->novalinha( array( "{$matriculas['nm_curso']}  -  {$matriculas['nm_serie']}  -  {$matriculas['nm_turma']}              Total Alunos:{$total_alunos}" ),0,16,true,"arial",array( 400 ),"#515151","#d3d3d3","#FFFFFF",false,true);
							}

							$obj_aluno = new clsPmieducarAluno($matriculas['ref_cod_aluno']);
							$det_aluno = $obj_aluno->getResponsavelAluno();
							if($matriculas['data_nasc'])
								$matriculas['data_nasc'] = dataFromPgToBr($matriculas['data_nasc']);


							$obj_pessoa = new clsFisica($det_aluno['ref_idpes']);
							$det_pessoa = $obj_pessoa->detalhe();

							$obj_fisica_raca = new clsCadastroFisicaRaca( $det_aluno['ref_idpes']);
							$det_fisica_raca = $obj_fisica_raca->detalhe();
							$obj_raca = new clsCadastroRaca($det_fisica_raca['ref_cod_raca']);
							$det_raca = $obj_raca->detalhe();

							$relatorio->novalinha( array( $matriculas['ref_cod_aluno'], minimiza_capitaliza($matriculas['nome']),strtoupper($det_raca['nm_raca']), strtoupper($det_pessoa['sexo']),$matriculas['data_nasc'], minimiza_capitaliza($det_aluno['nome_responsavel']) ) , 5, 17, false, "arial", array( 60, 175,65,40,80 ));

							$ultimo_cont++;
						}
					}
					else
					{
							//echo "<center>Não existem alunos enturmados!</center>"	;
					}
				}


			}

			// pega o link e exibe ele ao usuario
			if($relatorio_criado)
				$link = $relatorio->fechaPdf();
			else
			{
				echo "<center>Não existem alunos enturmados!</center>";
				die;
			}

			echo "<center><a target='blank' href='" . $link . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
				<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

				Clique na Imagem para Baixar o instalador<br><br>
				<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
				</span>
				</center><script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$link."'}</script>";
		}


?>
</body>
</html>
