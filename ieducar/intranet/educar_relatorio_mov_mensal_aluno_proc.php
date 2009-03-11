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
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/clsPDF.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - i-Educar - Movimentação Mensal de Alunos" );
		$this->processoAp = "661";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;


	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ano;
	var $mes;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $sequencial;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $page_y = 125;

	var $cursos = array();

	var $array_disciplinas = array();

	var $get_link;

	var $ref_cod_modulo;

	var $meses_do_ano = array(
							 "1" => "JANEIRO"
							,"2" => "FEVEREIRO"
							,"3" => "MARÇO"
							,"4" => "ABRIL"
							,"5" => "MAIO"
							,"6" => "JUNHO"
							,"7" => "JULHO"
							,"8" => "AGOSTO"
							,"9" => "SETEMBRO"
							,"10" => "OUTUBRO"
							,"11" => "NOVEMBRO"
							,"12" => "DEZEMBRO"
						);

	var $total_dias_uteis;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}

		if(!$_POST)
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNão existem dados!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}

		if(empty($this->cursos))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhum curso selecionado!");
	     			window.location = window.location;
	     		</script>';
	     	return true;
		}

		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		}

	     $obj_calendario = new clsPmieducarEscolaAnoLetivo();
	     $lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

	     if(!$lista_calendario)
	     {
	     	echo '<script>
	     			alert("Escola não possui calendário definido para este ano");
	     			window.location = window.location;
	     		</script>';
	     	return true;
	     }

		//$calendario = array_shift($lista_calendario);

		$obj_cal_ano_letivo = new clsPmieducarCalendarioAnoLetivo();
		$lst_cal_ano_letivo = $obj_cal_ano_letivo->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1,null,$this->ref_cod_instituicao);

		$calendario = array_shift($lst_cal_ano_letivo);

		$obj_calendario_dia = new clsPmieducarCalendarioDia();
		$lista_dias = $obj_calendario_dia->lista($calendario['cod_calendario_ano_letivo'],$this->mes,null,null,null,null,null,null,null,1);
		$dias_mes = array();

		if($lista_dias)
		{
			foreach ($lista_dias as $dia) {
				$obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);
				$det_motivo = $obj_motivo->detalhe();
				$dias_mes[$dia['dia']] = strtolower($det_motivo['tipo']);
			}
		}
		 //Dias previstos do mes
	     // Qual o primeiro dia do mes
	     $primeiroDiaDoMes = mktime(0,0,0,$this->mes,1,$this->ano);
	     // Quantos dias tem o mes
	     $NumeroDiasMes = date('t',$primeiroDiaDoMes);

	     //informacoes primeiro dia do mes
		 $dateComponents = getdate($primeiroDiaDoMes);

	     // What is the name of the month in question?
	     $NomeMes = $mesesDoAno[$dateComponents['mon']];

	     // What is the index value (0-6) of the first day of the
	     // month in question.
	     $DiaSemana = $dateComponents['wday'];

	     //total de dias uteis + dias extra-letivos - dia nao letivo - fim de semana
	     $this->totalDiasUteis = 0;
	     
	     $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
		 $inicio_ano = $obj_ano_letivo_modulo->menorData( $this->ano, $this->ref_cod_escola );
	     $inicio_ano = explode("-", $inicio_ano);
	     
	     for($dia = 1; $dia <= $NumeroDiasMes; $dia++)
	     {
	     	if($DiaSemana >= 7) {
	     		$DiaSemana = 0;
	     	}
	     	if ($this->mes == $inicio_ano[1])
	     	{
	     		if ($dia >= $inicio_ano[2]) {
	     			if($DiaSemana != 0 && $DiaSemana != 6){
	     				if(!(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('n')))
	     				$this->totalDiasUteis++;
	     			}elseif(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('e'))
	     			$this->totalDiasUteis++;
	     		}
	     	}
	     	else
	     	{
	     		if($DiaSemana != 0 && $DiaSemana != 6){
	     			if(!(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('n')))
	     			$this->totalDiasUteis++;
	     		}elseif(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('e'))
	     		$this->totalDiasUteis++;
	     	}

	     	$DiaSemana++;

	     }

		$cursos_in = '';
		$conc = '';
		foreach ($this->cursos as $curso)
		{
			$cursos_in .= "{$conc}{$curso}";
			$conc = ",";
		}

		$this->pdf = new clsPDF("Movimentação Mensal de Alunos - {$this->ano}", "Movimentação Mensal de Alunos - {$this->ano}", "A4", "", false, false);

		$this->pdf->largura  = 842.0;
  		$this->pdf->altura = 595.0;

		$fonte = 'arial';
		$corTexto = '#000000';



		$altura_linha = 23;
		$inicio_escrita_y = 175;

		$this->pdf->OpenPage();
		$this->addCabecalho();
		$this->novoCabecalho();
		
		$quantidade_total = array();
		
		foreach ($this->cursos as $curso)
		{

			//busca todas as series de um curso
			$obj_serie_cursos = new clsPmieducarSerie();
			$lista_serie_cursos = $obj_serie_cursos->lista(null,null,null,$curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

			$total_turmas_curso = 0;
			$total_curso = array();

			for($a=0;$a<30;$a++ )
				$total_curso[$a] = 0;

			if($lista_serie_cursos)
			{


				for($a=0;$a<30;$a++ )
					$total_serie[$a] = 0;

				foreach ($lista_serie_cursos as $serie)
				{
					$total_serie = array();
					//nome da serie
					$nm_serie = $serie['nm_serie'];

					//MATRICULA INICIAL
					//busca todas turmas de cada serie
					$obj_serie_turmas = new clsPmieducarTurma();
					$obj_serie_turmas->setOrderby("nm_turma");
					$lista_serie_turmas = $obj_serie_turmas->lista(null,null,null,$serie['cod_serie'],$this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1,null,null,null,null,null,null,null,null,null);

					//total de turmas de uma serie
					$total_turmas_curso += $total_turmas_serie = $obj_serie_turmas->_total;

					if($lista_serie_turmas)
					{
						$quantidades = array();

						foreach ($lista_serie_turmas as $turma)
						{
							//professor regente da turma
							$obj_servidor = new clsPessoa_($turma['ref_cod_regente']);
							$det_sevidor  =  $obj_servidor->detalhe();
							$nm_servidor  = $det_sevidor['nome'];

							//nome da turma de uma serie
							$nm_turma = $turma['nm_turma'];

							if($turma['hora_inicial'] <= '12:00')
								$turno = 'M';
							elseif($turma['hora_inicial'] > '12:00' && $turma['hora_inicial'] <= '18:00')
								$turno = 'V';
							else
								$turno = 'N';

							$depurar=false;
							if (trim($turma["nm_turma"]) == "202") {
//								$depurar=true;
							}
							/**
							 * busca todos os alunos que foram matriculados nos meses anteriores ao atual
							 */
							$obj_matriculas = new clsPmieducarMatriculaTurma();
							//$lista_matriculas = $obj_matriculas->lista(null,$turma['cod_turma'],null,null,null,null,null,null,1,$serie['cod_serie'],$curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array( 1, 2, 3 ),((int)$this->mes === 1 ) ? $this->mes + 1: $this->mes,null,$this->ano );
							//$lista_matriculas = $obj_matriculas->lista(null,$turma['cod_turma'],null,null,null,null,null,null,null,$serie['cod_serie'],$curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,null,((int)$this->mes === 1 ) ? $this->mes + 1: $this->mes,null,$this->ano );
							$lista_matriculas = $obj_matriculas->lista(null,$turma['cod_turma'],null,null,null,null,null,null,null,$serie['cod_serie'],$curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,null,null,null,$this->ano,null,null,null,null,true,null,$this->mes, null, null, null, null, $depurar);

							$total_matriculas_turma = $obj_matriculas->_total;

							$total_matriculas_turma_masculino = 0;
							$total_matriculas_turma_feminino = 0;
							
							if($lista_matriculas)
							{
								foreach ($lista_matriculas as $matricula)
								{
									$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
									$det_matricula = $obj_matricula->detalhe();

									$obj_aluno = new clsPmieducarAluno($det_matricula['ref_cod_aluno']);
									$det_aluno = $obj_aluno->detalhe();

									$obj_pessoa = new clsFisica($det_aluno['ref_idpes']);
									$det_pessoa = $obj_pessoa->detalhe();

									if( strtoupper($det_pessoa['sexo']) == 'M')
										$total_matriculas_turma_masculino++;
									else
										$total_matriculas_turma_feminino++;
								}
							}
							
							//quantidades matricula inicial
//							M
							$quantidades[0] = $total_matriculas_turma_masculino;
//							F
							$quantidades[1] = $total_matriculas_turma_feminino;
//							T
							$quantidades[2] = $total_matriculas_turma;
							
							$db3 = new clsBanco();
							
							if (trim($turma["nm_turma"]) == "304") {
//								echo "<pre>"; print_r($quantidades);// die();
							}
							/*Array
(
    [0] => 15
    [1] => 14
    [2] => 29
)
EL. ENTURMACAO 48743          2
EL. TRANSFERENCIA 48775                    1
AD ENTURMACAO 48816       4
EL. TRANSFERENCIA 54097                    1
AD ENTURMACAO 54962       4
AD ENTURMACAO 55101       4
AD TRANSFERENCIA 57059          3
AD TRANSFERENCIA 57070          3*/
							
//							echo $turma["cod_turma"]."<br>";
							
							//sql para pegar o numero de alunos que 
							// abandonaram e diminuir da soma final
							/*$sql = "SELECT COUNT(0) FROM
										pmieducar.matricula m, 
										pmieducar.matricula_turma mt,
										pmieducar.aluno a,
										cadastro.fisica f
									WHERE 
										mt.ref_cod_turma = {$turma["cod_turma"]} 
										AND mt.ativo = 1 
										AND mt.ref_cod_matricula = m.cod_matricula
										AND m.ref_cod_aluno = a.cod_aluno 
										AND a.ref_idpes = f.idpes 
										AND sexo = 'M' 
										AND ano = {$this->ano}
										AND m.aprovado not in(1,2,3)  
										AND ( to_char(mt.data_cadastro,'MM')::int = '{$this->mes}' 
											  OR to_char(mt.data_exclusao,'MM')::int = '{$this->mes}')";
							$diminui_masc = (int)$db3->CampoUnico($sql);
							if ($depurar) {
								echo $sql."<br>";
							}
							$sql = "SELECT COUNT(0) FROM 
										pmieducar.matricula m, 
										pmieducar.matricula_turma mt, 
										pmieducar.aluno a,
										cadastro.fisica f
									WHERE 
										mt.ref_cod_turma = {$turma["cod_turma"]} 
										AND mt.ativo = 1 
										AND mt.ref_cod_matricula = m.cod_matricula
										AND m.ref_cod_aluno = a.cod_aluno 
										AND a.ref_idpes = f.idpes 
										AND sexo = 'F'
										AND ano = {$this->ano}
										AND m.aprovado not in(1,2,3)
										AND ( to_char(mt.data_cadastro,'MM')::int = '{$this->mes}' 
												OR to_char(mt.data_exclusao,'MM')::int = '{$this->mes}')";
							$diminui_fem = (int)$db3->CampoUnico($sql);			*/
//							$depurar = false;		
							if ($depurar || $turma["nm_turma"] == "304") {
//								die($sql);
//								$depurar = true;
							}
							
//							$diminui_fem = $diminui_masc = 0;
//							ENTURMACAO
							/**
							 * seleciona todas as matriculas com data_* no mes atual
							 */
							$sql_complemento = "OR (
														EXISTS (
															SELECT 1 FROM pmieducar.transferencia_solicitacao ts WHERE ts.ativo = 1 AND 
															 to_char(ts.data_transferencia,'MM')::int = {$this->mes} AND 
															 ts.ref_cod_matricula_saida = m.cod_matricula 
															)
													)";
							$sql_complemento = "";
							if ($depurar) {
//								$sql_complemento1 = "AND m.cod_matricula NOT IN (48743, 48775, 48816, 54097, 54962, 55101, 57059, 57070)";
//								$sql_complemento1 = "AND m.cod_matricula NOT IN (48743, 48775, 48816, 54097, 54962, 55101, 57059)";
							}
													
							$db = new clsBanco();
							$consulta = "SELECT 
											DISTINCT mt.ref_cod_matricula, mt.sequencial, mt.ativo
										 FROM 
										 	pmieducar.matricula_turma mt
										 	,pmieducar.matricula m
										 WHERE
										 	mt.ref_cod_matricula = m.cod_matricula
										 	AND m.ano = {$this->ano}
										 	AND m.ativo = 1
										 	AND mt.ref_cod_turma = {$turma["cod_turma"]}
										 	AND (	to_char(mt.data_cadastro,'MM')::int = {$this->mes}
										 		OR
										 			to_char(mt.data_exclusao,'MM')::int = {$this->mes}
										 			{$sql_complemento}	
										 		)
										 	ORDER BY
										 		1, 2, ativo";
							$depurar = false;
							if ($depurar || trim($turma["nm_turma"]) == "202") {
//								$depurar = true;
							}
							$db->Consulta($consulta);

							$total_admitido_enturmacao  = 0;
							$total_admitido_enturmacao_masc  = 0;
							$total_admitido_enturmacao_fem  = 0;
							$total_eliminado_enturmacao = 0;
							$total_eliminado_enturmacao_masc = 0;
							$total_eliminado_enturmacao_fem = 0;

							$total_reclassificacao = 0;
							$total_admitido_reclassificao = 0;
							$total_admitido_reclassificao_masc = 0;
							$total_admitido_reclassificao_fem = 0;
							$total_eliminado_reclassificacao = 0;
							$total_eliminado_reclassificacao_masc = 0;
							$total_eliminado_reclassificacao_fem = 0;

							$total_transferencia = 0;
							$total_admitido_transferencia = 0;
							$total_admitido_transferencia_masc = 0;
							$total_admitido_transferencia_fem = 0;
							$total_eliminado_transferencia = 0;
							$total_eliminado_transferencia_masc = 0;
							$total_eliminado_transferencia_fem = 0;

							$total_abandono = 0;//$diminui_fem + $diminui_masc;
							$total_abandono_masc = 0;//$diminui_masc;
							$total_abandono_fem = 0;//$diminui_fem;
							
							$diminui_fem = $diminui_masc = 0;

							$total_aprovado = 0;
							$total_aprovado_masc = 0;
							$total_aprovado_fem = 0;

							$matriculas_transferencia=array();
							if($db->Num_Linhas())
							{
								$db2 = new clsBanco();
								while ($db->ProximoRegistro())
								{
									list( $cod_matricula, $sequencial,$ativo ) = $db->Tupla();									
									
//									$depurar=false;
									if ($cod_matricula==48743) {
//										die("<br><br><br><br><br><br>".$ativo." ,______");
//										$depurar=true;
									}
									$obj_matricula = new clsPmieducarMatricula($cod_matricula);
									$det_matricula = $obj_matricula->detalhe();

									$obj_aluno = new clsPmieducarAluno($det_matricula['ref_cod_aluno']);
									$det_aluno = $obj_aluno->detalhe();

									$obj_pessoa = new clsFisica($det_aluno['ref_idpes']);
									$det_pessoa = $obj_pessoa->detalhe();

									$sexo = strtoupper($det_pessoa['sexo']);
									
									$consulta = "SELECT ativo
												   FROM pmieducar.matricula_turma mt1
												  WHERE ref_cod_matricula = '{$cod_matricula}'
												    AND sequencial = $sequencial
													AND ref_cod_turma = '{$turma['cod_turma']}'
												    AND (data_cadastro, COALESCE(data_exclusao,now())) = ( SELECT data_cadastro, COALESCE(data_exclusao,now())
																											 FROM pmieducar.matricula_turma mt2
																											WHERE ref_cod_matricula IN ( SELECT cod_matricula
																											  							   FROM pmieducar.matricula
																											  							  WHERE ano = '{$this->ano}'
																											  							    AND ref_cod_aluno = ( SELECT DISTINCT ref_cod_aluno
																											  							  					        FROM pmieducar.matricula
																											  							  					       WHERE cod_matricula = '{$cod_matricula}' ) )
																											ORDER BY data_cadastro desc,data_exclusao desc
																											LIMIT 1 )
													AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
														   )
												ORDER BY data_cadastro desc,data_exclusao desc,ativo ";
													/*OR to_char( data_exclusao,'MM')::int = '{$this->mes}'*/

																														
									$eh_ultima_matricula = $db2->CampoUnico($consulta);
									/**
									 * nao eh a ultima matricula
									 */
																		
									//02-07-07
									//f(!is_numeric($eh_ultima_matricula) || $eh_ultima_matricula == 0 /*&& $eh_ultima_matricula != 0 && $eh_ultima_matricula != 1*/)
									
									if(!is_numeric($eh_ultima_matricula)  /*&& $eh_ultima_matricula != 0 && $eh_ultima_matricula != 1*/)
									{
										$foi_admitido_transferencia = false;
										$foi_eliminado_transferencia = false;
										if ($cod_matricula==48743) {
//											die("<br><br><br><br><br><br>".$ativo." ,______");
//											$depurar=true;
										}
									/**
										 * verifica se for a primeira enturmacao
										 * se ela esta marcada como reclassificacao
										 */
										//echo "$cod_matricula-$sequencial<br>";

										$consulta = "SELECT 1
													   FROM pmieducar.matricula_turma mt1
													  WHERE ref_cod_matricula = $cod_matricula
													    AND sequencial = $sequencial
														AND ref_cod_turma = {$turma['cod_turma']}
													    AND (data_cadastro, COALESCE(data_exclusao,now())) = ( SELECT data_cadastro, COALESCE(data_exclusao,now())
																		 FROM pmieducar.matricula_turma mt2
																		WHERE ref_cod_matricula = $cod_matricula
																			AND ref_cod_turma = {$turma['cod_turma']}
																		ORDER BY data_cadastro asc,data_exclusao asc
																		LIMIT 1 )
														AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
															  )
													ORDER BY data_cadastro asc,data_exclusao asc,ativo ";
												/*OR to_char( data_exclusao,'MM')::int = '{$this->mes}' */
										$eh_primeira_enturmacao_desta_matricula = $db2->CampoUnico($consulta);
									
										$consulta = "SELECT matricula_reclassificacao
										               FROM pmieducar.matricula
										              WHERE cod_matricula = {$cod_matricula}";

										$matricula_reclassificacao = $db2->CampoUnico($consulta);

										//ref_cod_matricula_saida
										$consulta = "SELECT 1
													   FROM pmieducar.transferencia_solicitacao
													  WHERE ref_cod_matricula_entrada = {$cod_matricula}
													    AND ativo = 1
														AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
														      OR to_char( data_exclusao,'MM')::int = '{$this->mes}' )";
									
										$existe_transferencia = $db2->CampoUnico($consulta);

										/**
										 * verifica se eh a primeira matricula do aluno
										 */
										$obj_matricula_aluno = new clsPmieducarMatricula();
										$lst_matricula_aluno = $obj_matricula_aluno->lista(null, null, null, null, null, null, $det_aluno['cod_aluno'], null, null, null, null, null, $this->ano);
										$eh_primeira_matricula_do_aluno = count($lst_matricula_aluno) == 1 ? 1 : 0;
										
										$consulta = "SELECT 1
													   FROM pmieducar.matricula
													  WHERE cod_matricula = {$cod_matricula}
													    AND matricula_transferencia = true AND to_char(data_cadastro,'MM')::int = {$this->mes}";
									
										$primeira_matricula_eh_transferencia = $db2->CampoUnico($consulta);
																			
										$consulta = "SELECT max(sequencial)
													   FROM pmieducar.matricula_turma
													  WHERE ref_cod_matricula = {$cod_matricula}";

										$max_sequencial = $db2->CampoUnico($consulta);

										if (is_numeric($primeira_matricula_eh_transferencia))
										{
											$matriculas_transferencia[$cod_matricula] = $cod_matricula;
											$total_admitido_transferencia++;
											if ($depurar) {
													echo("AD TRANSFERENCIA $cod_matricula  aquiiii<br>");
													$matriculas[]=$cod_matricula;
												}
											if ($sexo == 'M')
												$total_admitido_transferencia_masc++;
											else 
												$total_admitido_transferencia_fem++;
										
												
										}
										
										if($eh_primeira_enturmacao_desta_matricula && $matricula_reclassificacao )
										{
											if ($depurar) {
												$matriculas[]=$cod_matricula;
											}
											$total_admitido_reclassificao++;

											if( $sexo == 'M' )
												$total_admitido_reclassificao_masc++;
											else
												$total_admitido_reclassificao_fem++;
										}
										elseif(($eh_primeira_enturmacao_desta_matricula && $existe_transferencia) || ($eh_primeira_enturmacao_desta_matricula && $eh_primeira_matricula_do_aluno && $primeira_matricula_eh_transferencia) )
										{
											if (is_numeric($existe_transferencia) && $existe_transferencia == 1)
											{
												if ($depurar) {
													echo("AD TRANSFERENCIA $cod_matricula  1<br>");
													$matriculas[]=$cod_matricula;
												}
												if (!is_numeric(array_search($cod_matricula,$matriculas_transferencia))) {
													$total_admitido_transferencia++;
													if( $sexo == 'M' )
														$total_admitido_transferencia_masc++;
													else
														$total_admitido_transferencia_fem++;
														
													$foi_admitido_transferencia = true;
														
												}
											}

										}
										else
										{

											if (!is_numeric($primeira_matricula_eh_transferencia) && $ativo == 1) {
												
												if ($depurar) {
													echo("AD ENTURMACAO $cod_matricula             1<br>");
													$matriculas[]=$cod_matricula;
												}
												$total_admitido_enturmacao++;
												
												if( $sexo == 'M' )
													$total_admitido_enturmacao_masc++;
												else
													$total_admitido_enturmacao_fem++;
											}

										}

										/**
										 * verifica se eh a ultima enturmacao desta matricula
										 */
										$consulta = "SELECT ref_cod_turma
													   FROM pmieducar.matricula_turma mt1
													  WHERE ref_cod_matricula = $cod_matricula
														AND sequencial = $sequencial
														 AND (ref_cod_turma,sequencial) = (SELECT ref_cod_turma, max(sequencial)
																							 FROM pmieducar.matricula_turma mt2
																							WHERE ref_cod_matricula = $cod_matricula
																							group by ref_cod_turma,data_cadastro
																							order by data_cadastro desc limit 1)";

										$ultima_turma_enturmacao = $db2->CampoUnico($consulta);
										
										if($ultima_turma_enturmacao == $turma['cod_turma'])
										{
											$consulta = "SELECT aprovado
											               FROM pmieducar.matricula
											              WHERE cod_matricula = {$cod_matricula}";

											$situacao = $db2->CampoUnico($consulta);
												$consulta = "SELECT 1
															   FROM pmieducar.transferencia_solicitacao
															  WHERE ref_cod_matricula_saida = {$cod_matricula}
															    AND (ativo = 1 OR data_transferencia IS NOT NULL)
																AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
																      OR to_char( data_exclusao,'MM')::int = '{$this->mes}' )
																AND(to_char(data_transferencia,'MM')::int = '{$this->mes}' OR data_transferencia IS NULL)";

												$existe_transferencia = $db2->CampoUnico($consulta);
											if($situacao <= 3 && is_numeric($existe_transferencia))
											{//if($turma['cod_turma'] == 757)
													//	echo "1-$cod_matricula-$matricula_reclassificacao<br>";
												if($ativo == 0)
												{
													$total_eliminado_enturmacao++;
													if ($depurar) {
														echo("EL. ENTURMACAO $cod_matricula           1<br>");
													}
													if( $sexo == 'M' )
														$total_eliminado_enturmacao_masc++;
													else
														$total_eliminado_enturmacao_fem++;
												}
											}
											else
											{
												if($situacao == 5)
												{
													$total_eliminado_reclassificacao++;

													if( $sexo == 'M' )
														$total_eliminado_reclassificacao_masc++;
													else
														$total_eliminado_reclassificacao_fem++;
												}
												if($situacao == 4 && is_numeric($existe_transferencia))
												{

													$total_eliminado_transferencia++;
													if ($depurar)
														echo "EL. TRANSFERENCIA ".$cod_matricula."                    1<br>";
													if( $sexo == 'M' )
														$total_eliminado_transferencia_masc++;
													else
														$total_eliminado_transferencia_fem++;

													/**
													 * se for a primeira enturmacao mesmo que tenha
													 * sido eliminado deve contabilizar uma
													 * admissao por enturmacao
													 */

													if($eh_primeira_enturmacao_desta_matricula && !$primeira_matricula_eh_transferencia && !$foi_admitido_transferencia)
													{
														//if($turma['cod_turma'] == 40)
														//echo "1-$cod_matricula<br>";
														echo("AD ENTURMACAO $cod_matricula             aaaaaaaaaaaaaa<br>");
														$total_admitido_enturmacao++;

														if( $sexo == 'M' )
															$total_admitido_enturmacao_masc++;
														else
															$total_admitido_enturmacao_fem++;
													}
												}
												elseif($situacao == 6)
												{
													$total_abandono++;

													if( $sexo == 'M' )
														$total_abandono_masc++;
													else
														$total_abandono_fem++;
												}elseif($situacao == 1)
												{
													$total_aprovado++;

													if( $sexo == 'M' )
														$total_aprovado_masc++;
													else
														$total_aprovado_fem++;
												} /*03/07/2007*/
												elseif($situacao == 4 && !is_numeric($existe_transferencia) && is_numeric($eh_primeira_enturmacao_desta_matricula) && !$primeira_matricula_eh_transferencia)
												{
													//if($turma['cod_turma'] == 450)
													//	echo "1-$cod_matricula<br>";

													if ($depurar) {
														echo("AD ENTURMACAO $cod_matricula       2<br>");
														$matriculas[]=$cod_matricula;
													}
													$total_admitido_enturmacao++;
													
													if($sexo == 'M' )
														$total_admitido_enturmacao_masc++;
													else 													
														$total_admitido_enturmacao_fem++;
												}
											}
										}
										else
										{
											
											if($ativo == 0)
											{
												if($sequencial <= $max_sequencial)
												{
															//if($turma['cod_turma'] == 757)
														//echo "2-$cod_matricula-$matricula_reclassificacao<br>";
													$total_eliminado_enturmacao++;
													if ($depurar) {
														echo("EL. ENTURMACAO $cod_matricula          2<br>");
													}
													if( $sexo == 'M' )
														$total_eliminado_enturmacao_masc++;
													else
														$total_eliminado_enturmacao_fem++;
														
													if(is_numeric($eh_primeira_enturmacao_desta_matricula) && !$existe_transferencia && !$matricula_reclassificacao)
													{
														if ($depurar) {
															echo("AD ENTURMACAO $cod_matricula       3<br>");	
															$matriculas[]=$cod_matricula;	
														}	
														
														$total_admitido_enturmacao++;
														
														if( $sexo == 'M' )
															$total_admitido_enturmacao_masc++;
														else
															$total_admitido_enturmacao_fem++;
													}
													
												}
												/**
												 * se a matricula foi eliminada mas
												 * for a primeira enturmacao deve contar
												 * como admitido tambem
												 */
												elseif(is_numeric($eh_primeira_enturmacao_desta_matricula))
												{
													if($primeira_matricula_eh_transferencia)
													{
														if ($depurar) {
															echo("AD TRANSFERENCIAAA $cod_matricula            2<BR>");
															$matriculas[]=$cod_matricula;
														}
														if (!is_numeric(array_search($cod_matricula,$matriculas_transferencia))) {
															$total_admitido_transferencia++;
															if( $sexo == 'M' )
																$total_admitido_transferencia_masc++;
															else
																$total_admitido_transferencia_fem++;
														}
													}
													elseif(!$existe_transferencia && !$matricula_reclassificacao)
													{

														if ($depurar) {
															echo("AD ENTURMACAO $cod_matricula       3<br>");	
															$matriculas[]=$cod_matricula;	
														}	
														
														$total_admitido_enturmacao++;
														
														if( $sexo == 'M' )
															$total_admitido_enturmacao_masc++;
														else
															$total_admitido_enturmacao_fem++;														
													}
												}
											}
										}

									} // ultima matricula   ///////**************************************STOP HERE
									else
									{
//echo "ref_cod_matricula = $cod_matricula
													    //AND sequencial = $sequencial";
										
										$consulta = "SELECT 1
													   FROM pmieducar.matricula_turma mt1
													  WHERE ref_cod_matricula = $cod_matricula
													    AND sequencial = $sequencial
														AND ref_cod_turma = {$turma['cod_turma']}
													    AND (data_cadastro, COALESCE(data_exclusao,now())) = ( SELECT data_cadastro, COALESCE(data_exclusao,now())
																												 FROM pmieducar.matricula_turma mt2
																												WHERE ref_cod_matricula = $cod_matricula
																												ORDER BY data_cadastro asc,data_exclusao asc
																												LIMIT 1 )
																								AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
																									  )
																							ORDER BY data_cadastro asc,data_exclusao asc,ativo ";
																					/*OR to_char( data_exclusao,'MM')::int = '{$this->mes}'*/
										$eh_primeira_enturmacao_desta_matricula = $db2->CampoUnico($consulta);

										$consulta = "SELECT matricula_reclassificacao
										               FROM pmieducar.matricula
										              WHERE cod_matricula = {$cod_matricula}";

										$matricula_reclassificacao = $db2->CampoUnico($consulta);

										/**
										 * verifica se eh a primeira matricula do aluno
										 */
										$obj_matricula_aluno = new clsPmieducarMatricula();
										$lst_matricula_aluno = $obj_matricula_aluno->lista(null, null, null, null, null, null, $det_aluno['cod_aluno']);
										$eh_primeira_matricula_do_aluno = count($lst_matricula_aluno) == 1 ? 1 : 0;

										$consulta = "SELECT 1
													   FROM pmieducar.matricula
													  WHERE cod_matricula = {$cod_matricula}
													    AND matricula_transferencia = true";

										$primeira_matricula_eh_transferencia = $db2->CampoUnico($consulta);
											
										if (is_numeric($primeira_matricula_eh_transferencia))
										{
											$matriculas_transferencia[$cod_matricula] = $cod_matricula;
											$total_admitido_transferencia++;
											if ($sexo == 'M')
												$total_admitido_transferencia_masc++;
											else 
												$total_admitido_transferencia_fem++;
										}
										
										if($eh_primeira_enturmacao_desta_matricula && $matricula_reclassificacao)
										{
											$total_admitido_reclassificao++;
												if ($depurar) {
													$matriculas[]=$cod_matricula;
												}
											if( $sexo == 'M' )
												$total_admitido_reclassificao_masc++;
											else
												$total_admitido_reclassificao_fem++;
										}

										else
										{
											 $consulta = "SELECT 1
														   FROM pmieducar.transferencia_solicitacao
														  WHERE ref_cod_matricula_entrada = {$cod_matricula}
														    AND ativo = 1
															AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
															      OR to_char( data_exclusao,'MM')::int = '{$this->mes}' )";

											$existe_transferencia = (int)$db2->CampoUnico($consulta);
												//if($turma['cod_turma'] == 33 && $existe_transferencia)
												//	echo "$cod_matricula<br>";
											if ((is_numeric($eh_primeira_enturmacao_desta_matricula) && ($existe_transferencia) && $existe_transferencia == 1) || (is_numeric($eh_primeira_enturmacao_desta_matricula) && $eh_primeira_matricula_do_aluno && $primeira_matricula_eh_transferencia))
											{
												if ($depurar) {
													echo("AD TRANSFERENCIA $cod_matricula          3<br>");
													$matriculas[]=$cod_matricula;
												}
												if (!is_numeric(array_search($cod_matricula,$matriculas_transferencia))) {
													$total_admitido_transferencia++;
													if( $sexo == 'M' )
														$total_admitido_transferencia_masc++;
													else
														$total_admitido_transferencia_fem++;
												}
											}
											else
											{

												//if($det_matricula['aprovado'] != 4)
//												{
													//if($turma['cod_turma'] == 450)
														//echo "2-$cod_matricula<br>";
												if (!is_numeric($primeira_matricula_eh_transferencia)) {
													$total_admitido_enturmacao++;
													if ($depurar) {
														echo("AD ENTURMACAO $cod_matricula       4<br>");
														$matriculas[]=$cod_matricula;
													}
													if( $sexo == 'M' )
														$total_admitido_enturmacao_masc++;
													else 
														$total_admitido_enturmacao_fem++;
												}
											}
										}

										/**
										 * verifica se eh a ultima enturmacao desta matricula
										 */
										$consulta = "SELECT ref_cod_turma
													   FROM pmieducar.matricula_turma mt1
													  WHERE ref_cod_matricula = $cod_matricula
														AND sequencial = $sequencial
													    AND sequencial = (SELECT max(sequencial)
													   FROM pmieducar.matricula_turma mt2
													  WHERE ref_cod_matricula = $cod_matricula)
													order by data_cadastro desc limit 1";
										$ultima_turma_enturmacao = $db2->CampoUnico($consulta);
//echo "$cod_matricula - $ultima_turma_enturmacao == {$turma['cod_turma']}";
										if($ultima_turma_enturmacao == $turma['cod_turma'])
										{
											$consulta = "SELECT aprovado
											               FROM pmieducar.matricula
											              WHERE cod_matricula = {$cod_matricula}";

											$situacao = $db2->CampoUnico($consulta);

											if($situacao <= 3)
											{
												if($ativo == 0)
												{//if($turma['cod_turma'] == 757)
														//echo "3-$cod_matricula-$matricula_reclassificacao<br>";
													
													$total_eliminado_enturmacao++;
														
													if( $sexo == 'M' )
														$total_eliminado_enturmacao_masc++;
													else
														$total_eliminado_enturmacao_fem++;
												}
												else
												{

													$consulta = "SELECT 1
														   FROM pmieducar.transferencia_solicitacao
														  WHERE ref_cod_matricula_saida = {$cod_matricula}
														    AND (ativo = 1 OR data_transferencia IS NOT NULL)
															AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
															      OR to_char( data_exclusao,'MM')::int = '{$this->mes}' )
															AND(to_char(data_transferencia,'MM')::int = '{$this->mes}' OR data_transferencia IS NULL)";

													$existe_transferencia = $db2->CampoUnico($consulta);

													if (is_numeric($existe_transferencia) && $existe_transferencia == 1)
													{

														$total_eliminado_transferencia++;
														if ($depurar)
															echo "EL TRANSFERENCIA ".$cod_matricula."              2<br>";
														if( $sexo == 'M' )
															$total_eliminado_transferencia_masc++;
														else
															$total_eliminado_transferencia_fem++;
													}
													//echo '<br>->'.$existe_transferencia = $db->CampoUnico($consulta);
													//echo '<-<br>';

												}



											}
											else
											{ //echo $turma['cod_turma'].'b';
//echo "enter.$cod_matricula";

												$consulta = "SELECT 1
															   FROM pmieducar.transferencia_solicitacao
															  WHERE ref_cod_matricula_saida = {$cod_matricula}
																AND (ativo = 1 OR data_transferencia IS NOT NULL)
																AND ( to_char(data_cadastro,'MM')::int = '{$this->mes}'
																      OR to_char( data_exclusao,'MM')::int = '{$this->mes}' )
																AND(to_char(data_transferencia,'MM')::int = '{$this->mes}' OR data_transferencia IS NULL)";

												$existe_transferencia = $db2->CampoUnico($consulta);

//echo $situacao.'<br>';
												//if($turma['cod_turma'] == 33)
													//echo "$cod_matricula<br>";
												if($situacao == 5)
												{
													$total_eliminado_reclassificacao++;

													if( $sexo == 'M' )
														$total_eliminado_reclassificacao_masc++;
													else
														$total_eliminado_reclassificacao_fem++;
												}
												elseif (is_numeric($existe_transferencia) && $existe_transferencia == 1)
												{
													$total_eliminado_transferencia++;
													if ($depurar)
														echo "EL TRANSFERENCIA ".$cod_matricula."           3<br>";
													if( $sexo == 'M' )
														$total_eliminado_transferencia_masc++;
													else
														$total_eliminado_transferencia_fem++;
												}
												elseif ($situacao == 6)
												{
													$total_abandono++;

													if( $sexo == 'M' )
														$total_abandono_masc++;
													else
														$total_abandono_fem++;
												}
												elseif ($situacao == 1 || $situacao == 2)
												{
													$total_aprovado++;

													if( $sexo == 'M' )
														$total_aprovado_masc++;
													else
														$total_aprovado_fem++;
												}


											}
										}										

									}
								}
							}
							

							//							die(implode(",",$matriculas));
							//ADMITIDOS ativo = 1
							//admitido por enturmacao no mes atual

							
							//QUANTIDADES ENTURMACAO ADMITIDOS
							//M
							$quantidades[3] = $total_admitido_enturmacao_masc;
							//F
							$quantidades[4] = $total_admitido_enturmacao_fem;
							//T
							$quantidades[5] = $total_admitido_enturmacao;


							//ELIMINADOS ativo = 0


							//QUANTIDADES ENTURMACAO ELIMINADOS
							//M
							$quantidades[6] = $total_eliminado_enturmacao_masc;
							//F
							$quantidades[7] = $total_eliminado_enturmacao_fem;
							//T
							$quantidades[8] = $total_eliminado_enturmacao;



							//RECLASSIFICACAO
							//ADMITIDOS
							//$obj_matriculas->_total = 0;

							//$primeiroDiaDoMes = mktime(0,0,0,9,1,2006);
						 //   $NumeroDiasMes = date('t',$primeiroDiaDoMes);
						 //   $ultimoDiaMes =date('d/m/Y',mktime(0,0,0,9,$NumeroDiasMes,2006));



							/**
							 * busca todas as matriculas marcadas como matricula_reclassificacao de uma serie de um curso
							 * em seguida busca todas as matriculas_turma da matricula_turma ordenado por data e pegando a primeira matricula de reclassificacao
							 * se essa matricula for igual a matricula atual do loop entao esta matricula
							 * é uma reclassificacao
							 */




							//QUANTIDADES RECLASSIFICACAO ADMITIDOS
							//M
							$quantidades[9] = $total_admitido_reclassificao_masc;
							//F
							$quantidades[10] = $total_admitido_reclassificao_fem;
							//T
							$quantidades[11] = $total_admitido_reclassificao;


							//ELIMINADOS
							$obj_matriculas->_total = 0;


							//QUANTIDADES RECLASSIFICACAO ELIMINADOS
							//M
							$quantidades[12] = $total_eliminado_reclassificacao_masc;
							//F
							$quantidades[13] = $total_eliminado_reclassificacao_fem;
							//T
							$quantidades[14] = $total_eliminado_reclassificacao;

							//TRANSFERENCIA
							//ADMITIDOS ativo = 1


							//QUANTIDADES TRASNFERENCIA ADMITIDOS
							//M
							$quantidades[15] = $total_admitido_transferencia_masc;
							//F
							$quantidades[16] = $total_admitido_transferencia_fem;
							//T
							$quantidades[17] = $total_admitido_transferencia;


							//ELIMINADOS aprovado = 4
							//TRANSFERENCIA EXTERNA


							//QUANTIDADES TRANFERENCIA EXTERNO ELIMINADOS
							//M
							$quantidades[18] = $total_eliminado_transferencia_masc;
							//F
							$quantidades[19] = $total_eliminado_transferencia_fem;
							//T
							$quantidades[20] = $total_eliminado_transferencia ;

							//ABANDONO
							/*$db2 = new clsBanco();
							$consulta = "
										SELECT count(1)
											   FROM pmieducar.matricula_turma
												,pmieducar.matricula
											  WHERE cod_matricula = ref_cod_matricula
											    AND ref_cod_turma = {$turma['cod_turma']}
											    AND aprovado = 6
											    AND matricula_turma.ativo = 1
										            AND matricula.ativo = 1
												AND ( to_char( matricula_turma.data_cadastro,'MM')::int = {$this->mes}
												      OR to_char(  matricula_turma.data_exclusao,'MM')::int = {$this->mes} )
										";

							$total_transf = $db2->CampoUnico($consulta)	;*/
							//QUANTIDADES ABANDONO
							//M
							$quantidades[21] = $total_abandono_masc;
							//F
							$quantidades[22] = $total_abandono_fem;
							//T
							$quantidades[23] = $total_abandono;

							//APROVADOS

							//QUANTIDADES APROVADOS
							//M
							$quantidades[24] = $total_aprovado_masc;
							//F
							$quantidades[25] = $total_aprovado_fem;
							//T
							$quantidades[26] = $total_aprovado;

							/**
							 * Inicio linha
							 */
							$altura_linha = 18;

							$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_linha);
							$largura_linha = 18;
							for($ct = 294 ;$ct < 294+(30 * 17); $ct+=$largura_linha)
							{
								//159
								$this->pdf->linha_relativa($ct,$this->page_y,0,18);

							}
							//n
							$this->pdf->linha_relativa(50,$this->page_y,0,18);
							//ciclo
							$this->pdf->linha_relativa(134,$this->page_y,0,18);
							//turno
							$this->pdf->linha_relativa(149,$this->page_y,0,18);
							//professor
							$this->pdf->linha_relativa(275,$this->page_y,0,18);
							$largura_linha = 18;

							$index = 0;

							for($ct = 260 ;$ct < 260+(26 * 18); $ct+=$largura_linha*3)
							{
								$this->pdf->escreve_relativo( $quantidades[$index] == 0 ? '' : $quantidades[$index], $ct ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
								$this->pdf->escreve_relativo( $quantidades[$index+1] == 0 ? '' : $quantidades[$index+1], $ct + $largura_linha ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
								$this->pdf->escreve_relativo($quantidades[$index+2], $ct + $largura_linha * 2,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );


								$total_serie[$index]   += $quantidades[$index];
								$total_serie[$index+1] += $quantidades[$index + 1];
								$total_serie[$index+2] += $quantidades[$index + 2];

								$total_curso[$index]   += $quantidades[$index];
								$total_curso[$index+1] += $quantidades[$index + 1];
								$total_curso[$index+2] += $quantidades[$index + 2];

								$index +=3;
							}
//							echo '<pre>';print_r($total_curso);
							//matricula final
							// ( MI + Adm.Entur + Adm.Recla + Adm1.Transf ) - ( Elim.Entur + Elim.Recla + Elim.Transf + Abandono )
							$quantidades[27] = ( $quantidades[0] + $quantidades[3] + $quantidades[9] + $quantidades[15] ) - (  $quantidades[6] + $quantidades[12] + $quantidades[18] + $quantidades[21] );// - $diminui_masc;
							//F
							$quantidades[28] = ( $quantidades[1] + $quantidades[4] + $quantidades[10] + $quantidades[16] ) - ( $quantidades[7] + $quantidades[13] + $quantidades[19] + $quantidades[22] );// - $diminui_fem;
							//T
							$quantidades[29] = ( $quantidades[2] + $quantidades[5] + $quantidades[11] + $quantidades[17] ) - ( $quantidades[8] + $quantidades[14] + $quantidades[20] + $quantidades[23] );// - $diminui_fem - $diminui_masc;

							$this->pdf->escreve_relativo( $quantidades[$index], $ct ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
							$this->pdf->escreve_relativo( $quantidades[$index+1], $ct + $largura_linha ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
							$this->pdf->escreve_relativo($quantidades[$index+2], $ct + $largura_linha * 2,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );

							$total_serie[$index]   += $quantidades[$index];
							$total_serie[$index+1] += $quantidades[$index + 1];
							$total_serie[$index+2] += $quantidades[$index + 2];

							$total_curso[$index]   += $quantidades[$index];
							$total_curso[$index+1] += $quantidades[$index + 1];
							$total_curso[$index+2] += $quantidades[$index + 2];


							$expande = 24;
							$numero_x = 12 + $expande;
							$this->pdf->escreve_relativo( "", $numero_x,$this->page_y  + 3, 50, 50, $fonte, 7, $corTexto, 'left' );
							//posicao ciclo
							$ciclo_x =  $expande + 15;
							$this->pdf->escreve_relativo( "{$turma['nm_turma']}", $ciclo_x,$this->page_y  + 3 , 100, 50, $fonte, 7, $corTexto, 'center' );
							//posicao turno
							$turno_x = $ciclo_x + $expande + 28;
							$this->pdf->escreve_relativo( "$turno", $turno_x ,$this->page_y  + 3 , 100, 40, $fonte, 7, $corTexto, 'center' );

							$professor_x = 125 + $expande;
							$this->pdf->escreve_relativo( "$nm_servidor", $professor_x + 5,$this->page_y  + 5 , 100, 40, $fonte, 6, $corTexto, 'center' );

							$this->page_y +=18;
							/**
							 * Fim linha
							 */
							if($this->page_y + $altura_linha > 498)
							{
								$this->pdf->ClosePage();
								$this->pdf->OpenPage();
								$this->addCabecalho();
								$this->novoCabecalho();
							}
													
						}
					}

						/**
						 * subtototal
						 */
						$index = 0;
						$altura_linha = 18;
						$largura_linha = 18;

						if($lista_serie_turmas)

						{


						$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_linha);

						for($ct = 294 ;$ct < 294+(30 * 17); $ct+=$largura_linha)
						{
							$this->pdf->linha_relativa($ct,$this->page_y,0,18);

						}

						$this->pdf->linha_relativa(50,$this->page_y,0,18);

						$this->pdf->linha_relativa(275,$this->page_y,0,18);


							for($ct = 260 ;$ct < 260+(30 * 18); $ct+=$largura_linha*3)
							{

								$this->pdf->escreve_relativo( $total_serie[$index], $ct ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
								$this->pdf->escreve_relativo($total_serie[$index +1], $ct + $largura_linha ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
								$this->pdf->escreve_relativo( $total_serie[$index+2], $ct + $largura_linha * 2,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
								$index += 3;
							}

							$expande = 24;
							$numero_x = 12 + $expande;
							$this->pdf->escreve_relativo( "$total_turmas_serie", $numero_x,$this->page_y  + 3, 50, 50, $fonte, 7, $corTexto, 'left' );

							$professor_x = 40 + $expande;
							$this->pdf->escreve_relativo( "Subtotal {$nm_serie}", $professor_x + 5,$this->page_y  + 3 , 150, 40, $fonte, 7, $corTexto, 'center' );

							$this->page_y +=18;
						}

						/**
						 *
						 */
						if($this->page_y + $altura_linha > 498)
						{
							$this->pdf->ClosePage();
							$this->pdf->OpenPage();
							$this->addCabecalho();
							$this->novoCabecalho();
						}

				}

					if($total_curso[0] > 0)
					{
						/**
						 * TOTAL CURSO
						 */
						$altura_linha = 18;

						$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_linha);

						for($ct = 294 ;$ct < 294+(30 * 17); $ct+=$largura_linha)
						{

							$this->pdf->linha_relativa($ct,$this->page_y,0,18);

						}
						//n
						$this->pdf->linha_relativa(50,$this->page_y,0,18);

						$this->pdf->linha_relativa(275,$this->page_y,0,18);

						$index = 0;
						for($ct = 260 ;$ct < 260+(30 * 18); $ct+=$largura_linha*3)
						{

							$this->pdf->escreve_relativo( $total_curso[$index], $ct ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
							$this->pdf->escreve_relativo($total_curso[$index +1], $ct + $largura_linha ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
							$this->pdf->escreve_relativo( $total_curso[$index+2], $ct + $largura_linha * 2,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
							$index += 3;
						}

						$expande = 24;
						$numero_x = 12 + $expande;
						$this->pdf->escreve_relativo( "$total_turmas_curso", $numero_x,$this->page_y  + 3, 50, 50, $fonte, 7, $corTexto, 'left' );

						$obj_curso = new clsPmieducarCurso($curso);
						$det_curso = $obj_curso->detalhe();
						$nm_curso = $det_curso['nm_curso'];
						$professor_x = 40 + $expande;
						$this->pdf->escreve_relativo( "Total {$nm_curso}", $professor_x + 5,$this->page_y  + 3 , 150, 40, $fonte, 7, $corTexto, 'center' );

						$this->page_y +=18;
					}
					
					foreach ($total_curso as $key => $valor) {
						$quantidade_total[$key] += $valor;
					}
					
						/**
						 *
						 */
					if($this->page_y > 498)
					{
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->addCabecalho();
						$this->novoCabecalho();
					}
			}
		}
		
		$altura_linha = 18;

		$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_linha);

		for($ct = 294 ;$ct < 294+(30 * 17); $ct+=$largura_linha)
		{

			$this->pdf->linha_relativa($ct,$this->page_y,0,18);

		}
		//n
		$this->pdf->linha_relativa(50,$this->page_y,0,18);

		$this->pdf->linha_relativa(275,$this->page_y,0,18);

		$index = 0;
		for($ct = 260 ;$ct < 260+(30 * 18); $ct+=$largura_linha*3)
		{

			$this->pdf->escreve_relativo( $quantidade_total[$index], $ct ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
			$this->pdf->escreve_relativo($quantidade_total[$index +1], $ct + $largura_linha ,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
			$this->pdf->escreve_relativo( $quantidade_total[$index+2], $ct + $largura_linha * 2,$this->page_y  + 3, 50, 40, $fonte, 7, $corTexto, 'center' );
			$index += 3;
		}

		$expande = 24;
		$numero_x = 12 + $expande;
		$this->pdf->escreve_relativo( "$total_turmas_curso", $numero_x,$this->page_y  + 3, 50, 50, $fonte, 7, $corTexto, 'left' );

		$obj_curso = new clsPmieducarCurso($curso);
		$det_curso = $obj_curso->detalhe();
		$nm_curso = $det_curso['nm_curso'];
		$professor_x = 40 + $expande;
		$this->pdf->escreve_relativo( "Total Geral", $professor_x + 20,$this->page_y  + 3 , 150, 40, $fonte, 7, $corTexto, 'center' );

		$this->page_y +=18;



		$this->rodape();
		$this->pdf->ClosePage();

		//header( "location: " . $this->pdf->GetLink() );
		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();


		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";
	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		$this->page_y = 125;

		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 782, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 782, 80, $fonte, 18, $corTexto, 'center' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",136, 70, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 25, 30, 782, 80, $fonte, 10, $corTexto, 'right' );

		//titulo
		$this->pdf->escreve_relativo( "Movimentação Mensal de Alunos - {$this->nm_escola} ", 30, 85, 782, 80, $fonte, 12, $corTexto, 'center' );

		$obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
		$det_modulo = $obj_modulo->detalhe();
		//Data
		$this->pdf->escreve_relativo( "{$this->meses_do_ano[$this->mes]}/{$this->ano}", 45, 100, 535, 80, $fonte, 10, $corTexto, 'left' );
	    $this->pdf->escreve_relativo( "Dias Efetivos:{$this->totalDiasUteis}", 220, 100, 535, 80, $fonte, 10, $corTexto, 'center' );
	}

	function novoCabecalho()
	{
		$altura2 = 300;
		$altura = 50;

		$expande = 24;

		$fonte = 'arial';
		$corTexto = '#000000';

		if($this->page_y + 49 > 498)
		{
			$this->pdf->ClosePage();
			$this->pdf->OpenPage();
			$this->addCabecalho();
			$this->page_y = 125;
		}


		//linha
		$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura);

		//linha professor
		$this->pdf->quadrado_relativo( 30, $this->page_y, 221 + $expande , $altura);

		//linha turno
		$this->pdf->quadrado_relativo( 30, $this->page_y, 95 + $expande , $altura);

	    //linha ciclo
		$this->pdf->quadrado_relativo( 30, $this->page_y, 80 + $expande , $altura);

		//linha n
		$this->pdf->quadrado_relativo( 30, $this->page_y, 20, $altura);

		$centralizado = abs(($altura - 10) / 2) + $this->page_y;

		//posicao n

		$numero_x = 12 + $expande;
		$this->pdf->escreve_relativo( "Nº", $numero_x,$centralizado - 5, 50, 50, $fonte, 7, $corTexto, 'left' );
		//posicao ciclo
		$ciclo_x =  $expande + 15;
		$this->pdf->escreve_relativo( "Ciclo", $ciclo_x,$centralizado - 5, 100, 50, $fonte, 7, $corTexto, 'center' );
		//posicao turno
		$turno_x = $ciclo_x + $expande + 28;
		$this->pdf->escreve_relativo( "T\nu\nr\nn\no", $turno_x ,$centralizado -15, 100, 40, $fonte, 7, $corTexto, 'center' );

		$professor_x = 125 + $expande;
		$this->pdf->escreve_relativo( "Professor", $professor_x + 5,$centralizado - 5 , 100, 40, $fonte, 7, $corTexto, 'center' );

		$matricula_ini = 245 + $expande;
		$this->pdf->escreve_relativo( "Matrícula\nInicial", $matricula_ini + 8,$centralizado - 10 , 50, 40, $fonte, 7, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Enturmação", $matricula_ini + 65,$centralizado - 15 , 100, 40, $fonte, 7, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Reclassificação", $matricula_ini + 172,$centralizado - 15 , 100, 40, $fonte, 7, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Transferências", $matricula_ini + 285,$centralizado - 15 , 100, 40, $fonte, 7, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Abandono", $matricula_ini + 387,$centralizado - 10 , 50, 40, $fonte, 7, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Aprovados", $matricula_ini + 441,$centralizado - 10 , 50, 40, $fonte, 7, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Matrícula\nFinal", $matricula_ini + 493,$centralizado - 15 , 50, 40, $fonte, 7, $corTexto, 'center' );

		for($ct = 335;$ct < 335 +(108 * 3);$ct += 108)
		{
			$this->pdf->escreve_relativo( "Admitidos", $ct,149 , 50, 40, $fonte, 7, $corTexto, 'center' );
			$this->pdf->escreve_relativo( "Eliminados", $ct + 50,149 , 50, 40, $fonte, 7, $corTexto, 'center' );

		}

		$largura_linha = 18;

		$this->pdf->linha_relativa(294+($largura_linha*5),148,0,11);
		$this->pdf->linha_relativa(294+($largura_linha*11),148,0,11);
		$this->pdf->linha_relativa(294+($largura_linha*17),148,0,11);


		//enturmacao
		$this->pdf->linha_relativa(294+($largura_linha*2),125,0,34);

		//enturmacao
		$this->pdf->linha_relativa(294+($largura_linha*8),125,0,34);

		//reclassificacao
		$this->pdf->linha_relativa(294+($largura_linha*14),125,0,34);

		//tranferencia
		$this->pdf->linha_relativa(294+($largura_linha*20),125,0,34);

		//abandono
		$this->pdf->linha_relativa(294+($largura_linha*23),125,0,34);

		//aprovado
		$this->pdf->linha_relativa(294+($largura_linha*26),125,0,34);

		for($ct = 294 ;$ct < 294+(30 * 17); $ct+=$largura_linha)
		{

			$this->pdf->linha_relativa($ct,159,0,18);

		}

		for($ct = 260 ;$ct < 260+(30 * 18); $ct+=$largura_linha*3)
		{

			$this->pdf->escreve_relativo( "M", $ct ,162 , 50, 40, $fonte, 7, $corTexto, 'center' );
			$this->pdf->escreve_relativo( "F", $ct + $largura_linha ,162 , 50, 40, $fonte, 7, $corTexto, 'center' );
			$this->pdf->escreve_relativo( "T", $ct + $largura_linha * 2,162 , 50, 40, $fonte, 7, $corTexto, 'center' );

		}

		//divisao de admitidos e eliminados
		$this->pdf->linha_relativa(330,$this->page_y + 23,324,0);


		$this->pdf->linha_relativa(275,$this->page_y + 34,537,0);

		$largura_anos = 590;

		$reta_ano_x = 209 ;


		$this->page_y +=49;
//for($a = 0;$a< 10;$a++){




	}

	function rodape()
	{
		$corTexto = '#000000';
		$fonte = 'arial';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,756, 100, 50, $fonte, 7, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Assinatura do Diretor(a)", 68,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Assinatura do secretário(a)", 677,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa(52,517,130,0);
		$this->pdf->linha_relativa(660,517,130,0);
	}

	function Editar()
	{
		return false;
	}

	function Excluir()
	{
		return false;
	}

	function buscaDisciplinas($serie)
	{

		$this->array_disciplinas = array();

		$obj_disciplinas = new clsPmieducarDisciplinaSerie();
		$lista_disciplinas = $obj_disciplinas->lista(null,$serie);

		while(sizeof($lista_disciplinas))
		{
			$disciplina = array_shift($lista_disciplinas);
			$obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
			$det_disciplina = $obj_disciplina->detalhe();

			$this->array_disciplinas["{$det_disciplina["cod_disciplina"]}"] = array('ref_cod_disciplina' =>$det_disciplina['cod_disciplina'],'nm_disciplina' => $det_disciplina['abreviatura'],'total_disciplina_abaixo_media_serie' => 0,'total_disciplina_media_serie' => 0,'total_geral_disciplina_abaixo_media' => 0,'total_geral_disciplina_media' => 0);

		}
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();


?>
