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
 * Relatório de controle de desempenho de alunos.
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Controle Bimestral de Desempenho de Alunos" );
		$this->processoAp = "654";
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

	var $get_link;

	var $cursos = array();

	var $array_disciplinas = array();

	var $ref_cod_modulo;

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

	var $array_serie_abr = array(

		 99 => "Série 2"
		,85 => "Matern. I"
		,83 => "Berçar I"
		,87 => "Jardim I"
		,89 => "Pré"
		,97 => ""
		,12 => "oitava série"
		,98 => ""
		,100 => ""
		,96 => "Primeira"
		,28 => "1º Ciclo"
		,30 => "2º Ciclo"
		,31 => "3º Ciclo"
		,33 => "4º Ciclo"
		,15 => "1 Básico"
		,6 => "2º Ano"
		,5 => "1º Reg"
		,7 => "3º Ano"
		,8 => "4º Ano"
		,9 => "5º Ano"
		,10 => "6º Ano"
		,11 => "7º Ano"
		,25 => "8º Ano"
		,86 => "Mater II"
		,84 => "Berç. II"
		,88 => "Jard. II"
		,27 => "1º Ciclo"
		,29 => "2º Ciclo"
		,32 => "4º Ciclo");

	var $semestre;
	var $is_padrao;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}
