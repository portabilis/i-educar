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
 * Reserva de vaga.
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Diário de Classe - Avalia&ccedil;&otilde;es" );
		$this->processoAp = "670";
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
	var $ref_cod_matricula;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $pdf;
	var $nm_turma;
	var $nm_serie;
	var $nm_aluno;
	var $nm_ensino;
	var $nm_curso;
	var $data_solicitacao;

	var $page_y = 139;


	var $get_link;



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


	function renderHTML()
	{

		$ok = false;
		$obj_reserva_vaga = new clsPmieducarReservaVaga();
		$lst_reserva_vaga = $obj_reserva_vaga->lista($this->cod_reserva_vaga);
		$registro = array_shift($lst_reserva_vaga);
		if(is_numeric($_GET['cod_reserva_vaga']) && is_array($registro))
		{
			$this->data_solicitacao = $registro['data_cadastro'];

			$ok = true;
		}
		if(!$ok)
		{
			echo "<script>alert('Não é possível gerar documento para reserva de vaga para esta matrícula');window.location='educar_index.php';</script>";
			die('Não é possível gerar documento para reserva de vaga para esta matrícula');
		}

		$obj_aluno = new clsPmieducarAluno();
		$det_aluno = array_shift($obj_aluno->lista($registro['ref_cod_aluno']));
		$this->nm_aluno = $det_aluno['nome_aluno'];


		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $registro["ref_ref_cod_escola"] = $det_escola["nome"];
		}
		else
		{
			$registro["ref_ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_serie = $obj_serie->detalhe();
			$this->nm_serie = $registro["ref_ref_cod_serie"] = $det_serie["nm_serie"];
		}
		else
		{
			$registro["ref_ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
		}
		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_curso = $obj_curso->detalhe();
			$this->nm_curso = $registro["ref_cod_curso"] = $det_curso["nm_curso"];
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarCurso\n-->";
		}

		$fonte = 'arial';
		$corTexto = '#000000';

		$this->pdf = new clsPDF("Diário de Classe - {$this->ano}", "Diário de Classe - {$this->meses_do_ano[$this->mes]} e {$this->meses_do_ano[$prox_mes]} de {$this->ano}", "A4", "", false, false);

		$this->pdf->OpenPage();
		$this->addCabecalho();


		//titulo
		$this->pdf->escreve_relativo( "Reserva de Vaga", 30, 220, 535, 80, $fonte, 16, $corTexto, 'justify' );

		$texto = "Atesto para os devidos fins que o aluno {$this->nm_aluno}, solicitou reserva de vaga na escola {$this->nm_escola}, para o curso {$this->nm_curso}, na série {$this->nm_serie} e que a mesma possui a validade de 48 horas a partir da data de solicitação da mesma, ".dataFromPgToBr($this->data_solicitacao).".";
		$this->pdf->escreve_relativo( $texto, 30, 350, 535, 80, $fonte, 14, $corTexto, 'center' );
		$mes = date('n');
		$mes = strtolower($this->meses_do_ano["{$mes}"]);
		$data = date('d')." de $mes de ".date('Y');
		$this->pdf->escreve_relativo( "Brasilia, $data", 30, 600, 535, 80, $fonte, 14, $corTexto, 'center' );
		$this->rodape();
		$this->pdf->CloseFile();

		$this->get_link = $this->pdf->GetLink();

		//$down = new download($this->get_link);
		//echo "<script>window.location='$this->get_link';setTimeout('window.close();',300);</script>";
		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<center><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";


		//echo "location:download.php?filename=".$this->get_link;die;

	return;


	}

	function Novo()
	{

		return true;
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

    $this->pdf->quadrado_relativo( 30, $altura, 535, 85 );
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 45, 535, 80, $fonte, 18,
      $corTexto, 'center');
    $this->pdf->escreve_relativo("Secretaria Municipal da Educação", 30, 65,
      535, 80, $fonte, 12, $corTexto, 'center');

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie,etapa_curso');
    $lista_serie_curso = $obj->lista(NULL, NULL, NULL, $this->ref_cod_curso,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    $dataAtual = date("d/m/Y");
    $this->pdf->escreve_relativo("Data: " . $dataAtual, 480, 100, 535, 80,
      $fonte, 10, $corTexto, 'left');
  }


	function rodape()
	{
		$corTexto = '#000000';


		$this->pdf->escreve_relativo( "Assinatura do secretário(a)", 398,715, 150, 50, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa(385,710,140,0);
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
