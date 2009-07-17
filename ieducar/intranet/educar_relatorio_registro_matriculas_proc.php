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
 * Relatório de Registro de matrículas.
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Registro de Matr&iacute;culas" );
		$this->processoAp = "693";
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
	var $ref_cod_curso;
	var $ref_cod_turma;

	var $ano;

	var $nm_escola;
	var $nm_instituicao;
	var $nm_curso;

	var $pdf;

	var $page_y = 139;

	var $get_link;

	var $total;

	var $campo_assinatura;

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

	var $is_padrao;
	var $semestre;

	var $nm_serie_ = "";
	var $nm_turma_ = "";

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

		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		}
		if (is_numeric($this->ref_cod_serie)) {
			$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
			$det_serie = $obj_serie->detalhe();
			$this->nm_serie_ = $det_serie["nm_serie"];
		}
		if (is_numeric($this->ref_cod_turma)) {
			$obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
			$det_turma = $obj_turma->detalhe();
			$this->nm_turma_ = $det_turma["nm_turma"];
		}

		$this->pdf = new clsPDF("Registro de Matrículas - {$this->ano}", "Registro de Matrículas", "A4", "", false, false);

		$this->pdf->largura  = 842.0;
  		$this->pdf->altura = 595.0;


		$this->page_y = 125;

		if ($this->is_padrao || $this->ano == 2007)
		{
			$this->semestre = null;
		}

		$obj_matricula = new clsPmieducarMatricula();
		$obj_matricula->setOrderby("ref_ref_cod_escola, ref_ref_cod_serie, ref_cod_curso");
	    $lista_matricula = $obj_matricula->lista(null,null,$this->ref_cod_escola,$this->ref_cod_serie,null,null,null,array(1,2,3),null,null,null,null,1,$this->ano,$this->ref_cod_curso,$this->ref_cod_instituicao,null,null,null, null, null, null, null, null, null, null, null, null, null, null, $this->semestre, $this->ref_cod_turma);

		if($lista_matricula)
		{
			$obj_series = new clsPmieducarSerie();
			$lst_series = $obj_series->lista(null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao,null,null,null,$this->ref_cod_escola);
			if($lst_series)
			{
				$lst_series2 = array();
				foreach ($lst_series as $serie)
				{
					$lst_series2[$serie['cod_serie']] = $serie;
				}

				$lst_series = $lst_series2;

				unset($lst_series2);

			}

			$obj_turmas = new clsPmieducarTurma();
			$lst_turmas = $obj_turmas->lista(null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso, $this->ref_cod_instituicao);

			if($lst_turmas)
			{
				$lst_turmas2 = array();
				foreach ($lst_turmas as $turma)
				{
					$lst_turmas2[$turma['cod_turma']] = $turma;
				}

				$lst_turmas = $lst_turmas2;

				unset($lst_turmas2);

			}

			$obj_cursos = new clsPmieducarCurso();
			$lst_cursos = $obj_cursos->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null ,null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao);

			if($lst_cursos)
			{

				$lst_curso2 = array();
				foreach ($lst_cursos as $curso)
				{
					$lst_cursos2[$curso['cod_curso']] = $curso;
				}

				$lst_cursos = $lst_cursos2;

				unset($lst_curso2);

			}

			$altura_caixa = 45;


			//$curso = $lst_cursos[$lista_matricula[0]['cod_curso']];
			//$this->nm_curso = $curso['nm_curso'];

			$ultimo_cod = $lista_matricula[0]['cod_curso'];

			$this->pdf->OpenPage();
			$this->addCabecalho();
			$this->addTitulo();


		    foreach ($lista_matricula as $matricula)
		    {

				$this->nm_curso = $lst_cursos[$matricula['ref_cod_curso']]['nm_curso'];

		    	if($this->page_y > 530 || $ultimo_cod != $matricula['ref_cod_curso'])
				{
					$this->pdf->ClosePage();
					$this->pdf->OpenPage();
					$this->page_y = 125;
					$this->addCabecalho();
					$this->addTitulo();

				}

				$obj_pessoa = new clsPessoaFisica($matricula['ref_idpes']);
				$det_pessoa = $obj_pessoa->detalhe();
				$nacionalidade = array('NULL' => "Selecione", '1' => "Brasileiro", '2' => "Naturalizado Brasileiro", '3' => "Estrangeiro");
				$nacionalidade = $nacionalidade[$det_pessoa['nacionalidade']];
				$det_municipio =$det_pessoa['idmun_nascimento']->detalhe();

				$data_nasc = explode("-",$det_pessoa['data_nasc']);
				$idade = calculoIdade($data_nasc[2], $data_nasc[1], $data_nasc[0]);
				$data_nasc = implode("/",array($data_nasc[2], $data_nasc[1], $data_nasc[0]));
				$y_escrita    = $this->page_y + $altura_caixa / 4;


				$obj_aluno = new clsPmieducarAluno($matricula['ref_cod_aluno']);
				$det_aluno = $obj_aluno->detalhe();
				$obj_fisica= new clsFisica($det_aluno["ref_idpes"]);
				$det_fisica = $obj_fisica->detalhe();

				if(!$det_aluno['nm_mae'])
				{
					if($det_fisica["idpes_mae"] )
					{
						$obj_ref_idpes = new clsPessoa_( $det_fisica["idpes_mae"] );
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						$det_aluno['nm_mae'] = $det_ref_idpes['nome'];

					}
					elseif($det_fisica['nome_mae'])
					{
						$det_aluno['nm_mae'] = $det_fisica['nome_mae'];
					}
				}

				if(!$det_aluno['nm_pai'])
				{

					if($det_fisica["idpes_pai"] )
					{
						$obj_ref_idpes = new clsPessoa_( $det_fisica["idpes_pai"] );
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						$det_aluno['nm_pai'] = $det_ref_idpes["nome"];

					}
					elseif($det_fisica['nome_pai'])
					{
						$det_aluno['nome_pai'] = $det_fisica['nome_pai'];
					}
				}

				if($det_aluno['tipo_responsavel'] == 'r' || $det_aluno['tipo_responsavel'] == '' )
				{
					if($det_fisica["idpes_responsavel"] )
					{
						$obj_ref_idpes = new clsPessoa_( $det_fisica["idpes_responsavel"] );
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						if($det_aluno['nome_pai'])
						{
							if($det_aluno['nm_responsavel'] != $det_aluno['nome_pai'])
								$det_aluno['nm_responsavel'] = $det_ref_idpes["nome"];
						}
						elseif($det_aluno['nome_mae'])
						{
							if($det_aluno['nm_responsavel'] != $det_aluno['nome_mae'])
								$det_aluno['nm_responsavel'] = $det_ref_idpes["nome"];
						}
						else
						{
							$det_aluno['nm_responsavel'] = $det_ref_idpes["nome"];
						}

					}
					elseif($det_fisica['nome_responsavel'])
					{
						if($det_aluno['nome_pai'])
						{
							if($det_fisica['nome_responsavel'] != $det_aluno['nome_pai'])
								$det_aluno['nm_responsavel'] = $det_fisica['nome_responsavel'];
						}
						elseif($det_aluno['nome_mae'])
						{
							if($det_fisica['nome_responsavel'] != $det_aluno['nome_mae'])
								$det_aluno['nm_responsavel'] = $det_fisica['nome_responsavel'];
						}
						else
						{
							$det_aluno['nm_responsavel'] = $det_fisica['nome_responsavel'];
						}
					}
				}
				if($det_aluno['nm_pai'])
					$det_aluno['nm_pai'] = "{$det_aluno['nm_pai']}\n";

				if($det_aluno['nm_mae'])
					$det_aluno['nm_mae'] = "{$det_aluno['nm_mae']}\n";


				$filiacao = "{$det_aluno['nm_pai']}{$det_aluno['nm_mae']}{$det_aluno['nm_responsavel']}";

				$obj_matricula_turma = new clsPmieducarMatriculaTurma();
				$lst_matricula_turma = $obj_matricula_turma->lista($matricula['cod_matricula'], null, null, null, null, null, null, null, 1, $matricula['ref_ref_cod_serie'], $matricula['ref_cod_curso'], $matricula['ref_ref_cod_escola'], null, $matricula['ref_cod_aluno'], null, null, null);

				if(is_array($lst_matricula_turma))
				{
					$lst_matricula_turma = array_shift($lst_matricula_turma);

					$hora_inicial = $lst_turmas[$lst_matricula_turma['ref_cod_turma']]['hora_inicial'];
					$hora_final   = $lst_turmas[$lst_matricula_turma['ref_cod_turma']]['hora_final'];

					if($hora_inicial >= '07:00' and $hora_inicial <= '12:00')
						$turno = 'Matutino';
					else if($hora_inicial > '12:00' and $hora_inicial <= '18:00')
						$turno = 'Vespertino';
					else
					$turno = 'Noturno';
				}
				else
				{
					$turno = 'N/A';
				}


				$obj_endereco = new clsPessoaEndereco($det_aluno["ref_idpes"]);
				if($obj_endereco_det = $obj_endereco->detalhe())
				{
					$id_cep = $obj_endereco_det['cep']->cep;
					$id_bairro = $obj_endereco_det['idbai']->detalhe();
					$id_logradouro = $obj_endereco_det['idlog']->detalhe();
					$id_mun = $id_bairro['idmun']->detalhe();

					$id_logradouro = $id_logradouro['idlog']->detalhe();
					$idtlog = $id_logradouro[1];

					$numero = $obj_endereco_det['numero'];
					$letra = $obj_endereco_det['letra'];
					$complemento = $obj_endereco_det['complemento'];
					$andar = $obj_endereco_det['andar'];
					$apto = $obj_endereco_det['apartamento'];
					$bloco = $obj_endereco_det['bloco'];

					$cidade = $id_mun['nome'];
					$bairro =  $id_bairro['nome'];
					$logradouro =  $id_logradouro['nome'];

					//$endereco_uf =  $obj_endereco_det['sigla_uf'];
					$endereco_uf =  $id_bairro['idmun']->sigla_uf;

					$cep = int2CEP($id_cep);
				}
				else
				{
					$obj_endereco = new clsEnderecoExterno($det_aluno["ref_idpes"]);
					if($obj_endereco_det = $obj_endereco->detalhe())
					{
						$id_cep         = $obj_endereco_det['cep'];
						$cidade =  $obj_endereco_det['cidade'];
						$bairro =  $obj_endereco_det['bairro'];
						$logradouro =  $obj_endereco_det['logradouro'];

						$numero    	= $obj_endereco_det['numero'];
						$letra    	= $obj_endereco_det['letra'];
						$complemento  = $obj_endereco_det['complemento'];
						$andar    	= $obj_endereco_det['andar'];
						$apto  = $obj_endereco_det['apartamento'];
						$bloco	    = $obj_endereco_det['bloco'];

						$idtlog = $obj_endereco_det['idtlog']->idtlog;
				 		$endereco_uf = $obj_endereco_det['sigla_uf']->sigla_uf;
						$cep = int2CEP($id_cep);
					}
				}
				$idtlog = ucfirst(strtolower($idtlog));
				$logradouro = minimiza_capitaliza(($logradouro));
				$cidade = minimiza_capitaliza($cidade);
				$endereco = "{$idtlog} $logradouro,{$numero} {$letra} {$complemento} $apto $bloco $andar\n$cep $bairro, $cidade $endereco_uf";


				$this->pdf->quadrado_relativo( 30, $this->page_y, 782, $altura_caixa );

				$this->pdf->quadrado_relativo( 30, $this->page_y, 40, $altura_caixa );

				$this->pdf->quadrado_relativo( 70, $this->page_y, 50, $altura_caixa );

				$this->pdf->quadrado_relativo( 115, $this->page_y, 175, $altura_caixa );

				$this->pdf->quadrado_relativo( 285, $this->page_y, 55, $altura_caixa );

				$this->pdf->quadrado_relativo( 335, $this->page_y, 190, $altura_caixa );

				$this->pdf->quadrado_relativo( 520, $this->page_y, 160, $altura_caixa );

				$this->pdf->quadrado_relativo( 680, $this->page_y, 80, $altura_caixa );


				$this->pdf->escreve_relativo( "{$matricula['ref_cod_aluno']}", 30, $y_escrita + 5, 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				$this->pdf->escreve_relativo( "{$matricula['cod_matricula']}", 70, $y_escrita+ 5, 50, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				$this->pdf->escreve_relativo( "{$matricula['nome']}\n$endereco", 115, $y_escrita- 3, 175, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				$this->pdf->escreve_relativo( "{$data_nasc}\n{$idade} anos", 285, $y_escrita + 5 , 50, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				$this->pdf->escreve_relativo( "$filiacao", 335, $y_escrita + 5, 175, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				if($this->campo_assinatura)
					$this->pdf->escreve_relativo( " ", 520, $y_escrita + 2 , 160, $altura_caixa, $fonte, 8, $corTexto, 'center' );
				else
					$this->pdf->escreve_relativo( "{$nacionalidade}\n{$det_municipio['nome']}", 520, $y_escrita + 2 , 160, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				$obj_matricula_turma2 = new clsPmieducarMatriculaTurma();
				$det_matricula_turma2 = $obj_matricula_turma2->lista($matricula["cod_matricula"], null, null, null, null, null, null, null, 1);
				if (is_array($det_matricula_turma2))
				{
					$det_matricula_turma2 = array_shift($det_matricula_turma2);
					$obj_turma = new clsPmieducarTurma($det_matricula_turma2["ref_cod_turma"]);
					$det_turma = $obj_turma->detalhe();
				}

				$this->pdf->escreve_relativo( "{$lst_series[$matricula['ref_ref_cod_serie']]['nm_serie']}\n{$det_turma["nm_turma"]}", 680, $y_escrita + 5, 80, $altura_caixa, $fonte, 8, $corTexto, 'center' );

				$this->pdf->escreve_relativo( "{$turno}", 760, $y_escrita + 5, 50, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		    	$this->page_y +=$altura_caixa;

		    	$ultimo_cod = $matricula['ref_cod_curso'];

		    }

			$this->pdf->ClosePage();
		}
		else
		{

			echo '<script>
	     					alert("A turma não possui matrículas");
	     					window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
			     	return true;

			return;
		}

		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();


		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='_blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
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

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80,
      $fonte, 18, $corTexto, 'center');
    $this->pdf->escreve_relativo(date("d/m/Y"), 745, 30, 100, 80, $fonte, 12,
      $corTexto, 'left');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:$this->nm_instituicao", 120, 52,
      300, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola:{$this->nm_escola}",132, 64, 300, 80,
      $fonte, 7, $corTexto, 'left');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:$this->nm_instituicao", 120, 52,
      300, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola:{$this->nm_escola}",132, 64, 300, 80,
      $fonte, 7, $corTexto, 'left');

    if (!empty($this->nm_serie_)) {
      $this->pdf->escreve_relativo("Série: {$this->nm_serie_}",132, 76, 300, 80,
        $fonte, 7, $corTexto, 'left');
    }

    if (!empty($this->nm_turma_)) {
      $this->pdf->escreve_relativo("Turma: {$this->nm_turma_}",132, 88, 300,
        80, $fonte, 7, $corTexto, 'left');
    }

    $this->pdf->escreve_relativo("Registro de Matrículas - {$this->ano}", 30,
      75, 782, 80, $fonte, 12, $corTexto, 'center');
    $this->pdf->escreve_relativo("{$this->nm_curso}", 30, 90, 782, 80, $fonte,
      12, $corTexto, 'center');
    $this->page_y +=19;
  }


	function addTitulo()
	{

		$fonte    = 'arial';
		$corTexto = '#000000';

		$y_quadrado = 120;
		$y_escrita  = 125;

		$altura_caixa = 20;

		$this->pdf->quadrado_relativo( 30, $y_quadrado, 782, $altura_caixa );

		$this->pdf->quadrado_relativo( 30, $y_quadrado, 40, $altura_caixa );

		$this->pdf->quadrado_relativo( 70, $y_quadrado, 50, $altura_caixa );

		$this->pdf->quadrado_relativo( 115, $y_quadrado, 175, $altura_caixa );

		$this->pdf->quadrado_relativo( 285, $y_quadrado, 55, $altura_caixa );

		$this->pdf->quadrado_relativo( 335, $y_quadrado, 190, $altura_caixa );

		$this->pdf->quadrado_relativo( 520, $y_quadrado, 160, $altura_caixa );

		$this->pdf->quadrado_relativo( 680, $y_quadrado, 80, $altura_caixa );


		$this->pdf->escreve_relativo( "Cód.", 30, $y_escrita, 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Matrícula", 70, $y_escrita, 50, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Nome / Endereço", 115, $y_escrita, 175, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Nasc./\nIdade", 285, $y_escrita - 5, 50, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Filiação", 335, $y_escrita , 175, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		if(!$this->campo_assinatura)
			$this->pdf->escreve_relativo( "Nacionalidade /\nNaturalidade", 520, $y_escrita - 5 , 165, $altura_caixa, $fonte, 8, $corTexto, 'center' );
		else
			$this->pdf->escreve_relativo( "Assinatura Responsável", 520, $y_escrita  , 165, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Série / Turma", 680, $y_escrita , 80, $altura_caixa, $fonte, 8, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Turno", 760, $y_escrita , 50, $altura_caixa, $fonte, 8, $corTexto, 'center' );

	}

	function desenhaLinhasVertical()
	{
		$corTexto = '#000000';

		$this->total = 10;
		$largura_anos = 380;

		if($this->total >= 1)
		{

			$incremental = floor($largura_anos/ ($this->total )) ;

		}else {

			$incremental = 1;
		}

		$reta_ano_x = 200 ;


		$resto = $largura_anos - ($incremental * $this->total);

		for($linha = 0;$linha <$this->total;$linha++)
		{

			if(( $resto > 0) || $linha == 0)
			{
				$reta_ano_x++;
				$resto--;
			}

			$this->pdf->linha_relativa($reta_ano_x,139,0,$this->page_y - 139);


			$reta_ano_x += $incremental;

		}

		$this->pdf->linha_relativa(50,139,0,$this->page_y - 139);
		$this->pdf->linha_relativa(812,125,0,$this->page_y - 139);


		$this->pdf->linha_relativa(570,125,0,$this->page_y - 139);

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