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
 * Diário de classe temporário.
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Diário de Classe" );
		$this->processoAp = "927";
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
	var $mes_inicial;
	var $mes_final;

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

	var $numero_registros;
	var $em_branco;

	var $page_y = 125;

	var $get_file;

	var $cursos = array();

	var $get_link;

	var $total;

	var $ref_cod_modulo;
	var $data_ini,$data_fim;

	var $z=0;

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

	var $indefinido;

	function renderHTML()
	{
		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}
		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';

		if(empty($this->ref_cod_turma))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}

		$modulo_sequencial = explode("-",$this->ref_cod_modulo);
		$this->ref_cod_modulo = $modulo_sequencial[0];
		$this->sequencial = $modulo_sequencial[1];

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

		$altura_linha = 23;
		$inicio_escrita_y = 175;


		$obj = new clsPmieducarSerie();
		$obj->setOrderby('cod_serie,etapa_curso');
		$lista_serie_curso = $obj->lista(null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();

		if($det_curso['falta_ch_globalizada'])
		{
			/**
			 * numero de semanas dos meses
			 */
			$db = new clsBanco();
			$consulta = "SELECT padrao_ano_escolar FROM pmieducar.curso WHERE cod_curso = {$this->ref_cod_curso}";
			$padrao_ano_escolar = $db->CampoUnico($consulta);
			$total_semanas = 0;
			if($padrao_ano_escolar)
			{
				$meses = $db->CampoUnico( "
				SELECT to_char(data_inicio,'dd/mm') || '-' || to_char(data_fim,'dd/mm')
				FROM
					pmieducar.ano_letivo_modulo
					,pmieducar.modulo
				WHERE modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
					AND modulo.ativo = 1
					AND ano_letivo_modulo.ref_cod_modulo = $this->ref_cod_modulo
					AND ano_letivo_modulo.sequencial = $this->sequencial
					AND ref_ano = $this->ano
					AND ref_ref_cod_escola = '{$this->ref_cod_escola}'
				ORDER BY
					data_inicio,data_fim ASC
				");
				$data = explode("-",$meses);

				if(!$this->data_ini)
					$this->data_ini = $data[0];

				if(!$this->data_fim)
					$this->data_fim = $data[1];

				$data_ini = explode("/",$data[0]);
				$data_fim = explode("/",$data[1]);

				$meses = array($data_ini[1],$data_fim[1]);
				$dias = array($data_ini[0],$data_fim[0]);
				$total_semanas = 0;

				for($mes = (int)$meses[0];$mes<=(int)$meses[1];$mes++)
				{
					$mes_final = false;
					if($mes == (int)$meses[0])
						$dia = $dias[0];
					elseif ($mes == (int)$meses[1])
					{
						$dia = $dias[1];
						$mes_final = true;
					}
					else
						$dia = 1;

				$total_semanas += $this->getNumeroDiasMes($dia,$mes,$this->ano,$mes_final);//,$lista_quadro_horarios[count($lista_quadro_horarios)-1]);

//				echo $total_semanas;
//				die("###");
				}
			    //$total_semanas += $this->getNumeroDiasMes($this->mes + 1,$this->ano);//,$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
			}
			else
			{
				$meses = $db->CampoUnico( "
				SELECT to_char(data_inicio,'dd/mm') || '-' || to_char(data_fim,'dd/mm')
					FROM
						pmieducar.turma_modulo
						,pmieducar.modulo
					WHERE modulo.cod_modulo = turma_modulo.ref_cod_modulo
						AND ref_cod_turma = '{$this->ref_cod_turma}'
					AND turma_modulo.ref_cod_modulo = $this->ref_cod_modulo
					AND turma_modulo.sequencial = $this->sequencial
					AND to_char(data_inicio,'yyyy') = $this->ano
					ORDER BY
						data_inicio,data_fim ASC

				");

				$total_semanas = 0;

				$data = explode("-",$meses);

				if(!$this->data_ini)
					$this->data_ini = $data[0];

				if(!$this->data_fim)
					$this->data_fim = $data[1];

				$data_ini = explode("/",$data[0]);
				$data_fim = explode("/",$data[1]);

				$meses = array($data_ini[1],$data_fim[1]);
				$dias = array($data_ini[0],$data_fim[0]);
				$total_semanas = 0;

				for($mes = $meses[0];$mes<=$meses[1];$mes++)
				{
					$mes_final = false;
					if($mes == $meses[0])
						$dia = $dias[0];
					elseif ($mes == $meses[1])
					{
						$dia = 1;
						$mes_final = true;
					}
					else
						$dia = 1;
					$total_semanas += $this->getNumeroDiasMes($dia,$mes,$this->ano,$mes_final);
					//**************************************************30032007/\
				}


			}

			$this->pdf = new clsPDF("Diário de Classe - {$this->ano}", "Diário de Classe - {$data[0]} até {$data[1]} de {$this->ano}", "A4", "", false, false);
			$this->mes_inicial = (int)$meses[0];
			$this->mes_final = (int)$meses[1];
			$this->pdf->largura  = 842.0;
	  		$this->pdf->altura = 595.0;

		    $this->total = $total_semanas;//$total_semanas * count($lista_quadro_horarios);


		    if(!$this->em_branco)
		    {
			    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
				$obj_matricula_turma->setOrderby("nome_ascii");
			    $lista_matricula = $obj_matricula_turma->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array(1,2,3),null,null,$this->ano,null,true,null,null,true);
		    }

			if($lista_matricula || $this->em_branco)
			{
				$this->pdf->OpenPage();
				$this->addCabecalho();

				if($this->em_branco)
				{
					$lista_matricula = array();
					$this->numero_registros = $this->numero_registros? $this->numero_registros : 20;
					for ($i = 0 ; $i < $this->numero_registros; $i++)
					{
						$lista_matricula[] = '';
					}
				}

				$num = 0;
			    foreach ($lista_matricula as $matricula)
			    {

			    	$num++;

					if($this->page_y > 500)
					{
						$this->desenhaLinhasVertical();
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->page_y = 125;
						$this->addCabecalho();


					}


			    	$this->pdf->quadrado_relativo( 30, $this->page_y , 782, 19);
			    	$this->pdf->escreve_relativo($matricula['nome_aluno'] , 33 ,$this->page_y + 4,160, 15, $fonte, 7, $corTexto, 'left' );
			    	$this->pdf->escreve_relativo( sprintf("%02d",$num),757, $this->page_y + 4, 30, 30, $fonte, 7, $corTexto, 'left' );

			    	$this->page_y +=19;



			    }

		    	$this->desenhaLinhasVertical();

				$this->rodape();
				$this->pdf->ClosePage();
			}
			else
			{

		     	echo '<script>
		     			alert("Turma não possui matriculas");
		     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
		     		</script>';

		     		return;
			}


			//header( "location: " . $this->pdf->GetLink() );
			$this->pdf->CloseFile();
			$this->get_link = $this->pdf->GetLink();
		}
		else
		{
			/**
			 * CARGA HORARIA NAO GLOBALIZADA
			 * GERAR UMA PAGINA PARA CADA DISICIPLINA
			 */
			//$obj_turma_disc = new clsPmieducarTurmaDisciplina();
			$obj_turma_disc = new clsPmieducarDisciplinaSerie();
			$obj_turma_disc->setCamposLista("ref_cod_disciplina");
			$lst_turma_disc = $obj_turma_disc->lista(null,$this->ref_cod_serie,1);
			if($lst_turma_disc)
			{
				$this->indefinido = false;
				$this->pdf = new clsPDF("Diário de Classe - {$this->ano}", "Diário de Classe - {$this->data_ini} até {$this->data_fim}  de {$this->ano}", "A4", "", false, false);
				foreach ($lst_turma_disc as $disciplina)
				{
					$obj_disc = new clsPmieducarDisciplina($disciplina);
					$det_disc = $obj_disc->detalhe();
					$this->nm_disciplina = $det_disc['nm_disciplina'];
					$this->page_y = 125;

					/**
					 * numero de semanas dos meses
					 */
//					$obj_quadro = new clsPmieducarQuadroHorario();
//					$obj_quadro->setCamposLista("cod_quadro_horario");
//					$quadro_horario = $obj_quadro->lista(null,null,null,$this->ref_cod_turma, null, null, null, null,1);
//					if(!$quadro_horario)
//					{
//						echo '<script>alert(\'Turma não possui quadro de horários\');
//						window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));</script>';
//						die();
//					}
//					$obj_quadro_horarios = new clsPmieducarQuadroHorarioHorarios();
//					$obj_quadro_horarios->setCamposLista("dia_semana");
//					$obj_quadro_horarios->setOrderby("1 asc");
//
//					$lista_quadro_horarios = $obj_quadro_horarios->lista($quadro_horario[0],$this->ref_cod_serie,$this->ref_cod_escola,$disciplina,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1);

//					die("<br><br><br>{$quadro_horario[0]},$this->ref_cod_serie,$this->ref_cod_escola,$disciplina");

//					if(!$lista_quadro_horarios)
//					{
						//echo '<script>alert(\'Turma não possui quadro de horário\');
						//window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));</script>';
						//die();
//						continue;
//					}
			$db = new clsBanco();
			$consulta = "SELECT padrao_ano_escolar FROM pmieducar.curso WHERE cod_curso = {$this->ref_cod_curso}";
			$padrao_ano_escolar = $db->CampoUnico($consulta);

			$total_semanas = 0;
			if($padrao_ano_escolar)
			{
				$meses = $db->CampoUnico( "
				SELECT to_char(data_inicio,'dd/mm') || '-' || to_char(data_fim,'dd/mm')
				FROM
					pmieducar.ano_letivo_modulo
					,pmieducar.modulo
				WHERE modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
					AND modulo.ativo = 1
					AND ano_letivo_modulo.ref_cod_modulo = $this->ref_cod_modulo
					AND ano_letivo_modulo.sequencial = $this->sequencial
					AND ref_ano = $this->ano
					AND ref_ref_cod_escola = '{$this->ref_cod_escola}'
				ORDER BY
					data_inicio,data_fim ASC
				");
				$data = explode("-",$meses);

				if(!$this->data_ini)
					$this->data_ini = $data[0];

				if(!$this->data_fim)
					$this->data_fim = $data[1];

				$data_ini = explode("/",$data[0]);
				$data_fim = explode("/",$data[1]);

				$meses = array($data_ini[1],$data_fim[1]);
				$dias = array($data_ini[0],$data_fim[0]);
				//for($mes = $meses[0];$mes<=$meses[1];$mes++)
				//	$total_semanas = $this->getNumeroDiasMes($mes,$this->ano);//,$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
			    //$total_semanas += $this->getNumeroDiasMes($this->mes + 1,$this->ano);//,$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
			}
			else
			{
				$meses = $db->CampoUnico( "
				SELECT to_char(data_inicio,'mm') || '-' || to_char(data_fim,'mm')
					FROM
						pmieducar.turma_modulo
						,pmieducar.modulo
					WHERE modulo.cod_modulo = turma_modulo.ref_cod_modulo
						AND ref_cod_turma = '{$this->ref_cod_turma}'
					AND turma_modulo.ref_cod_modulo = $this->ref_cod_modulo
					AND turma_modulo.sequencial = $this->sequencial
					AND to_char(data_inicio,'yyyy') = $this->ano
					ORDER BY
						data_inicio,data_fim ASC

				");

				$data = explode("-",$meses);

				if(!$this->data_ini)
					$this->data_ini = $data[0];

				if(!$this->data_fim)
					$this->data_fim = $data[1];

				$data_ini = explode("/",$data[0]);
				$data_fim = explode("/",$data[1]);


				$meses = array($data_ini[1],$data_fim[1]);
				$dias = array($data_ini[0],$data_fim[0]);

				//for($mes = $meses[0];$mes<=$meses[1];$mes++)
				//	$total_semanas = $this->getNumeroDiasMes($mes,$this->ano);
			}

					$total_dias_semanas = 0;
					//$total_semanas = $this->getNumeroSemanasMes($this->mes,$this->ano,$lista_quadro_horarios[0],$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
				    //$total_semanas = $this->getNumeroSemanasMes($this->mes + 1,$this->ano,$lista_quadro_horarios[0],$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
					if($lista_quadro_horarios) {

						for($mes_ = $meses[0];$mes_<=$meses[1];$mes_++)
					    {

					    	$mes_final = false;

						    foreach ($lista_quadro_horarios as $dia_semana)
						    {
							    if($mes_ == $meses[0]) // Last Change -> $mes to $mes_
									$dia = $dias[0];
								elseif ($mes == $meses[1])
								{
									$dia = 1;//$dias[1];
									$mes_final = true;
								}
								else
									$dia = 1;
								$total_dias_semanas += $this->getDiasSemanaMes($dia, $mes_,$this->ano,$dia_semana,$mes_final);
						    }
					    }
					} else {
						$total_dias_semanas = 30;
						$this->indefinido = true;
					}
					$this->mes_inicial = (int)$meses[0];
					$this->mes_final = (int)$meses[1];
					$this->pdf->largura  = 842.0;
			  		$this->pdf->altura = 595.0;

				    $this->total = $total_dias_semanas;

				    if(!$this->total)
						break;

					if(!$this->em_branco)
					{
					    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
					    $obj_matricula_turma->setOrderby("nome_ascii");
					    $lista_matricula = $obj_matricula_turma->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array( 1, 2, 3 ),null,null,$this->ano,null,true,null,null,true);
					}


					if($lista_matricula || $this->em_branco)
					{
						$this->pdf->OpenPage();
						$this->addCabecalho();

						if($this->em_branco)
						{
							$lista_matricula = array();
							$this->numero_registros = $this->numero_registros? $this->numero_registros : 20;
							for ($i = 0 ; $i < $this->numero_registros; $i++)
							{
								$lista_matricula[] = '';
							}
						}


						$num = 0;
					    foreach ($lista_matricula as $matricula)
					    {
					    	$num++;

							if($this->page_y > 500)
							{
								$this->desenhaLinhasVertical();
								$this->pdf->ClosePage();
								$this->pdf->OpenPage();
								$this->page_y = 125;
								$this->addCabecalho();


							}

					    	//$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
					    	//$det_matricula = $obj_matricula->detalhe();


					    	//$obj_aluno = new clsPmieducarAluno();
					    	//$det_aluno = array_shift($obj_aluno->lista($matricula['ref_cod_aluno']));

					    	$this->pdf->quadrado_relativo( 30, $this->page_y , 782, 19);
					    	$this->pdf->escreve_relativo($matricula['nome_aluno'] , 33 ,$this->page_y + 4,160, 15, $fonte, 7, $corTexto, 'left' );
					    	$this->pdf->escreve_relativo( sprintf("%02d",$num),757, $this->page_y + 4, 30, 30, $fonte, 7, $corTexto, 'left' );

					    	$this->page_y +=19;



					    }
						$this->desenhaLinhasVertical();
						//$this->rodape();
						$this->pdf->ClosePage();
					}
					else
					{


				     	echo '<script>
				     			alert("Turma não possui matriculas");
				     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
				     		</script>';

				     		return;
					}


				}
				/**
				 * gera diario de clase de avaliacoes
				 */

			}


			//header( "location: " . $this->pdf->GetLink() );
//			$this->pdf->CloseFile();
//			$this->get_link = $this->pdf->GetLink();
			if($this->total)
			{
				$this->pdf->CloseFile();
				$this->get_link = $this->pdf->GetLink();
			}
			else
			{
				$this->mensagem = "Não existem dias letivos cadastrados para esta turma";
			}
		}
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
    $altura = 30;
    $fonte = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $altura, 782, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80, $fonte, 18,
      $corTexto, 'center' );
    $this->pdf->escreve_relativo(date("d/m/Y"), 25, 30, 782, 80, $fonte, 10,
      $corTexto, 'right');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:$this->nm_instituicao", 120, 52,
      300, 80, $fonte, 7, $corTexto, 'left' );
    $this->pdf->escreve_relativo("Escola:{$this->nm_escola}",132, 64, 300, 80,
      $fonte, 7, $corTexto, 'left' );

    $dif = 0;
    if ($this->nm_professor) {
      $this->pdf->escreve_relativo("Prof.Regente:{$this->nm_professor}",111, 76, 300, 80, $fonte, 7, $corTexto, 'left' );
    }
    else {
      $dif = 12;
    }

    $this->pdf->escreve_relativo("Série:{$this->nm_serie}",138, 88  - $dif, 300,
      80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Turma:{$this->nm_turma}",134, 100 - $dif, 300,
      80, $fonte, 7, $corTexto, 'left');

    // Título
    $nm_disciplina = "";
    if ($this->nm_disciplina) {
      $nm_disciplina = " - $this->nm_disciplina";
    }

    $this->pdf->escreve_relativo("Diário de Frequência {$nm_disciplina}", 30,
      75, 782, 80, $fonte, 12, $corTexto, 'center');

    $obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
    $det_modulo = $obj_modulo->detalhe();

    // Data
    $this->pdf->escreve_relativo( "{$this->data_ini} até {$this->data_fim} de {$this->ano}",
      45, 100, 782, 80, $fonte, 10, $corTexto, 'center');

    $this->pdf->linha_relativa(201,125,612,0);
    $this->page_y +=19;

    if ($this->indefinido) {
      $this->pdf->escreve_relativo("Dias de aula: Indefinido", 680, 100, 535,
        80, $fonte, 10, $corTexto, 'left' );
    }
    else {
      $this->pdf->escreve_relativo("Dias de aula: {$this->total}", 715, 100,
        535, 80, $fonte, 10, $corTexto, 'left');
    }
  }


	function desenhaLinhasVertical()
	{
		$corTexto = '#000000';
			/**
			 *
			 */
						//612
		$largura_anos = 550;

				if($this->total >= 1)
				{

					$incremental = floor($largura_anos/ ($this->total +1)) ;

				}else {

					$incremental = 1;
				}

				$reta_ano_x = 200 ;


				$resto = $largura_anos - ($incremental * $this->total);

				for($linha = 0;$linha <$this->total+1;$linha++)
				{

					if(( $resto > 0) /*|| ($linha + 1 == $total && $resto >= 1) */|| $linha == 0)
					{
						$reta_ano_x++;
						$resto--;
					}

						$this->pdf->linha_relativa($reta_ano_x,125,0,$this->page_y - 125);

					$reta_ano_x += $incremental;

				}

				$this->pdf->linha_relativa(812,125,0,$this->page_y - 125);

			$this->pdf->escreve_relativo( "Nº:",755, 128, 100, 80, $fonte, 7, $corTexto, 'left' );
			$this->pdf->linha_relativa(775,125,0,$this->page_y - 125);
			$this->pdf->escreve_relativo( "Faltas",783, 128, 100, 80, $fonte, 7, $corTexto, 'left' );

			$this->rodape();
			$this->pdf->ClosePage();
			$this->pdf->OpenPage();
			$this->page_y = 125;
			$this->addCabecalho();

			for($ct = 125;$ct < 500;$ct += 19)
			{
				$this->pdf->quadrado_relativo( 30, $ct , 782, 19);
			}
			$this->pdf->escreve_relativo( "Observações",30, 130, 782, 30, $fonte, 7, $corTexto, 'center' );
			$this->pdf->linha_relativa(418,144,0,360);

			/**
			 *
			 */
	}

	function rodape()
	{
		$corTexto = '#000000';
		$fonte = 'arial';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,795, 100, 50, $fonte, 7, $corTexto, 'left' );

		//$this->pdf->escreve_relativo( "Assinatura do Diretor(a)", 68,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Assinatura do Professor(a)", 695,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		//$this->pdf->linha_relativa(52,517,130,0);
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


	//function getNumeroSemanasMes($mes,$ano,$primeiro_dia_semana = null,$ultimo_dia_semana = null)
	function getNumeroDiasMes($dia,$mes,$ano,$mes_final = false)
	{
		$year = $ano;
		$month = $mes;

		$date = mktime(1, 1, 1, $month, $dia/*date("d")*/, $year);

		$first_day_of_month = strtotime("-" . (date("d", $date)-1) . " days", $date);
		$last_day_of_month = strtotime("+" . (date("t", $first_day_of_month)-1) . " days", $first_day_of_month);

		//$first_week_day = date("l", $first_day_of_month);
		$last_day_of_month = date("d", $last_day_of_month);

		$numero_dias = 0;
		/**
		 * verifica se dia eh feriado
		 */
		$obj_calendario = new clsPmieducarCalendarioAnoLetivo();
		$obj_calendario->setCamposLista("cod_calendario_ano_letivo");
		$lista = $obj_calendario->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1);
		if($lista)
		{
			$lista_calendario = array_shift($lista);
		}
		$obj_dia = new clsPmieducarCalendarioDia();
		$obj_dia->setCamposLista("dia");
		$dias_nao_letivo = $obj_dia->lista($lista_calendario,$mes,null,null,null,null,null,null,null,null,null,null,1,"'n'");
		if(!$dias_nao_letivo)
			$dias_nao_letivo = array();
		if($mes_final)
		{
			$last_day_of_month = $dia;
			$dia = 1;
		}
		for($day = $dia; $day <= $last_day_of_month; $day++)
		{
			$date = mktime(1, 1, 1, $month, $day, $year);
			$dia_semana_corrente = getdate($date);
			$dia_semana_corrente = $dia_semana_corrente['wday'] + 1;

			if( ($dia_semana_corrente != 1 &&  $dia_semana_corrente != 7) && (array_search($day,$dias_nao_letivo) === false))
				$numero_dias++;
		}


		return $numero_dias;
	}

	function getDiasSemanaMes($dia,$mes,$ano,$dia_semana,$mes_final = false)
	{
		$year = $ano;
		$month = $mes;

		$date = mktime(1, 1, 1, $month, $dia/*date("d")*/, $year);

		$first_day_of_month = strtotime("-" . (date("d", $date)-1) . " days", $date);
		$last_day_of_month = strtotime("+" . (date("t", $first_day_of_month)-1) . " days", $first_day_of_month);

		//$first_week_day = date("l", $first_day_of_month);
		$last_day_of_month = date("d", $last_day_of_month);

		$numero_dias = 0;


		/**
		 * verifica se dia eh feriado
		 */
		$obj_calendario = new clsPmieducarCalendarioAnoLetivo();
		$obj_calendario->setCamposLista("cod_calendario_ano_letivo");
		$lista_calendario = $obj_calendario->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1);
		if(is_array($lista_calendario))
			$lista_calendario = array_shift($lista_calendario);
		$obj_dia = new clsPmieducarCalendarioDia();
		$obj_dia->setCamposLista("dia");
		$dias_nao_letivo = $obj_dia->lista($lista_calendario,$mes,null,null,null,null,null,null,null,null,null,null,1,"'n'");
		if(!$dias_nao_letivo)
			$dias_nao_letivo = array();
		if($mes_final)
		{
			$last_day_of_month = $dia;
			$dia = 1;
		}
		for($day = $dia; $day <= $last_day_of_month; $day++)
		{
			$date = mktime(1, 1, 1, $month, $day, $year);
			$dia_semana_corrente = getdate($date);
			$dia_semana_corrente = $dia_semana_corrente['wday'] + 1;

			$data_atual = "{$day}/{$mes}/{$ano}";
			$data_final = "{$this->data_fim}/{$ano}";

			if(($dia_semana ==  $dia_semana_corrente) && (array_search($day,$dias_nao_letivo) === false) && data_maior($data_final, $data_atual))
				$numero_dias++;
		}
		return $numero_dias;
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
