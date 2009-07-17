<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * Demonstrativo de defasagem.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Relatório
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';


class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Demonstrativo de alunos defasados idade/s&eacute;rie" );
		$this->processoAp = "653";
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
	//var $totalDiasUteis;
	var $qt_anos = 11;
	var $idade_inicial = 6 ;
	//var $necessidades;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $get_link = false;
	var $cursos = array();

	var $array_ano_idade = array();

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

	function renderHTML()
	{
		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}

		if(empty($this->cursos))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhum curso selecionado!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
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

		$cursos_in = '';
		$conc = '';
		foreach ($this->cursos as $curso)
		{
			$cursos_in .= "{$conc}{$curso}";
			$conc = ",";
		}

		$db= new clsbanco();
		$consulta = "SELECT (SELECT coalesce(min(s.idade_inicial),0) as min
								  FROM pmieducar.serie  s
								       ,pmieducar.turma t
								 WHERE s.cod_serie     = t.ref_ref_cod_serie
								   AND s.ref_cod_curso in ($cursos_in )) as min
								,
								(SELECT coalesce(max(s.idade_final),0)  as max
								  FROM pmieducar.serie  s
								 WHERE s.ref_cod_curso in ( $cursos_in)) as max";
/*		echo $consulta = "SELECT distinct
					       coalesce(min(s.idade_inicial),0) as min
					       ,coalesce(max(s.idade_final),0)  as max
					  FROM pmieducar.serie  s
					       ,pmieducar.turma t
					 WHERE s.cod_serie     = t.ref_ref_cod_serie
					   AND s.ref_cod_curso in ( $cursos_in )";*/
		$db->Consulta($consulta);
		$db->ProximoRegistro();
		$max_min = $db->Tupla();


		$consulta = "SELECT distinct
					       coalesce(s.idade_inicial,0) as min
					       ,coalesce(s.idade_final,0)  as max
					  FROM pmieducar.serie  s
					 WHERE  s.ref_cod_curso in ( $cursos_in )";
	/*	$consulta = "SELECT distinct
					       coalesce(s.idade_inicial,0) as min
					       ,coalesce(s.idade_final,0)  as max
					  FROM pmieducar.serie  s
					       ,pmieducar.turma t
					 WHERE s.cod_serie     = t.ref_ref_cod_serie
					   AND s.ref_cod_curso in ( $cursos_in )";*/

		$faixa_min_max = array();

		$db->Consulta($consulta);
		while($db->ProximoRegistro())
			$numeros[] = $db->Tupla();

		$faixa_min_max = array($numeros[0][0],$numeros[count($numeros)-1][1]);

		$consulta2 = "SELECT distinct
					         s.idade_inicial
					    FROM pmieducar.serie  s
					   WHERE  s.ref_cod_curso in ( $cursos_in )

			   		   UNION

					  SELECT distinct
					         s.idade_final
					    FROM pmieducar.serie  s
					   WHERE s.ref_cod_curso in ( $cursos_in ) ";

		/* $consulta2 = "SELECT distinct
					         s.idade_inicial
					    FROM pmieducar.serie  s
					         ,pmieducar.turma t
					   WHERE s.cod_serie     = t.ref_ref_cod_serie
					     AND s.ref_cod_curso in ( $cursos_in )

			   		   UNION

					  SELECT distinct
					         s.idade_final
					    FROM pmieducar.serie  s
					         ,pmieducar.turma t
					   WHERE s.cod_serie     = t.ref_ref_cod_serie
					     AND s.ref_cod_curso in ( $cursos_in ) ";*/
		$idades = array();

		$db->Consulta($consulta2);
		while($db->ProximoRegistro())
			$idades[] = array_shift($db->Tupla());

		$consulta3 = "SELECT distinct
					         s.idade_inicial
					    FROM pmieducar.serie  s
					   WHERE s.ref_cod_curso in ( $cursos_in )";
