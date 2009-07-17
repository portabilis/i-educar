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
 * Boletim de aluno.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Aluno
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Boletim" );
		$this->processoAp = "664";
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
	var $ref_cod_serie;
	var $ref_cod_turma;

	var $ano;
	var $mes;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $sequencial;
	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;
	var $nm_professor;
	var $nm_turma;
	var $nm_serie;
	var $nm_disciplina;
	var $curso_com_exame = 0;
	var $ref_cod_matricula;

	var $page_y = 135;

//	var $ref_cod_matricula;
	var $nm_aluno;
	var $array_modulos = array();
	var $nm_curso;
	var $get_link = false;
	//var $cursos = array();

	var $total;

	//var $array_disciplinas = array();

	var $ref_cod_modulo;
	var $inicio_y;

	var $numero_registros;
	var $em_branco;

	var $meses_do_ano = array(
							 "1" => "JANEIRO"
							,"2" => "FEVEREIRO"
							,"3" => "MAR&Ccedil;O"
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

	/****************COLOCADO********************************/
	var $segue_padrao_escolar = true;
	var $mostra_cabecalho_modulo = array();
	/****************COLOCADO********************************/


	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}


		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';

		if(empty($this->ref_cod_turma) && !$this->em_branco)
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}


		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

		}

		$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
		$det_instituicao = $obj_instituicao->detalhe();
		$this->nm_instituicao = $det_instituicao['nm_instituicao'];

	     $obj_calendario = new clsPmieducarEscolaAnoLetivo();
	     $lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

	     $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
	     $det_turma = $obj_turma->detalhe();
	     $this->nm_turma = $det_turma['nm_turma'];


	     $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
	     $det_serie = $obj_serie->detalhe();
	     $this->nm_serie = $det_serie['nm_serie'];

		 $obj_pessoa = new clsPessoa_($det_turma["ref_cod_regente"]);
		 $det = $obj_pessoa->detalhe();
		 $this->nm_professor = $det["nome"];

	     if(!$lista_calendario)
	     {
	     	echo '<script>
	     			alert("Escola não possui calendário definido para este ano");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
	     }

		$prox_mes = $this->mes + 1;



		$obj = new clsPmieducarSerie();
		$obj->setOrderby('cod_serie,etapa_curso');
		$lista_serie_curso = $obj->lista(null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();
		$this->nm_curso = $det_curso['nm_curso_upper'];



		if(!$this->em_branco)
		{
			if($det_curso['padrao_ano_escolar'])
			{
				$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
				$obj_ano_letivo_modulo->setOrderby("data_inicio asc");
				$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista($this->ano,$this->ref_cod_escola,null,null);

				if($lst_ano_letivo_modulo)
				{
					foreach ($lst_ano_letivo_modulo as $modulo) {

						$obj_modulo = new clsPmieducarModulo($modulo['ref_cod_modulo']);
						$det_modulo = $obj_modulo->detalhe();
						$this->array_modulos[] = $det_modulo;

					}
				}

				if(!$this->em_branco)
				{
					$obj_disc_serie = new clsPmieducarEscolaSerieDisciplina();
					$lst_disc_serie = $obj_disc_serie->lista($this->ref_cod_serie, $this->ref_cod_escola, null, 1);

					if(is_array($lst_disc_serie))
					{
						foreach ($lst_disc_serie as $key => $disciplina)
						{
							$obj_disc = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
				    		$det_disc = $obj_disc->detalhe();
				    		$lst_disc_serie[$key]['nm_disciplina'] = $det_disc['nm_disciplina'];
				    		$array_disc[$key] = $det_disc['nm_disciplina'];

						}
						array_multisort($array_disc,SORT_ASC,$lst_disc_serie);
						//ksort($lst_disc_serie);
						//echo '<pre>';print_r($lst_disc_serie);die;
					}
				}

			}
			else
			{
				$obj_turma_modulo = new clsPmieducarTurmaModulo();
				$lst_turma_modulo = $obj_turma_modulo->lista($this->ref_cod_turma,null,null,null,null,null,null);

				if($lst_turma_modulo)
				{


					/****************COLOCADO********************************/
					$this->segue_padrao_escolar = false;
					/****************COLOCADO********************************/

					foreach ($lst_turma_modulo as $modulo) {

						$obj_modulo = new clsPmieducarModulo($modulo['ref_cod_modulo']);
						$det_modulo = $obj_modulo->detalhe();
						$this->array_modulos[] = $det_modulo;

						/****************COLOCADO********************************/
						$nm_modulo = substr(strtoupper($det_modulo["nm_tipo"]), 0, 1);
						/****************COLOCADO********************************/
					}

					/****************COLOCADO********************************/
					for ($i = 0; $i < count($this->array_modulos); $i++) {
						$this->mostra_cabecalho_modulo[$i] = ($i+1)."º".$nm_modulo;
					}
					/****************COLOCADO********************************/

				}

				//$obj_disc_serie = new clsPmieducarDisciplinaSerie();
				//$lst_disc_serie = $obj_disc_serie->lista(null, $this->ref_cod_serie, 1);
				if(!$this->em_branco)
				{
					$obj_disc_serie = new clsPmieducarEscolaSerieDisciplina();
//					$lst_disc_serie = $obj_disciplinas->lista($this->ref_cod_serie,$this->ref_cod_escola,null,1);
					$lst_disc_serie = $obj_disc_serie->lista($this->ref_cod_serie,$this->ref_cod_escola,null,1);

					if(is_array($lst_disc_serie))
					{
						foreach ($lst_disc_serie as $key => $disciplina)
						{
							$obj_disc = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
				    		$det_disc = $obj_disc->detalhe();
				    		$lst_disc_serie[$key]['nm_disciplina'] = $det_disc['nm_disciplina'];

						}

					}
				}

			}
		}
		else
		{
			$this->array_modulos = array(array('nm_tipo' => 'Bimestre'),array('nm_tipo' => 'Bimestre'),array('nm_tipo' => 'Bimestre'),array('nm_tipo' => 'Bimestre'));
		}

		if($det_curso['media_exame'])
			$this->curso_com_exame = 1;

			$media_curso = $det_curso['media'];

		if(!$this->em_branco)
		{
		    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
		    $obj_matricula_turma->setOrderby("nome_aluno");
			$lista_matricula = $obj_matricula_turma->lista($this->ref_cod_matricula,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,null,null,null,$this->ano,null,true);
		}
		$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao($det_curso['ref_cod_tipo_avaliacao']);
		$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
		$curso_conceitual = $det_tipo_avaliacao['conceitual'];

		if($lista_matricula || $this->em_branco)
		{
			if($this->em_branco)
			{
				$lista_matricula = array();
				$lista_matricula[] = '';
				$this->numero_registros = $this->numero_registros? $this->numero_registros : 20;
				for ($i = 0 ; $i < $this->numero_registros; $i++)
				{
					$lst_disc_serie[] = '';
				}
			}


			if(!$curso_conceitual)
			{
				$this->pdf = new clsPDF("Boletim", "Boletim {$this->ano}", "A4", "", false, false);

				$this->pdf->largura  = 842.0;
		  		$this->pdf->altura = 595.0;

		  		$this->pdf->topmargin     = 5;
		  		$this->pdf->bottommargirn = 5;



				$altura_linha = 13;
				//$inicio_escrita_y = 50;

				$this->pdf->OpenPage();
				//$this->page_y = 95;
				$this->page_y = 10;

				$flag_tamanho = false;
				$tamanho = $this->page_y;
				foreach ($lista_matricula as $matricula)
			    {
					$reprovou = false;
			    	if(!$this->em_branco)
			    	{
				    	$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
				    	$det_matricula = $obj_matricula->detalhe();
				    	$this->ref_cod_matricula = $matricula['ref_cod_matricula'];
						$this->nm_aluno = $matricula['nome_aluno'];
			    	}

					if($this->page_y + $tamanho > 540)
					{
						//$this->desenhaLinhasVertical();
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->page_y = 10;
						//$this->addCabecalho();

					}

					$this->addCabecalho();
					$this->inicio_y = $this->page_y - 25;


			    	if(!$this->em_branco)
			    	{
				    	$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
				    	$det_matricula = $obj_matricula->detalhe();

				    	$obj_aluno = new clsPmieducarAluno();
				    	$det_aluno = array_shift($obj_aluno->lista($det_matricula['ref_cod_aluno']));
			    	}

			    	foreach ($lst_disc_serie as $key => $disciplina)
			    	{
			    		$this->pdf->quadrado_relativo( 30, $this->page_y , 782, $altura_linha,0.5);
			    		$inc =  (strlen($det_disc['nm_disciplina']) > 40 )? -2 : +2;
			    		$fonte_dis =  7;//(strlen($det_disc['nm_disciplina']) > 40 )? 7 : 9;
			    		$this->pdf->escreve_relativo($disciplina['nm_disciplina'] , 33 ,$this->page_y +$inc,170, 15, $fonte, $fonte_dis, $corTexto, 'left' );

				    	/**
				    	 * notas
				    	 */
						$largura_anos = 620;
						$altura = 30;

						if(sizeof($this->array_modulos)  + $this->curso_com_exame + 2 >= 1)
						{

							$incremental = (int)ceil($largura_anos/ (sizeof($this->array_modulos) + $this->curso_com_exame + 2));

						}else {

							$incremental = 1;
						}

						$reta_ano_x = 209 ;
						$anos_x = 209;


				    	if(!$this->em_branco)
				    	{
							$obj_nota = new clsPmieducarNotaAluno();
							$obj_nota->setOrderby("cod_nota_aluno asc");

							if($det_curso['padrao_ano_escolar'] == 1)
								$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'],$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null);
							else
								$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,null,$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null,$disciplina['ref_cod_disciplina']);

								if (is_array($det_nota))
								usort($det_nota, "cmp");


							$obj_dispensa = new clsPmieducarDispensaDisciplina();
							$matricula_dispensa_disciplina = $obj_dispensa->lista($matricula['ref_cod_matricula'],$this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'],null,null,null,null,null,null,null,1);
				    	}
						$dispensas = array();
						$completo = true;
						if(count($det_nota) < count($this->array_modulos) || !$det_nota)
						{
							if(!$det_nota)
								$det_nota = array();

							for ($ct = count($det_nota);$ct <= count($this->array_modulos);$ct++)
							{
								if ($matricula_dispensa_disciplina)
								{
									$det_nota[$ct] = array('D');
									$dispensas[$ct] = 1;
								}
								else
								{
									$det_nota[$ct] = array('');
								}

								$completo = false;
							}
						}

				    	if(!$this->em_branco)
				    	{
							if($det_curso['falta_ch_globalizada'])
							{
								$obj_falta = new clsPmieducarFaltas();
								$obj_falta->setOrderby("sequencial asc");
								$det_falta = $obj_falta->lista($matricula['ref_cod_matricula'],null,null,null,null,null);
								if(is_array($det_falta))
								{
									$total_faltas = 0;
									foreach ($det_falta as $key => $value)
									{
										$total_faltas += $det_falta[$key]['faltas'] = $value['falta'];
									}

									$det_falta['total'] = $total_faltas;

								}
								if(count($det_nota) < count($this->array_modulos) || !$det_nota)
								{
									if(!$det_falta)
										$det_falta = array();
									for ($ct = count($det_nota);$ct <= count($this->array_modulos);$ct++)
									{
										$det_falta[$ct] = array('');
										$det_falta[$ct]['faltas'] = '0';
									}
								}

							}
							else
							{
								$obj_falta = new clsPmieducarFaltaAluno();
								$obj_falta->setOrderby("cod_falta_aluno asc");
								if($det_curso['padrao_ano_escolar'] == 1)
									$det_falta = $obj_falta->lista(null,null,null,$this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'],$matricula['ref_cod_matricula'],null,null,null,null,null,1);
								else
									$det_falta = $obj_falta->lista(null,null,null,$this->ref_cod_serie,$this->ref_cod_escola,null,$matricula['ref_cod_matricula'],null,null,null,null,null,1,null,$disciplina['ref_cod_disciplina']);

								$total_faltas = 0;
								if(is_array($det_falta))

									foreach ($det_falta as $key => $value)
									{
										$total_faltas += $det_falta[$key]['faltas'];
									}

								$det_falta['total'] = $total_faltas;
							}
				    	}


						if($det_nota){
							$soma_notas = 0;

							/*********************COLOCADO*****************/
							$notas_primeiro_regular = array();
							/*********************COLOCADO*****************/

							foreach ($det_nota as $key => $nota) {

								$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota['ref_ref_cod_tipo_avaliacao'],$nota['ref_sequencial'],null,null,null,null);
								$det_tipo_av_val = $obj_tipo_av_val->detalhe();

								if (dbBool($det_serie["ultima_nota_define"]))
								{
									$soma_notas = $det_tipo_av_val["valor"];
								}
								else
								{
									$soma_notas	+= $det_tipo_av_val['valor'];
								}
								if ( count($this->array_modulos) == count($det_nota) )
								{
									$frequencia_minima = $det_curso["frequencia_minima"];
									$hora_falta = $det_curso["hora_falta"];
									$carga_horaria_curso = $det_curso["carga_horaria"];


								}

										if ($det_tipo_av_val['conceitual'] )
											$aprovado = $this->aprovado; // situacao definida pelo professor
										else if( (count($this->array_modulos) <= $nota['modulo']) && ($aprovado == 3) && !$det_tipo_av_val['conceitual'] )
											$aprovado = 1; // aluno aprovado

								/**
								 *
								 */
								if($key < (count($this->array_modulos))  )
								{
									/**
									 * variavel de controle para verificacao de media
									 */
									$media_sem_exame = true;

									//$det_tipo_av_val['valor'] = $det_tipo_av_val['valor'] ? $det_tipo_av_val['valor'] : '0,0';
									if($det_tipo_av_val['valor'])
										$nota = sprintf("%01.1f",$det_tipo_av_val['valor']);
									else
										$nota = "-";

									if($this->em_branco)
										$nota = "";

									$nota = $dispensas[$key] ? "D" : str_replace(".",",",$nota);

									$this->pdf->escreve_relativo( $nota, $anos_x + 10,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );
									if($det_curso['falta_ch_globalizada'])
									{//

										$falta = " - ";
									}
									else
									{
										if($det_falta[$key]['faltas'])
											$falta = $det_falta[$key]['faltas'];
										else
											$falta = "0";
									}

									if($this->em_branco)
										$falta = "";
									$falta = $dispensas[$key] ? "D" : $falta;
									$this->pdf->escreve_relativo( $falta, $anos_x +($incremental/2)+ 10,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );
									if(count($this->array_modulos) == count($det_nota) && $key == count($det_nota) - 1)
									{

										$this->pdf->escreve_relativo( $this->em_branco?"":"-", $anos_x +$incremental/3+$incremental,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );
									}

								}
								else
								{
									/**
									 * variavel de controle para verificacao de media
									 */
									$media_sem_exame = false;
									if($this->curso_com_exame && ($det_matricula['aprovado'] == 1  || $det_matricula['aprovado'] == 2) )
									{
										$nota = sprintf("%01.2f",$det_nota[$key]['nota']);
										$nota = str_replace(".",",",$nota);
										$nota = $dispensas[$key] ? "D" : $nota;
										$this->pdf->escreve_relativo($nota/*$det_tipo_av_val['nome']*/, $anos_x +$incremental/3,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );

										/****************COLOCADO*********************/
										$nota_exame = true;
										$exame_nota = $det_nota[$key]["nota"];
										/****************COLOCADO*********************/

									}
								}


							 	 $anos_x += $incremental;

								$reta_ano_x += $incremental;
							}


							/****************COLOCADO*********************/
							if (!dbBool($det_serie["ultima_nota_define"]))
							{
								if (!$nota_exame)
								{
									$media = $soma_notas / count($det_nota); //soh esta parte eh do codigo original
									$media_ = $media;
								}
								else
								{
									$media = ($soma_notas + $exame_nota * 2) / (count($det_nota)+1);
								}
							}
							else
							{
								$media = $soma_notas;
							}
//							$nota_exame = false;
							/****************COLOCADO*********************/

							/****************COLOCADO*********************/
							$det_media = array();
							if ($media >= $det_curso['media'] || $nota_exame) {
								$obj_media = new clsPmieducarTipoAvaliacaoValores();
								$det_media = $obj_media->lista($det_curso['ref_cod_tipo_avaliacao'],$det_curso['ref_sequencial'],null,null,$media,$media);
							}
							$nota_exame =false;
							if($det_media && is_array($det_media))
							{
								$det_media = array_shift($det_media);
								$media = $det_media['valor'];
								$media = sprintf("%01.1f",$media);
								$media = str_replace(".",",",$media);
							}
							elseif (dbBool($det_serie["ultima_nota_define"]))
							{
								$media = sprintf("%01.1f",$media);
								$media = str_replace(".",",",$media);
							}
							if(count($det_nota) <= count($this->array_modulos) && $this->curso_com_exame)
							{
								$anos_x += $incremental;

								$reta_ano_x += $incremental;
							}

							if( count($det_nota) == 4 && ($det_matricula['aprovado'] == 1 || $det_matricula['aprovado'] == 2 || !(($det_matricula['aprovado'] == 7 || (str_replace(",",".",$media) < $media_curso_ && !$nota_exame)))))
							{
								if ($dispensas[$key])
									$this->pdf->escreve_relativo( "D", $anos_x +5,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );
								else
									$this->pdf->escreve_relativo( $media, $anos_x +5,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );
							}
							else
							{
								$this->pdf->escreve_relativo( "-", $anos_x +5,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );
							}
							if($det_curso['falta_ch_globalizada'])
							{

								$falta = " - ";
							}
							else
							{
								if($det_falta['total'])
									$falta = $det_falta['total'];
								else
									$falta = "0";
							}
							if($this->em_branco)
								$falta = "";
							$falta = $dispensas[$key] ? "D" : $falta;
							$this->pdf->escreve_relativo(  $falta, $anos_x +($incremental/2)+2,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );


						}
							$anos_x += $incremental;

							$reta_ano_x += $incremental;
						$sit = "";

					if($completo){

						if($media_sem_exame)
							$media_curso_ = $det_curso['media'];
						else
							$media_curso_ = $det_curso['media_exame'];


//						$sit = (str_replace(",",".",$media_) >= $media_curso_)? "APROVADO" : "REPROVADO";

						$sit = (str_replace(",",".",$media) >= $media_curso_)? "APROVADO" : "REPROVADO";
						$sit = ($det_matricula['aprovado'] == 3 && str_replace(",",".",$media) >= $media_curso_)? "APROVADO" : $sit;

//						$sit = ($det_matricula['aprovado'] == 3)? "EM ANDAMENTO" : $sit; //original
						/********COLOCADO****************/
						if ($det_matricula["aprovado"] != 1 && $det_matricula["aprovado"] != 2)
							$sit = ($det_matricula['aprovado'] == 7 || (str_replace(",",".",$media) < $media_curso_ && !$nota_exame))? "EM EXAME" : $sit;
						/********COLOCADO****************/
//						$sit = ($det_matricula['aprovado'] == 7)? "EM EXAME" : $sit;//original

						if($this->em_branco)
							$sit = "";
						$sit = $dispensas[$key] ? "D" : $sit;
						$this->pdf->escreve_relativo(/*($det_media['nota'] >= $media_curso)*//*$det_media*/$sit, $anos_x  -12,$this->page_y + 2, $incremental, $altura, $fonte, 8, $corTexto, 'center' );

					}

					$this->page_y +=$altura_linha;

					/**
					 *
					 */
			    	}
			    	if($det_curso['falta_ch_globalizada'])
			    	{


						$this->pdf->quadrado_relativo( 30, $this->page_y , 782, $altura_linha,0.5);
			    		$fonte_dis =  7;//(strlen($det_disc['nm_disciplina']) > 40 )? 7 : 9;
			    		$this->pdf->escreve_relativo("TOTAL FALTAS" , 80 ,$this->page_y +2,170, 15, $fonte, 8, $corTexto, 'left' );

			    		$reta_ano_x = 209 ;
						$anos_x = 209;

						if(is_array($det_falta))
							$total_faltas = array_pop($det_falta);

						if($det_falta)
							foreach ($det_falta as $key => $value)
							{
								$incr = $anos_x +($incremental/2)+ 10;
								if(count($det_nota) <= count($this->array_modulos) && $this->curso_com_exame && (count($det_falta)  == $key))
								{
									$incr = $anos_x +($incremental/3);
								}

								$this->pdf->escreve_relativo($det_falta[$key]['faltas']?  $det_falta[$key]['faltas'] : "0" , $incr,$this->page_y + 2, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );

								$anos_x += $incremental;

								$reta_ano_x += $incremental;

							}
						$reta_ano_x += $incremental;
						if(count($det_nota) >= count($this->array_modulos) && $this->curso_com_exame)
						{
							$anos_x += $incremental;

							$reta_ano_x += $incremental;
						}



						//$this->pdf->escreve_relativo($total_faltas?  $total_faltas : "-" , $anos_x +($incremental/2)+5,$this->page_y + 5, ($incremental/3), $altura, $fonte, 9, $corTexto, 'center' );

						$this->page_y +=$altura_linha;
			    	}

			    	$this->desenhaLinhasVertical();
			    	$sit = "";

			    	if($completo)
			    	{
			    		if($det_matricula['aprovado'] == 1 )
			    			$sit = "APROVADO";
			    		elseif ($det_matricula['aprovado'] == 2)
			    			$sit = "REPROVADO";
			    		elseif ($det_matricula['aprovado'] == 7)
			    			$sit = "EM EXAME";
			    	}
			    	if($sit)
			    		$this->pdf->escreve_relativo( "ALUNO ".$sit , 150,$this->page_y + 30, 530, $altura, $fonte, 9, $corTexto, 'center' );
					$this->rodape();

					$this->page_y += 50;

					$this->pdf->linha_relativa(30,$this->page_y - 15,785,0,0.1);

					if(!$flag_tamanho)
						$tamanho = $this->page_y - $tamanho;
			    }

				//$this->rodape();


			}
			/**
			 * conceitual
			 */
			else
			{
				$this->pdf = new clsPDF("Alunos Matriculados - {$this->ano}", "Alunos Matriculados - Sintético", "A4", "", false, false);
				foreach ($lista_matricula as $matricula)
			    {

			    	if(!$this->em_branco)
			    	{
				    	$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
				    	$det_matricula = $obj_matricula->detalhe();
				    	$this->ref_cod_matricula = $matricula['ref_cod_matricula'];
						$this->nm_aluno = $matricula['nome_aluno'];
			    	}

					$page_open = false;
					if($lst_disc_serie)
					{
						foreach ($lst_disc_serie as $disciplina)
						{
							if(!$page_open)
							{
								$x_quadrado = 30;
								$this->page_y = 95;
								$altura_caixa = 85;
								$this->pdf->OpenPage();
								$this->addCabecalhoc();
								$this->addCabecalhoc2();

								$page_open = true;
							}
							$altura_caixa = 15 + (int)((strlen($disciplina['nm_disciplina']) / 60 ) * 7) ;
							$this->pdf->quadrado_relativo( 30, $this->page_y, 535, $altura_caixa );
							$this->pdf->linha_relativa( 440, $this->page_y, 0, $altura_caixa, '0.1');
							$this->pdf->escreve_relativo($disciplina['nm_disciplina'],35,$this->page_y + 5,400,120,"arial","8","#000000","justify");


					    	if(!$this->em_branco)
					    	{
								$obj_nota = new clsPmieducarNotaAluno();
								$obj_nota->setOrderby("cod_nota_aluno asc");

								if($det_curso['padrao_ano_escolar'] == 1)
									$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'],$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null);
								else
									$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'],$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null,null);
//									$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,null,$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null,$disciplina['ref_cod_disciplina']);
					    	}

							$x_bim = 440 + 31;
							for ($i=1;$i <= 4;$i++)
							{
								if(is_array($det_nota))
								{
									$nota = array_shift($det_nota);
									$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota['ref_ref_cod_tipo_avaliacao'],$nota['ref_sequencial'],null,null,null,null);
									$det_tipo_av_val = $obj_tipo_av_val->detalhe();

								}
								else
									$det_tipo_av_val = null;
								if($i<=3)
									$this->pdf->linha_relativa( $x_bim, $this->page_y, 0, $altura_caixa, '0.1');
								$this->pdf->escreve_relativo("{$det_tipo_av_val['nome']}",$x_bim-31,$this->page_y + ($altura_caixa / 3),31,120,"arial","10","#000000","center");
								$x_bim += 31;
							}

							$this->page_y += $altura_caixa;
						}
			    	}
			    	else
			    	{
						$x_quadrado = 30;
						$this->page_y = 95;
						$altura_caixa = 85;
						$this->pdf->OpenPage();
						$this->addCabecalhoc();
						$this->addCabecalhoc2();

						$page_open = true;
			    	}

					////////////////////
					$this->pdf->quadrado_relativo( 30, $this->page_y, 535, 15 );
					$this->pdf->linha_relativa( 440, $this->page_y, 0, 15, '0.1');
					$this->pdf->escreve_relativo("TOTAL DE FALTAS",35,$this->page_y + 2,400,120,"arial","8","#000000","justify");
					if($det_curso['falta_ch_globalizada'])
					{

				    	if(!$this->em_branco)
				    	{
							$obj_falta = new clsPmieducarFaltas();
							$obj_falta->setOrderby("sequencial asc");
							$det_falta = $obj_falta->lista($matricula['ref_cod_matricula'],null,null,null,null,null);
					    }
						if(is_array($det_falta))
						{
							$total_faltas = 0;
							foreach ($det_falta as $key => $value)
							{
								$total_faltas += $det_falta[$key]['faltas'] = $value['falta'];
							}

							$det_falta['total'] = $total_faltas;

						}
					}

					$x_bim = 440 + 31;
					for ($i=1;$i <= 4;$i++)
					{
						if(is_array($det_falta))
						{
							$falta = array_shift($det_falta);
							$falta = $falta['faltas'];

						}
						else
							$falta = null;
						if($i<=3)
							$this->pdf->linha_relativa( $x_bim, $this->page_y, 0, 15, '0.1');
						$this->pdf->escreve_relativo("$falta",$x_bim-31,$this->page_y + 2,31,120,"arial","10","#000000","center");
						$x_bim += 31;
					}
					/********************COLOCADO*************************/
					$obj_matricula_situacao = new clsPmieducarMatricula($matricula["ref_cod_matricula"]);
					$obj_matricula_situacao->setCamposLista("aprovado");
					$det_matricula_situacao = $obj_matricula_situacao->detalhe();
					if ($det_matricula_situacao["aprovado"] == 1)
						$situacao = "APROVADO";
					elseif ($det_matricula_situacao["aprovado"] == 2)
						$situacao = "REPROVADO";
					else
						$situacao = "EM ANDAMENTO";
					$this->pdf->quadrado_relativo( 30, $this->page_y+15, 535, 15 );
					$this->pdf->linha_relativa( 440, $this->page_y+15, 0, 15, '0.1');
					$this->pdf->escreve_relativo("SITUAÇÃO",35,$this->page_y +15+ 2,400,120,"arial","8","#000000","justify");
					$this->pdf->escreve_relativo($situacao, 440, $this->page_y + 15 + 2, 120, 120, "arial", "10", "#000000", "center");
					/////////////////////
					$this->page_y += 35; //original é 25
					/********************COLOCADO*************************/
//					$this->page_y += 25; //original

					$this->pdf->escreve_relativo( "LEGENDA: \n
						D   = Desenvolvida
						PD = Parcialmente Desenvolvida
						ID   = Iniciando o Desenvolvimento
						ND = Não Desenvolvida
						CNA = Competência Não Avaliada", 36,$this->page_y, 200, 50, $fonte, 7, $corTexto, 'left' );

					$this->page_y += 75;
					$altura_obs = 60;

					$this->pdf->quadrado_relativo( 30, $this->page_y , 535, $altura_obs,0.1,"#000000","#FFFFFF" );
					$this->pdf->escreve_relativo( "OBS: ",33, $this->page_y + 3 , 545, 60, $fonte, 8, $corTexto, 'justify' );

					$this->pdf->ClosePage();

				}
			}


		}
		else
		{

		     	echo '<script>
		     			alert("Turma não possui matrículas no ano selecionado");
		     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
		     		</script>';

		     		return;

		}

		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();
		//header( "location: " . $this->pdf->GetLink() );

		//return true;
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
    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Variável que controla a altura atual das caixas
    $altura          = 10;
    $fonte           = 'arial';
    $corTexto        = '#000000';
    $espessura_linha = 0.5;

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $this->page_y, 782, 65, $espessura_linha);
    $this->pdf->insertImageScaled('gif', $logo, 50, $this->page_y + 52, 41);

    // Título principal
    $titulo = $config->get($config->titulo, "i-Educar");
    $this->pdf->escreve_relativo($titulo, 30,
      $this->page_y + 2, 782, 80, $fonte, 18, $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:  $this->nm_instituicao", 110,
      $this->page_y + 27, 400, 80, $fonte, 10, $corTexto, 'left');
    $this->nm_escola || $this->em_branco? $this->pdf->escreve_relativo( "Escola:  {$this->nm_escola}",127, $this->page_y + 43, 300, 80, $fonte, 10, $corTexto, 'left' ) : NULL;
    $dif = 0;

    if ($this->nm_professor) {
      $this->pdf->escreve_relativo("Prof.Regente:  {$this->nm_professor}",
        111, $this->page_y + 36, 300, 80, $fonte, 7, $corTexto, 'left');
    }
    else {
      $dif = 15;
    }

    $this->pdf->quadrado_relativo(30, $this->page_y + 68, 782, 12,$espessura_linha);
    $this->pdf->quadrado_relativo(30, $this->page_y + 83, 782, 12,$espessura_linha);
    $this->pdf->escreve_relativo("Aluno:  ".$this->nm_aluno,37, $this->page_y + 70,
      200, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Matricula:   ".str2upper($this->ref_cod_matricula),
      222, $this->page_y + 70, 300, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Turma:   ".str2upper($this->nm_turma),300,
      $this->page_y + 70, 300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo("Curso:  $this->nm_curso",37, $this->page_y + 85,
      300, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Ano/Série/Etapa:   " . ($this->nm_serie ? str2upper($this->nm_serie) : $this->ano),
      200, $this->page_y + 85, 300, 80, $fonte, 7, $corTexto, 'left');

    // Título
    $this->pdf->escreve_relativo("Boletim Escolar - $this->ano", 30,
      $this->page_y + 30, 782, 80, $fonte, 12, $corTexto, 'center');

    $obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
    $det_modulo = $obj_modulo->detalhe();

    // Data
    $this->pdf->escreve_relativo("Data de Emissão: " . date("d/m/Y"), 700,
      $this->page_y + 50, 535, 80, $fonte, 8, $corTexto, 'left');
    $this->page_y +=100;
    $this->novoCabecalho();
  }


	function desenhaLinhasVertical()
	{

		/**
		 *
		 */

		$espessura_linha = 0.5;
		$largura_anos = 620;

		if(count($this->array_modulos) + $this->curso_com_exame >= 1)
		{

			$incremental = floor($largura_anos/ (count($this->array_modulos)  + $this->curso_com_exame + 2/*situacao*/)) ;

		}else {

			$incremental = 1;
		}

		$reta_ano_x = 209 ;

		$resto = $largura_anos - ($incremental * (count($this->array_modulos) + $this->curso_com_exame + 1 /*situacao*/));

		for($linha = 0;$linha <count($this->array_modulos) + $this->curso_com_exame + 2 ;$linha++)
		{

				$this->pdf->linha_relativa($reta_ano_x,$this->inicio_y,0,$this->page_y - $this->inicio_y ,$espessura_linha);

			if(($this->curso_com_exame && $linha != count($this->array_modulos) && $linha != count($this->array_modulos)+2)  || (!$this->curso_com_exame && $linha != (count($this->array_modulos) + 1  ))  )
				$this->pdf->linha_relativa($reta_ano_x+($incremental/2),$this->inicio_y + 12 ,0,$this->page_y - $this->inicio_y - 12,$espessura_linha);

				$reta_ano_x += $incremental;



		}

		$this->pdf->linha_relativa(812,$this->inicio_y,0,$this->page_y - $this->inicio_y,$espessura_linha);


		/**
		 *
		 */
	}

	function rodape()
	{
		$corTexto = '#000000';
		$fonte = 'arial';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,$this->page_y + 2, 100, 50, $fonte, 7, $corTexto, 'left' );

		if(!$this->em_branco)
			$this->pdf->escreve_relativo( "Estou ciente do aproveitamento de ".str2upper($this->nm_aluno).", matrícula nº: $this->ref_cod_matricula.", 68,$this->page_y +12, 600, 50, $fonte, 9, $corTexto, 'left' );

			$this->pdf->escreve_relativo( "Assinatura do Responsável(a)", 677,$this->page_y +18, 200, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa(660,$this->page_y+18,130,0,0.4);
	}

	function Editar()
	{
		return false;
	}

	function Excluir()
	{
		return false;
	}

  public function novoCabecalho()
  {


    $altura2         = 300;
    $altura          = 50;
    $espessura_linha = 0.5;
    $expande         = 24;

    $fonte    = 'arial';
    $corTexto = '#000000';

    $inicio_escrita_y = $this->page_y;

    $this->pdf->linha_relativa(30,$this->page_y ,782,0,$espessura_linha);
    $this->pdf->escreve_relativo( 'Disciplina', 110,$this->page_y , 50, $altura, $fonte, 9, $corTexto, 'left' );

    $this->pdf->linha_relativa(30,$this->page_y,0,25,$espessura_linha);

    $largura_anos = 605;


    if (sizeof($this->array_modulos)  + $this->curso_com_exame + 2 >= 1) {
      $incremental = (int) ceil($largura_anos / (sizeof($this->array_modulos) + $this->curso_com_exame + 2));
    }
    else {
      $incremental = 1;
    }

    $reta_ano_x = 209 ;
    $anos_x = 209;

    $ct = 0;

    $num_modulo = 1;

    foreach ($this->array_modulos as $key => $modulo) {
      $this->pdf->escreve_relativo($num_modulo++."º ".$modulo['nm_tipo'], $anos_x ,$inicio_escrita_y + 1, $incremental, $altura, $fonte, 9, $corTexto, 'center' );

      // Médias
      $this->pdf->escreve_relativo('Nota', $anos_x + 8, $inicio_escrita_y + 12,
        ($incremental / 3), $altura, $fonte, 9, $corTexto, 'center');
      $this->pdf->escreve_relativo('Faltas', $anos_x +($incremental / 2) + 8,
        $inicio_escrita_y + 12, ($incremental / 3), $altura, $fonte, 9,
        $corTexto, 'center');

      $anos_x     += $incremental;
      $reta_ano_x += $incremental;

      $ct++;
    }

    if ($this->curso_com_exame) {
      $this->pdf->escreve_relativo('Exame Final', $anos_x + 5,
        $inicio_escrita_y + 4, $incremental, $altura, $fonte, 9, $corTexto, 'center');
      $anos_x     += $incremental;
      $reta_ano_x += $incremental;
    }

    $this->pdf->escreve_relativo('Resultado Final', $anos_x + 2,
      $inicio_escrita_y + 1, $incremental, $altura, $fonte, 9, $corTexto, 'center');

    // Médias
    $this->pdf->escreve_relativo('Nota', $anos_x + 15, $inicio_escrita_y + 12,
      ($incremental / 3), $altura, $fonte, 9, $corTexto, 'center');

    $this->pdf->escreve_relativo('Faltas', $anos_x + ($incremental / 2) + 15,
      $inicio_escrita_y + 12, ($incremental / 3), $altura, $fonte, 9,
      $corTexto, 'center');

    $this->pdf->escreve_relativo('Situação', $anos_x + $incremental + 5,
      $inicio_escrita_y + 4, $incremental, $altura, $fonte, 9, $corTexto, 'center');

    $this->page_y +=25;
  }


  public function addCabecalhoc()
  {
    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Variável que controla a altura atual das caixas
    $altura = 30;
    $fonte = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo( 30, $altura, 535, 85 );
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, "i-Educar");
    $this->pdf->escreve_relativo($titulo, 30, 30, 535, 80, $fonte, 18,
      $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:".str2upper($this->nm_instituicao),
      120, 50, 300, 80, $fonte, 10, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola:".str2upper($this->nm_escola),136, 62,
      380, 80, $fonte, 10, $corTexto, 'left');
    $this->pdf->escreve_relativo("Curso:".str2upper($this->nm_curso)."                     Turma:" . str2upper($this->nm_turma),
      136, 74, 500, 80, $fonte, 10, $corTexto, 'left');
    $this->pdf->escreve_relativo("Aluno:".str2upper($this->nm_aluno), 136, 86,
      300, 80, $fonte, 10, $corTexto, 'left');

    // Título
    $this->pdf->escreve_relativo("B O L E T I M  E S C O L A R", 30, 98, 535,
      80, $fonte, 14, $corTexto, 'center');

    // Data
    $mes = date('n');
  }


	function addCabecalhoc2()
	{
		$fonte = 'arial';
		$corTexto = '#000000';
		$x_quadrado = 30;
		$altura_caixa = 20;

		$this->page_y += $altura_caixa;

		$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y+10, 535, $altura_caixa );
		$this->pdf->escreve_relativo( "COMPETÊNCIAS", 30, $this->page_y + 15, 405, $altura_caixa, $fonte, 9, $corTexto, 'center' );

		$this->pdf->linha_relativa( 440, $this->page_y + 10, 0, $altura_caixa, '0.1');

		/***************************COLOCADO DENTRO DO IF ORIGINAL************/
		/***************************COLOCADO ELSE TAMBEM**********************/
		if ($this->segue_padrao_escolar)
		{
			$x_bim = 440 + 31;
			for ($i=1;$i <= 4;$i++)
			{
				if($i <= 3)
					$this->pdf->linha_relativa( $x_bim, $this->page_y + 10, 0, $altura_caixa, '0.1');
				$this->pdf->escreve_relativo("{$i}ºBIM",$x_bim-31,$this->page_y + 15,31,120,"arial","10","#000000","center");
				$x_bim += 31;
			}
			$this->page_y += $altura_caixa + 10;
		}
		else
		{
			$x_bim = 440 + 31;
			for ($i=1;$i <= 4;$i++)
			{
				if($i <= 3)
					$this->pdf->linha_relativa( $x_bim, $this->page_y + 10, 0, $altura_caixa, '0.1');
				$this->pdf->escreve_relativo($this->mostra_cabecalho_modulo[$i-1],$x_bim-31,$this->page_y + 15,31,120,"arial","10","#000000","center");
				$x_bim += 31;
			}
			$this->page_y += $altura_caixa + 10;
		}

		$this->page_y += $altura_caixa + 10;

	}
}

function cmp($a, $b)
{
	return $a["modulo"] > $b["modulo"];
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
<script>