//		echo "<pre>"; print_r($_POST); die();
		if(!$_POST)
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNão existem dados!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}


		$modulo_sequencial = explode("-",$this->ref_cod_modulo);
		$this->ref_cod_modulo = $modulo_sequencial[0];
		$this->sequencial = $modulo_sequencial[1];

		if(empty($this->ref_cod_curso))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhum curso selecionado!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}

		if ($this->is_padrao || $this->ano == 2007) {
			$this->semestre = null;
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
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
	     }


		$obj = new clsPmieducarSerie();
		$obj->setOrderby('cod_serie,etapa_curso');
		$lista_serie_curso = $obj->lista(null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao,null,null,null,$this->ref_cod_escola);


		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();

		$media_curso = $det_curso['media'];

		if($lista_serie_curso)
		{


			$this->pdf = new clsPDF("Controle Bimestral de Desempenho de Alunos - {$this->ano}", "Controle Bimestral de Desempenho de Alunos - {$this->ano}", "A4", "", false, false);

			$this->pdf->largura  = 842.0;
	  		$this->pdf->altura = 595.0;


			$fonte = 'arial';
			$corTexto = '#000000';

			$this->pdf->OpenPage();
			$this->addCabecalho();


			$altura_linha = 23;
			$inicio_escrita_y = 175;

			$serie_ini =  $lista_serie_curso[0];
			$serie_fim = $lista_serie_curso[count($lista_serie_curso)-1];
			$total_geral_alunos = 0;
			$db = new clsBanco();
			foreach ($lista_serie_curso as $key => $serie)
			{

				$total_geral_alunos_turma = 0;

				$this->buscaDisciplinas($serie['cod_serie']);

				$this->novoCabecalho($serie['nm_serie']);

				$obj_turmas = new clsPmieducarTurma();
				$obj_turmas->setOrderby("nm_turma");
				$lista_turmas = $obj_turmas->lista(null,null,null,$serie['cod_serie'],$this->ref_cod_escola);

				if($lista_turmas)
				{
					foreach ($lista_turmas as $turma)
					{
						foreach ($this->array_disciplinas as $key => $value)
						{
							$this->array_disciplinas[$key]['total_disciplina_abaixo_media_serie']= 0;
							$this->array_disciplinas[$key]['total_disciplina_media_serie'] = 0;
						}

						if($turma['hora_inicial'] <= '12:00')
							$turno = 'M';
						elseif($turma['hora_inicial'] > '12:00' && $turma['hora_inicial'] <= '18:00')
							$turno = 'V';
						else
							$turno = 'N';

						$obj_servidor = new clsPessoa_($turma['ref_cod_regente']);
						$det_sevidor =  $obj_servidor->detalhe();
//						$obj_mat_turma = new clsPmieducarMatriculaTurma();
//						$lista_matricula = $obj_mat_turma->lista(null,$turma['cod_turma']);
						if (is_numeric($this->semestre) && $this->ano != 2007 && !$this->is_padrao)
						{
							$sql = " AND m.semestre = {$this->semestre} ";
						}
						$select = "SELECT na.ref_cod_disciplina
									       ,na.ref_cod_serie
									       ,tav.valor
									       ,na.ref_cod_escola
									       ,na.ref_cod_matricula
									   	   ,cod_transferencia_solicitacao
									  FROM pmieducar.nota_aluno 		 na
									       ,pmieducar.tipo_avaliacao_valores tav
									       ,pmieducar.matricula_turma	 mt
									       ,pmieducar.matricula			 m
									      	LEFT OUTER JOIN pmieducar.transferencia_solicitacao ts ON
									       		(ref_cod_matricula_saida = m.cod_matricula AND
									       		(data_transferencia >= (SELECT data_inicio FROM pmieducar.ano_letivo_modulo an, pmieducar.modulo m
																		WHERE m.cod_modulo = an.ref_cod_modulo AND m.ativo = 1 and ref_ano = {$this->ano}
																		AND ref_ref_cod_escola = {$this->ref_cod_escola} AND sequencial = {$this->sequencial}) AND
												 data_transferencia <= (SELECT data_fim FROM pmieducar.ano_letivo_modulo an, pmieducar.modulo m
																		WHERE m.cod_modulo = an.ref_cod_modulo
																		AND m.ativo = 1 AND ref_ano = {$this->ano}
																		AND ref_ref_cod_escola = {$this->ref_cod_escola} AND sequencial = {$this->sequencial})
												))
									 WHERE na.ref_cod_serie 	= {$serie['cod_serie']}
									   AND na.ref_cod_escola 	= {$this->ref_cod_escola}
									   AND mt.ref_cod_turma = {$turma['cod_turma']}
									   AND na.ativo 		= 1
									   AND na.modulo = {$this->sequencial}
									   AND na.ref_ref_cod_tipo_avaliacao = tav.ref_cod_tipo_avaliacao
									   AND na.ref_sequencial     	     = tav.sequencial
									   AND na.ref_cod_matricula	         = mt.ref_cod_matricula
									   AND m.cod_matricula        		 = mt.ref_cod_matricula
									   AND m.ano     = {$this->ano}
									   AND cod_transferencia_solicitacao IS NULL
									   {$sql}
									   AND mt.ativo  = 1 ORDER BY na.ref_cod_disciplina, na.ref_cod_matricula";

						$db->Consulta($select);

						$alunos = null;
						$media_turma = array(array());

						while($db->ProximoRegistro())
						{

							$alunos[$tupla['ref_cod_matricula']] = $tupla['ref_cod_matricula'];

							$tupla = $db->Tupla();
							if($tupla['valor'] < $media_curso){
								$this->array_disciplinas[$tupla['ref_cod_disciplina']]['total_disciplina_abaixo_media_serie']++;
								$this->array_disciplinas[$tupla['ref_cod_disciplina']]['total_geral_disciplina_abaixo_media_serie']++;

							}
							else
							{
								$this->array_disciplinas[$tupla['ref_cod_disciplina']]['total_disciplina_media_serie']++;
								$this->array_disciplinas[$tupla['ref_cod_disciplina']]['total_geral_disciplina_media_serie']++;

							}
							$media_turma[$tupla['ref_cod_disciplina']]['media'] = $media_turma[$tupla['ref_cod_disciplina']]['media'] + $tupla['valor'];
							$media_turma[$tupla['ref_cod_disciplina']]['total'] = $media_turma[$tupla['ref_cod_disciplina']]['total'] + 1;
						}

						//$media_turma = $media_turma / $total;
						//$media_turma = number_format($media_turma,2,'.','');

						if(is_array($alunos))
							$alunos = count($alunos)-1;
						else
							$alunos = 0;

						foreach ($media_turma as $key =>$media)
						{
							if($media_turma[$key]['total'])
								$media_turma[$key]['media'] = $media_turma[$key]['media'] / $media_turma[$key]['total'];
							$media_turma[$key]['media'] = number_format($media_turma[$key]['media'],2,'.','');

						}
						//echo '<pre>';print_r($alunos);die;
						$total_alunos_turma = $alunos;//$obj_mat_turma->_total;
						$total_geral_alunos_turma +=  $alunos;//$obj_mat_turma->_total;
						$total_geral_alunos += $alunos;//$obj_mat_turma->_total;


						$expande = 24;
						//linha
						$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_linha);

						//linha aluno
						$this->pdf->quadrado_relativo( 30, $this->page_y, 150 + $expande , $altura_linha);

						//linha serie
						$this->pdf->quadrado_relativo( 30, $this->page_y, 120 + $expande , $altura_linha);

					    //linha professor regente
						$this->pdf->quadrado_relativo( 30, $this->page_y, 90 + $expande , $altura_linha);
						//$this->pdf->quadrado_relativo( 30, 125, 170 + $expande + 30, $altura);

						//linha turno
						$this->pdf->quadrado_relativo( 30, $this->page_y, 20, $altura_linha);


						$centralizado = abs(($altura - 12) / 2) + $this->page_y;

						//posicao serie
						$serie_x = 38;
						$this->pdf->escreve_relativo( "$turno", $serie_x,$centralizado , 50, 50, $fonte, 7, $corTexto, 'left' );
						//regente
						$turma_x = 30 + $expande;
						$this->pdf->escreve_relativo( $det_sevidor['nome'], $turma_x -5,$centralizado, 100, 50, $fonte, 7, $corTexto, 'center' );
						//serie
						$alunos_x = 80 + $expande;
						//$this->pdf->escreve_relativo( $this->array_serie_abr["{$serie['cod_serie']}"], $alunos_x + 5,$centralizado , 100, 40, $fonte, 7, $corTexto, 'center' );
						$this->pdf->escreve_relativo( $turma['nm_turma'], $alunos_x + 5,$centralizado , 100, 40, $fonte, 7, $corTexto, 'center' );

						$n_alunos_x = 110 + $expande;
						$this->pdf->escreve_relativo( "$total_alunos_turma", $n_alunos_x + 5,$centralizado , 100, 40, $fonte, 8, $corTexto, 'center' );

						$inicio_escrita_y = $this->page_y + 5;

						$largura_anos = 590;


						if(sizeof($this->array_disciplinas) >= 1)
						{

							$incremental = (int)ceil($largura_anos/ (sizeof($this->array_disciplinas)));

						}else {

							$incremental = 1;
						}

						$reta_ano_x = 209 ;
						$anos_x = 209;


						$ct = 0;
						foreach ($this->array_disciplinas as $key => $disciplina)
						{


							//medias
							//$this->pdf->escreve_relativo( $disciplina['total_disciplina_media_serie'] == 0 ?  '' : $disciplina['total_disciplina_media_serie'] , $anos_x ,$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
							$this->pdf->escreve_relativo( $media_turma[$disciplina['ref_cod_disciplina']]['media'] == 0 ?  '' : $media_turma[$disciplina['ref_cod_disciplina']]['media'] , $anos_x ,$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
							$this->pdf->linha_relativa($reta_ano_x+($incremental/3),$inicio_escrita_y -5,0,23);
							$this->pdf->escreve_relativo( $disciplina['total_disciplina_abaixo_media_serie'] == 0 ?  '' : $disciplina['total_disciplina_abaixo_media_serie'] , $anos_x +($incremental/3),$inicio_escrita_y , ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
							$this->pdf->linha_relativa($reta_ano_x+($incremental*2/3),$inicio_escrita_y -5,0,23);
							$this->pdf->escreve_relativo( ($disciplina['total_disciplina_media_serie'] + $disciplina['total_disciplina_abaixo_media_serie'] ) > 0 ? (ceil($disciplina['total_disciplina_abaixo_media_serie'] / ($disciplina['total_disciplina_media_serie'] + $disciplina['total_disciplina_abaixo_media_serie'])*100)) : '-', $anos_x +($incremental*2/3),$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );

							$anos_x += $incremental;
							$reta_ano_x += $incremental;

							if($ct +1 < sizeof($this->array_disciplinas))
								$this->pdf->linha_relativa($reta_ano_x,$inicio_escrita_y - 5,0,23);
							$ct++;
						}

						$this->page_y +=$altura_linha;
//						echo '<pre>';print_r($total_alunos_turma);die;
					}
				}

				$expande = 24;
				//linha
				$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_linha);

				//linha aluno
				$this->pdf->quadrado_relativo( 30, $this->page_y, 150 + $expande , $altura_linha);

				//linha serie
				$this->pdf->quadrado_relativo( 30, $this->page_y, 120 + $expande , $altura_linha);

				$centralizado = abs(($altura - 6) / 2) + $this->page_y;



				//posicao numero turmas
				$turma_x = 38;
				$this->pdf->escreve_relativo( "Subtotal ".$serie['nm_serie'], $turma_x -5,$centralizado, 120, 50, $fonte, 7, $corTexto, 'center' );

				$n_alunos_x = 110 + $expande;
				$this->pdf->escreve_relativo( "{$total_geral_alunos_turma}", $n_alunos_x + 5,$centralizado +2, 100, 40, $fonte, 8, $corTexto, 'center' );

				$inicio_escrita_y = $this->page_y + 5;

				$largura_anos = 590;


				if(sizeof($this->array_disciplinas) >= 1)
				{

					$incremental = (int)ceil($largura_anos/ (sizeof($this->array_disciplinas)));

				}else {

					$incremental = 1;
				}

				$reta_ano_x = 209 ;
				$anos_x = 209;


				$ct = 0;
				foreach ($this->array_disciplinas as $key => $disciplina)
				{
					//medias
				//	$this->pdf->escreve_relativo( $disciplina['total_geral_disciplina_media_serie'] == 0 ? '' : $disciplina['total_geral_disciplina_media_serie'], $anos_x ,$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
					$this->pdf->escreve_relativo( '-', $anos_x ,$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
					$this->pdf->linha_relativa($reta_ano_x+($incremental/3),$inicio_escrita_y -5,0,23);
					$this->pdf->escreve_relativo(  $disciplina['total_geral_disciplina_abaixo_media_serie'] == 0 ? '' : $disciplina['total_geral_disciplina_abaixo_media_serie'], $anos_x +($incremental/3),$inicio_escrita_y , ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
					$this->pdf->linha_relativa($reta_ano_x+($incremental*2/3),$inicio_escrita_y -5,0,23);
					//$this->pdf->escreve_relativo( ($disciplina['total_geral_disciplina_abaixo_media_serie'] + $disciplina['total_geral_disciplina_media_serie']) == 0 ? '' : ceil(($disciplina['total_geral_disciplina_abaixo_media_serie'] / ($disciplina['total_geral_disciplina_abaixo_media_serie'] + $disciplina['total_geral_disciplina_media_serie']))*100)."%", $anos_x +($incremental*2/3),$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );
					$this->pdf->escreve_relativo( '-', $anos_x +($incremental*2/3),$inicio_escrita_y, ($incremental/3), $altura_linha, $fonte, 8, $corTexto, 'center' );

					$anos_x += $incremental;
					$reta_ano_x += $incremental;

					if($ct +1 < sizeof($this->array_disciplinas))
						$this->pdf->linha_relativa($reta_ano_x,$inicio_escrita_y - 5,0,23);
					$ct++;
				}

				$this->page_y +=$altura_linha;

				if($this->page_y + $altura_linha > 510)
				{
					$this->pdf->ClosePage();
					$this->pdf->OpenPage();
					$this->addCabecalho();
					$this->page_y = 125;
					$this->novoCabecalho($serie['nm_serie']);
				}

//				echo "uiauiaaiuauaiuaiuiuiauiuaiuaiuaiuiui";
			}

//			die('lllllllllllllllllllllllllllllllllllllllll!!!!!!!!!!!');

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
		else
		{
			echo "<center> Não existem dados a serem exibidos! </center>";
		}


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

    $this->pdf->quadrado_relativo( 30, $altura, 782, 85 );
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80, $fonte, 18,
      $corTexto, 'center' );

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:$this->nm_instituicao", 120, 58,
      300, 80, $fonte, 8, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola:{$this->nm_escola}",136, 70, 300, 80,
      $fonte, 8, $corTexto, 'left');
    $this->pdf->escreve_relativo(date("d/m/Y"), 25, 30, 782, 80, $fonte, 10,
      $corTexto, 'right' );

    // Título
    $this->pdf->escreve_relativo("Controle de Desempenho de Alunos ", 30, 85,
      782, 80, $fonte, 12, $corTexto, 'center');

    $obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
    $det_modulo = $obj_modulo->detalhe();

    //Data
    $this->pdf->escreve_relativo( "{$this->sequencial}º {$det_modulo['nm_tipo']}/{$this->ano}", 45, 100, 782, 80, $fonte, 8, $corTexto, 'center' );
  }


	function novoCabecalho($nm_serie)
	{
		$altura2 = 300;
		$altura = 50;

		$expande = 24;

		$fonte = 'arial';
		$corTexto = '#000000';

		if($this->page_y + 49 > 510)
		{
			$this->pdf->ClosePage();
			$this->pdf->OpenPage();
			$this->addCabecalho();
			$this->page_y = 125;
		}

		//linha
		$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura);

		//linha aluno
		$this->pdf->quadrado_relativo( 30, $this->page_y, 150 + $expande , $altura);

		//linha serie
		$this->pdf->quadrado_relativo( 30, $this->page_y, 120 + $expande , $altura);

	    //linha professor regente
		$this->pdf->quadrado_relativo( 30, $this->page_y, 90 + $expande , $altura);

		//linha turno
		$this->pdf->quadrado_relativo( 30, $this->page_y, 20, $altura);


		$centralizado = abs(($altura - 10) / 2) + $this->page_y;

		//posicao serie
		$serie_x = 38;
		$this->pdf->escreve_relativo( "T\nu\nr\nn\no", $serie_x,$centralizado - 15, 50, 50, $fonte, 7, $corTexto, 'left' );
		//posicao numero turmas
		$turma_x = 30 + $expande;
		$this->pdf->escreve_relativo( "Professor/Regente", $turma_x -5,$centralizado, 100, 50, $fonte, 7, $corTexto, 'center' );
		//posicao numero alunos
		$alunos_x = 80 + $expande;
		$this->pdf->escreve_relativo( "Turma", $alunos_x + 5,$centralizado , 100, 40, $fonte, 7, $corTexto, 'center' );

		$n_alunos_x = 110 + $expande;
		$this->pdf->escreve_relativo( "Nº\nAlunos", $n_alunos_x + 5,$centralizado - 5 , 100, 40, $fonte, 7, $corTexto, 'center' );

		$necessidade_x = 220 + $expande;
		$this->pdf->escreve_relativo( "$nm_serie", $alunos_x +80, $this->page_y + 2, 620, $altura, $fonte, 12, $corTexto, 'center' );

		$inicio_escrita_y = $this->page_y + 19;

		$this->pdf->linha_relativa(205,$this->page_y + 34,607,0);
		$this->pdf->linha_relativa(205,$this->page_y + 20,607,0);

		$largura_anos = 590;


		if(sizeof($this->array_disciplinas) >= 1)
		{

			$incremental = (int)ceil($largura_anos/ (sizeof($this->array_disciplinas)));

		}else {

			$incremental = 1;
		}

		$reta_ano_x = 209 ;
		$anos_x = 209;

		$ct = 0;
		foreach ($this->array_disciplinas as $key => $disciplina)
		{
			$this->pdf->escreve_relativo($disciplina['nm_disciplina'], $anos_x ,$inicio_escrita_y + 1, $incremental, $altura, $fonte, 10, $corTexto, 'center' );

			//medias
			$this->pdf->escreve_relativo( 'M', $anos_x ,$inicio_escrita_y + 17, ($incremental/3), $altura, $fonte, 10, $corTexto, 'center' );
			$this->pdf->linha_relativa($reta_ano_x+($incremental/3),$inicio_escrita_y + 15,0,16);
			$this->pdf->escreve_relativo( '< M', $anos_x +($incremental/3),$inicio_escrita_y + 17, ($incremental/3), $altura, $fonte, 10, $corTexto, 'center' );
			$this->pdf->linha_relativa($reta_ano_x+($incremental*2/3),$inicio_escrita_y + 15,0,16);
			$this->pdf->escreve_relativo( '%', $anos_x +($incremental*2/3),$inicio_escrita_y+17, ($incremental/3), $altura, $fonte, 10, $corTexto, 'center' );

			$anos_x += $incremental;
			$reta_ano_x += $incremental;

			if($ct +1 < sizeof($this->array_disciplinas))
				$this->pdf->linha_relativa($reta_ano_x,$inicio_escrita_y +1,0,29);
			$ct++;
		}

		$this->page_y +=49;

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
		$lista_disciplinas = $obj_disciplinas->lista(null,$serie,1);

		//while(is_array($lista_disciplinas) && sizeof($lista_disciplinas) > 0)
		if(is_array($lista_disciplinas))
		{
			foreach ($lista_disciplinas as $key => $disciplina)
			{
				//$disciplina = array_shift($lista_disciplinas);
				$obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
				$det_disciplina = $obj_disciplina->detalhe();

				$this->array_disciplinas["{$det_disciplina["cod_disciplina"]}"] = array('ref_cod_disciplina' =>$det_disciplina['cod_disciplina'],'nm_disciplina' => substr($det_disciplina['abreviatura'],0,6),'total_disciplina_abaixo_media_serie' => 0,'total_disciplina_media_serie' => 0,'total_geral_disciplina_abaixo_media_serie' => 0,'total_geral_disciplina_media_serie' => 0);
			}

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