/*$consulta3 = "SELECT distinct
					         s.idade_inicial
					    FROM pmieducar.serie  s
					         ,pmieducar.turma t
					   WHERE s.cod_serie     = t.ref_ref_cod_serie
					     AND s.ref_cod_curso in ( $cursos_in )";*/

		$db->Consulta($consulta3);
		while($db->ProximoRegistro())
			$faixa[] = $db->Tupla();


		$ultima_idade = null;
		while(sizeof($idades))
		{

			$idade = array_shift($idades);
			if($idade == $faixa_min_max[0])
			{
				$ultima_idade = array_shift($idades);
				$this->array_ano_idade[] = array('ano' => ($this->ano - $idade) . " - " .($this->ano -$ultima_idade),'idade' => $idade . " - " . $ultima_idade ,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
				if(!empty($idades))
				{
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 1,'idade' => $ultima_idade + 1,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 2,'idade' => $ultima_idade + 2,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 3,'idade' => $ultima_idade + 3,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$ultima_idade = $ultima_idade + 3;
					while($ultima_idade > $idades[0] + 3)
						$ultima_idade = array_shift($idades);
				}elseif(sizeof($this->array_ano_idade) == 1)
				{
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 1,'idade' => $ultima_idade + 1,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 2,'idade' => $ultima_idade + 2,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 3,'idade' => $ultima_idade + 3,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$ultima_idade = $ultima_idade + 3;
				}

			}

			foreach ($faixa as $key => $value)
			{

				for($ct = $ultima_idade + 1;$ct<= $idade + 3;$ct++)
				{
					$this->array_ano_idade[] = array('ano' => $this->ano - $ct,'idade' => ((sizeof($idades) === 0 && $ct == $idade + 3) ? "" : "" ).$ct,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$ultima_idade = $ct;
				}

				break;

			}
			$ultima_idade = idade > $ultima_idade ? $idade : $ultima_idade;
		}


		$altura2 = 300;
		$altura = 50;

		$expande = 24;

		$fonte = 'arial';
		$corTexto = '#000000';

		$flag_defasado = 1;

		$obj_lst_escola = new clsPmieducarEscola();
		$lst_escola = $obj_lst_escola->lista($this->ref_cod_escola,null,null,$this->ref_cod_instituicao,null,null,null,null,null,null,1);

		if($lst_escola)
		{

			$this->pdf = new clsPDF("Demonstrativo de Alunos Defasados Idade/Série - {$this->ano}", "Demonstrativo de Alunos Defasados Idade/Série - {$this->ano}", "A4", "", false, false);

			$this->pdf->largura  = 842.0;
	  		$this->pdf->altura = 595.0;

			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];


			foreach ($lst_escola as $escola)
			{
				$this->ref_cod_escola = $escola['cod_escola'];

				if($this->ref_cod_escola){

					$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
					$det_escola = $obj_escola->detalhe();
					$this->nm_escola = $det_escola['nome'];

				}

			     $obj_calendario = new clsPmieducarEscolaAnoLetivo();
			     $lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

			     if(!$lista_calendario)
			     {
			     	continue;
			     	/*echo '<script>
			     			alert("Escola não possui calendário definido para este ano");
			     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		</script>';*/
			     	//return true;
			     }


				for($ct_analfabeto = 0;$ct_analfabeto <=2 ;$ct_analfabeto++)
				{

					foreach ($this->array_ano_idade as $key =>$value)
					{
						$this->array_ano_idade [$key]['total_geral'] = 0;
						$this->array_ano_idade [$key]['total_serie'] = 0;
					}

					$this->pdf->OpenPage();
					$this->addCabecalho();
					$this->novaPagina($ct_analfabeto);

					if($ct_analfabeto <= 1)
					{
						$total_geral_alunos = 0;
						$total_geral_turmas = 0;
					}

					$total_turmas_serie = 0;
					$total_alunos_serie = 0;

					$altura_linha = 23;
					$inicio_escrita_y = 175;

					foreach ($this->cursos as $curso){

						$obj = new clsPmieducarSerie();
						$obj->setOrderby("idade_inicial,idade_final");
						$lista_serie_curso = $obj->lista(null,null,null,$curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao,         null, null,null,$this->ref_cod_escola);

						if($lista_serie_curso){
							foreach ($lista_serie_curso as $id_serie => $serie) {
							/**
							 * busca todas as matriculas de cada curso
							 */
							if(empty($serie['idade_inicial']) || empty($serie['idade_final']))
								continue;
								//$subtotal_serie = 0;
								$total_turmas_serie = 0;
								$total_alunos_serie = 0;


								foreach ($this->array_ano_idade as $key =>$value)
								{

									$this->array_ano_idade [$key]['total_serie'] = 0;
								}

								$possui_turmas = false;
								if($ct_analfabeto <= 1)
								{
									$obj_turma = new clsPmieducarTurma();
									$turmas = count($obj_turma->lista(null,null,null,$serie['cod_serie'],$this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1));
//									if ($turmas > 0) {
										$possui_turmas = true;
										$total_geral_turmas = $total_geral_turmas +  $turmas;
										$total_turmas_serie = $turmas;

										$total_geral_turmas_ambos = $total_geral_turmas;

										$obj_matricula = new clsPmieducarMatricula();

	//									$lista_matricula_serie = $obj_matricula->lista(null,null,$this->ref_cod_escola,$serie['cod_serie'],null,null,null,array(1,2,3),null,null,null,null,1,$this->ano,$curso,$this->ref_cod_instituicao,null,null,null,$ct_analfabeto,null,null,null,null,null,null,null,$this->mes,true);
										$lista_matricula_serie = $obj_matricula->lista(null,null,$this->ref_cod_escola,$serie['cod_serie'],null,null,null,array(1,2,3/*,4*/),null,null,null,null,1,$this->ano,$curso,$this->ref_cod_instituicao,null,null,null,$ct_analfabeto,null,null,null,null,null,null,null,$this->mes,true);
										$total_geral_alunos += $obj_matricula->_total;
										$total_alunos_serie = $obj_matricula->_total;
										$total_geral_alunos_ambos = (int)$total_geral_alunos_ambos + $obj_matricula->_total;
										$total_alunos_serie_ambos[$serie['cod_serie']] = (int)$total_alunos_serie_ambos[$serie['cod_serie']] + $obj_matricula->_total;
										$total_turmas_serie_ambos[$serie['cod_serie']] = $turmas;

//									}
								}


								if($lista_matricula_serie && $ct_analfabeto <= 1/* && $possui_turmas*/)
								{

									/**
									 * busca dados da matricula de um aluno de uma turma de uma serie =p
									 */
									foreach ($lista_matricula_serie as $matricula)
									{

										//$obj_aluno = new clsPmieducarAluno();

										//$det_aluno = array_shift($obj_aluno->lista($matricula['ref_cod_aluno'],null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, true ));

										//$obj_fisica = new clsFisica($det_aluno['ref_idpes']);
										//$det_fisica = $obj_fisica->detalhe();
										if($matricula['data_nasc'])
										{
											$ano_nasc = explode("-",$matricula['data_nasc']);
											$idade_aluno = date("Y") - $ano_nasc[0];
										}

										/*if($matricula['data_nasc'])
										{
											$dia = (($this->mes == date('n') && $this->ano == date('y')) ? date('j') : 1);
											$ano_nasc = explode("-",$matricula['data_nasc']);
											$idade_aluno = $this->ano - $ano_nasc[0];
											if($this->mes < (int)$ano_nasc[1])
												$idade_aluno--;
											elseif ($this->mes == (int)$ano_nasc[1] && $dia < (int)$ano_nasc[2])
												$idade_aluno--;
										}*/

										//$obj_matricula_turma = new clsPmieducarMatriculaTurma();
										//$det_matricula_turma = $obj_matricula_turma->lista($matricula['cod_matricula'],null,null,null,null,null,null,null,1,$serie['cod_serie'],$curso,$this->ref_cod_escola,$this->ref_cod_instituicao);

										foreach ($this->array_ano_idade as $key => $value) {

											if(strpos($value['idade'],"-") && is_numeric($idade_aluno))
											{
												$idade = explode("-",$value['idade'] );
												if(    ( $idade_aluno > $serie['idade_final'] + $flag_defasado
														&& $idade_aluno >= $idade[0]
														&& $idade_aluno <= $idade[1])
													||
														($idade_aluno > $serie['idade_final'] + $flag_defasado
														&& $key == count($this->array_ano_idade) - 1)
													)
												{
													$this->array_ano_idade[$key]['total_serie'] +=1;
													$this->array_ano_idade[$key]['total_geral'] +=1;
													$this->array_ano_idade[$key]['total_geral_ambos'] +=1;
													break;
												}
											}
											elseif( is_numeric($idade_aluno))
											{
												$idade = $value['idade'] ;
												if($idade_aluno > $serie['idade_final'] + $flag_defasado
													&& $idade_aluno == $idade
												||
													$idade_aluno >= $idade &&
													$key == count($this->array_ano_idade) - 1)
												{
													$this->array_ano_idade[$key]['total_serie'] +=1;
													$this->array_ano_idade[$key]['total_geral'] +=1;
													$this->array_ano_idade[$key]['total_geral_ambos'] +=1;
													break;
												}
											}
										}
									}

								}
							/**
							 * INFORMACOES
							 */


							/**
							 *  linha
							 */


								$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 782, $altura_linha);

								//linha alfabetizados
								$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 695 + $expande, $altura_linha);

								//linha numero de alunos
								$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 90 + $expande , $altura_linha);

							    //linha numero de turmas
								$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 60 + $expande , $altura_linha);
								//$this->pdf->quadrado_relativo( 30, 125, 170 + $expande + 30, $altura);

								//linha serie
								$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 55, $altura_linha);

								//posicao serie
								$serie_x = 35;
								$this->pdf->escreve_relativo( $serie['nm_serie'], $serie_x,$inicio_escrita_y + 5, 50, 50, $fonte, 6, $corTexto, 'left' );
								//posicao numero turmas
								$turma_x = 30 + $expande;

								$total = $ct_analfabeto <= 1 ? $total_turmas_serie : $total_turmas_serie_ambos[$serie['cod_serie']];

								$this->pdf->escreve_relativo( $total, $turma_x -5,$inicio_escrita_y + 5, 100, 50, $fonte, 10, $corTexto, 'center' );
								//posicao numero alunos
								$alunos_x = 40 + $expande;

								$total = $ct_analfabeto <= 1 ? $total_alunos_serie : $total_alunos_serie_ambos[$serie['cod_serie']];

								$this->pdf->escreve_relativo( $total, $alunos_x + 15,$inicio_escrita_y + 5, 100, 40, $fonte, 10, $corTexto, 'center' );


							//	$largura_quadrado = $incremental-5;
								//$this->qt_anos = 1;
								$largura_anos = 615;
								//$this->idade_inicial = 6;
								$array_ano_idade = array();
								//$incremental = (int)($largura_anos/ $this->qt_anos);
								if(sizeof($this->array_ano_idade))
									$incremental = (int)($largura_anos/ sizeof($this->array_ano_idade));
								else
									$incremental = 0;
								$reta_ano_x = $alunos_x + 80;
								$largura_quadrado = $incremental-5;

								//$incremental = (int)($largura_anos/ $this->qt_anos);
							//	$reta_ano_x = $alunos_x + 85;
								$ajuste = 0;
								//for($ct = $this->ano - $this->idade_inicial ;$ct > $this->ano - $this->idade_inicial - $this->qt_anos;$ct--)

								foreach ($this->array_ano_idade as $key => $ano)
								{

									//ajuste2

									$ajuste = 5;

									$total_valor = $ct_analfabeto <= 1 ? $ano["total_serie"] : $total_serie_ano["{$serie['cod_serie']}"][$key];

									if($ano['idade'] <= $serie['idade_final'] + $flag_defasado && $key < sizeof($this->array_ano_idade) - 1)
										$this->pdf->quadrado_relativo( $reta_ano_x ,$inicio_escrita_y, $largura_quadrado + $ajuste + ($key == sizeof($this->array_ano_idade)-1? -5 : 0), $altura_linha,0.5,"#A1B3BD","");
									else
										$this->pdf->escreve_relativo($total_valor, $reta_ano_x + 1 ,$inicio_escrita_y + 5, $incremental, $altura_linha, $fonte, 10, $corTexto, 'center' );


									$total_serie_ano["{$serie['cod_serie']}"][$key] = (int)$total_serie_ano["{$serie['cod_serie']}"][$key] + $ano["total_serie"];



									//$anos_x += $incremental;
									$reta_ano_x += $incremental ;
									//reta
									if($key < sizeof($this->array_ano_idade) - 1){
										$this->pdf->linha_relativa($reta_ano_x ,$inicio_escrita_y,0,$altura_linha);

									}
									else
									$largura_quadrado+=4.5;


								}

								if($ct_analfabeto == 2)
								{
									//echo '<pre>';print_r($total_serie_ano);die;
								}


								//$campo_total = "total_serie";//$ct_analfabeto <= 1 ? "total_serie" : "total_ambos_geral";
								$total_defasado_serie = 0;
								foreach ($this->array_ano_idade as $key =>$value)
								{

									$total_defasado_serie += $this->array_ano_idade [$key]["total_serie"];

								}//

								$total_alunos_serie = $total_alunos_serie == 0 ? 1 : $total_alunos_serie;


								if($ct_analfabeto <= 1){
									$total_serie_ano["{$serie['cod_serie']}"]["total_defasado_serie"] = (int)$total_serie_ano["{$serie['cod_serie']}"]["total_defasado_serie"] + $total_defasado_serie;
									$total_defasado_serie_ambos = (int)$total_defasado_serie_ambos + $total_defasado_serie;

								}
								//$total_serie_ano["{$serie['cod_serie']}"]["total_porcent_defasado_serie"] = round(($total_defasado_serie / $total_alunos_serie) * 100) . "%";

								//$total["{$serie['cod_serie']}"] = $ano["total_serie"];
								$total_defasado_serie = $ct_analfabeto <= 1 ? $total_defasado_serie : $total_serie_ano["{$serie['cod_serie']}"]["total_defasado_serie"];
								$this->pdf->escreve_relativo( $total_defasado_serie, 750 ,$inicio_escrita_y + 5, 30, $altura, $fonte, 10, $corTexto, 'center' );

								if($ct_analfabeto <= 1)
									$this->pdf->escreve_relativo( round(($total_defasado_serie / $total_alunos_serie) * 100) . "%", 780 ,$inicio_escrita_y + 5, 30, $altura, $fonte, 10, $corTexto, 'center' );
								else
									$this->pdf->escreve_relativo( round(($total_serie_ano["{$serie['cod_serie']}"]["total_defasado_serie"] / $total_alunos_serie_ambos[$serie['cod_serie']]) * 100) . "%", 780 ,$inicio_escrita_y + 5, 30, $altura, $fonte, 10, $corTexto, 'center' );

								$this->pdf->linha_relativa(780,$inicio_escrita_y ,0,23);

								$inicio_escrita_y += 20;

							/**
							 *
							 */

							}
						}
					}

					/**
					 *  TOTAL
					 */


					$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 782, $altura_linha);

					//linha alfabetizados
					$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 695 + $expande, $altura_linha);

					//linha numero de alunos
					$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 90 + $expande , $altura_linha);

				    //linha numero de turmas
					$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 60 + $expande , $altura_linha);
					//$this->pdf->quadrado_relativo( 30, 125, 170 + $expande + 30, $altura);

					//linha serie
					$this->pdf->quadrado_relativo( 30, $inicio_escrita_y, 55, $altura_linha);



					//posicao serie
					$serie_x = 35;
					if($ct_analfabeto <= 1)
						$texto = $ct_analfabeto == 0 ? "Total\nAlfabetizado" : "Total\nNão-Alfabetizado";
					else
						$texto = "Total Geral";
					$this->pdf->escreve_relativo( $texto, $serie_x,$inicio_escrita_y + 5, 50, 50, $fonte, 6, $corTexto, 'left' );
					//posicao numero turmas
					$turma_x = 25 + $expande;

					$this->pdf->escreve_relativo( $total_geral_turmas, $turma_x ,$inicio_escrita_y + 5, 100, 50, $fonte, 10, $corTexto, 'center' );
					//posicao numero alunos
					$alunos_x = 40 + $expande;
					$total_geral_alunos = $ct_analfabeto <= 1 ? $total_geral_alunos : $total_geral_alunos_ambos;
					$this->pdf->escreve_relativo( $total_geral_alunos, $alunos_x + 15,$inicio_escrita_y + 5, 100, 40, $fonte, 10, $corTexto, 'center' );


					$campo_total = $ct_analfabeto <= 1 ? "total_geral" : "total_geral_ambos";
					$total_defasados = 0;
					//echo '<pre>';print_r($this->array_ano_idade);
					//if($ct_analfabeto <= 1)
					{
						foreach ($this->array_ano_idade as $key =>$value)
						{

							$total_defasados += $this->array_ano_idade [$key]["$campo_total"] ;
						}
					}
					if($total_geral_alunos > 0)
						$media = (($total_defasados / $total_geral_alunos) * 100);
					else
						$media = 0;

					//$total_defasados = $ct_analfabeto <= 1 ? $total_defasados : 	$total_defasado_serie_ambos;
					$this->pdf->escreve_relativo( $total_defasados, 750 ,$inicio_escrita_y + 5, 30, $altura, $fonte, 10, $corTexto, 'center' );
					$this->pdf->escreve_relativo(  $media . "%", 780 ,$inicio_escrita_y + 5, 30, $altura, $fonte, 10, $corTexto, 'center' );

					$total_defasado_serie_ambos += $total_defasados;
					$total_porcen_defasado_serie_ambos += $media;

					//$this->qt_anos = 1;
					$largura_anos = 615;
					//$this->idade_inicial = 6;
					$array_ano_idade = array();
					//$incremental = (int)($largura_anos/ $this->qt_anos);
					if(sizeof($this->array_ano_idade) > 0)
					$incremental = (int)($largura_anos/ sizeof($this->array_ano_idade));
					else
					$incremental = 0;
					$reta_ano_x = $alunos_x + 80;
					$largura_quadrado = $incremental-5;


					//$incremental = (int)($largura_anos/ $this->qt_anos);
					$reta_ano_x = $alunos_x + 85;
					$ajuste = 0;
					//for($ct = $this->ano - $this->idade_inicial ;$ct > $this->ano - $this->idade_inicial - $this->qt_anos;$ct--)
					$campo_total = $ct_analfabeto <= 1 ? "total_geral" : "total_geral_ambos";
					foreach ($this->array_ano_idade as $key => $ano)
					{

						//ajuste2


						$this->pdf->escreve_relativo($ano["$campo_total"], $reta_ano_x -5 ,$inicio_escrita_y + 5, $incremental, $altura_linha, $fonte, 10, $corTexto, 'center' );
						//$this->pdf->quadrado_relativo( $reta_ano_x - $ajuste,$inicio_escrita_y, $largura_quadrado + $ajuste + ($key == sizeof($array_ano_idade)-1? 5 : 0), $altura_linha,0.5,"#A1B3BD","");

						$ajuste = 5;

						//$anos_x += $incremental;
						$reta_ano_x += $incremental ;
						//reta
						if($key < sizeof($this->array_ano_idade) - 1){
							$this->pdf->linha_relativa($reta_ano_x -5,$inicio_escrita_y,0,$altura_linha);

						}
						else $largura_quadrado+=4.5;


					}

					$this->pdf->linha_relativa(780,$inicio_escrita_y ,0,23);

					$inicio_escrita_y += 20;

					/**
					 *
					 */
					if($ct_analfabeto == 2)
						$this->rodape();

					$this->pdf->ClosePage();


				}


				/**
				 *
				 */
			}
		}



		$this->get_link = $this->pdf->GetLink();
		//header( "location: " . $this->pdf->GetLink() );
		$this->pdf->CloseFile();

		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";

	}

  public function addCabecalho()
  {
    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Variável que controla a altura atual das caixas
    $altura   = 30;
    $fonte    = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $altura, 782, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Paginador
    $this->pdf->escreve_relativo(date("d/m/Y"), 25, 30, 782, 80, $fonte, 10,
      $corTexto, 'right');

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80, $fonte, 18,
      $corTexto, 'center' );

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:$this->nm_instituicao", 120, 58,
      300, 80, $fonte, 10, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola:{$this->nm_escola}",136, 70, 300, 80,
      $fonte, 10, $corTexto, 'left');

    // Título
    $this->pdf->escreve_relativo("Demonstrativo de Alunos Defasados Idade/Série - {$this->ano}",
      30, 85, 782, 80, $fonte, 12, $corTexto, 'center' );

    // Data
    $this->pdf->escreve_relativo("{$this->meses_do_ano[$this->mes]}/{$this->ano}",
      45, 100, 535, 80, $fonte, 10, $corTexto, 'left');
  }


	function novaPagina($analfabeto = 1)
	{
		$altura2 = 300;
		$altura = 50;

		$expande = 24;
	   // $this->pdf->quadrado_relativo( 30, 125, 782, $altura2);


		$fonte = 'arial';
		$corTexto = '#000000';

		//linha turno
		$this->pdf->quadrado_relativo( 30, 125, 782, $altura);

		//linha alfabetizados
		$this->pdf->quadrado_relativo( 30, 125, 695 + $expande, $altura);

		//linha numero de alunos
		$this->pdf->quadrado_relativo( 30, 125, 90 + $expande , $altura);

	    //linha numero de turmas
		$this->pdf->quadrado_relativo( 30, 125, 60 + $expande , $altura);
		//$this->pdf->quadrado_relativo( 30, 125, 170 + $expande + 30, $altura);

		//linha serie
		$this->pdf->quadrado_relativo( 30, 125, 55, $altura);


		$centralizado = abs(($altura - 10) / 2) + 125;

		//posicao serie
		$serie_x = 45;
		$this->pdf->escreve_relativo( "Série", $serie_x,$centralizado, 50, 50, $fonte, 7, $corTexto, 'left' );
		//posicao numero turmas
		$turma_x = 30 + $expande;
		$this->pdf->escreve_relativo( "Nº\n\nde\n\nTurmas", $turma_x -5,$centralizado - 15, 100, 50, $fonte, 7, $corTexto, 'center' );
		//posicao numero alunos
		$alunos_x = 40 + $expande;
		$this->pdf->escreve_relativo( "Nº\n\nde\n\nAlunos", $alunos_x + 15,$centralizado - 15, 100, 40, $fonte, 7, $corTexto, 'center' );

		$necessidade_x = 170 + $expande;
		if($analfabeto <= 1)
			$analfabeto = $analfabeto == 0 ? "ALFABETIZADOS " : "NÃO ALFABETIZADOS";
		else
			$analfabeto = "ALFABETIZADOS E NÃO ALFABETIZADOS";
		$this->pdf->escreve_relativo($analfabeto , $alunos_x +80, 127, 620, $altura, $fonte, 12, $corTexto, 'center' );

		//posicao serie
		$defasagem_x = 720  + $expande;
		//$this->pdf->escreve_relativo( "Série", $serie_x,$centralizado, 92, 40, $fonte, 7, $corTexto, 'center' );
		$this->pdf->escreve_relativo( "Total de\n\ndefasagem\n\n(2 anos)", $defasagem_x ,$centralizado - 19, 70, 80, $fonte, 6, $corTexto, 'center' );

		$inicio_escrita_y = 144;

		$this->pdf->escreve_relativo( "Nº", 750 ,$inicio_escrita_y + 16, 30, $altura, $fonte, 10, $corTexto, 'center' );
		$this->pdf->escreve_relativo( "%", 780 ,$inicio_escrita_y + 16, 30, $altura, $fonte, 10, $corTexto, 'center' );
		$this->pdf->linha_relativa(780,159,0,16);

		$this->pdf->linha_relativa(144,159,668,0);
		$this->pdf->linha_relativa(144,145,605,0);
		$anos_x = $alunos_x + 77;


		//$this->qt_anos = 1;
		$largura_anos = 615;
		//$this->idade_inicial = 6;
		$array_ano_idade = array();
		//$incremental = (int)($largura_anos/ $this->qt_anos);
		if(sizeof($this->array_ano_idade))
			$incremental = (int)($largura_anos/ sizeof($this->array_ano_idade));
		else
			$incremental = 0;
		$reta_ano_x = $alunos_x + 80;


		foreach ($this->array_ano_idade as $key => $faixa_etaria)
		{
			$this->pdf->escreve_relativo(/*$ct*/$faixa_etaria['ano'], $anos_x + 1 ,$inicio_escrita_y + 2, $incremental, $altura, $fonte, 10, $corTexto, 'center' );
			$this->pdf->escreve_relativo( ((sizeof($this->array_ano_idade) -1 == $key && (sizeof($this->array_ano_idade) > 1)) ? ">" : "" ) . $faixa_etaria['idade']/*$this->ano - $ct == $this->idade_inicial +  $this->qt_anos - 1? ">".($this->ano - $ct) : $this->ano - $ct*/, $anos_x ,$inicio_escrita_y + 17, $incremental, $altura, $fonte, 10, $corTexto, 'center' );

			$anos_x += $incremental;
			$reta_ano_x += $incremental;

			if($key+1 < sizeof($this->array_ano_idade))
				$this->pdf->linha_relativa($reta_ano_x,145,0,30);

		}



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

	function corta_string( $string, $tamanho = 300, $add_fim = "" )
	{
		if( strlen($string) > $tamanho )
		{
			$string = substr( $string, 0, $tamanho ) . $add_fim;
		}
		return $string;
	}

	function Editar()
	{
		return false;
	}

	function Excluir()
	{
		return false;
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
