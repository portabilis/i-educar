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
 * Relatório de frequência do aluno.
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Atestado de Freqüência" );
		$this->processoAp = "578";
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
	var $endereco;

	var $page_y = 139;


	var $get_link;



	var $meses_do_ano = array(
							 "1" => "Janeiro"
							,"2" => "Fevereiro"
							,"3" => "Março"
							,"4" => "Abril"
							,"5" => "Maio"
							,"6" => "Junho"
							,"7" => "Julho"
							,"8" => "Agosto"
							,"9" => "Setembro"
							,"10" => "Outubro"
							,"11" => "Novembro"
							,"12" => "Dezembro"
						);


	function renderHTML()
	{

		$ok = false;
		if(is_numeric($_GET['cod_matricula']))
		{
			$this->ref_cod_matricula = $_GET['cod_matricula'];
			$obj_mat = new clsPmieducarMatricula($this->ref_cod_matricula);
			$det_matricula = $obj_mat->detalhe();
			$this->nm_aluno = $det_matricula['nome_upper'];

			if($det_matricula['aprovado'] == 4)
			{

				$ok = true;

			}

			$obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
			$lst_transferencia = $obj_transferencia->lista( null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,1,null,null,$det_matricula['ref_cod_aluno'],false);
			// verifica se existe uma solicitacao de transferencia INTERNA
			if(is_array($lst_transferencia))
				$ok = true;
		}
		if(!$ok)
		{
			echo "<script>alert('Não é possível gerar atestado de freqüência para esta matrícula');window.location='educar_index.php';</script>";
			die('Não é possível gerar atestado de freqüência para esta matrícula');
		}

		$obj_curso = new clsPmieducarCurso($det_matricula['ref_cod_curso']);
		$det_curso = $obj_curso->detalhe();

		$obj_serie = new clsPmieducarSerie($det_matricula['ref_ref_cod_serie']);
		$det_serie = $obj_serie->detalhe();
		$this->nm_serie = $det_serie['nm_serie'];

		$obj_instituicao = new clsPmieducarInstituicao($det_curso['ref_cod_instituicao']);
		$det_instituicao = $obj_instituicao->detalhe();
		$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		$obj_escola = new clsPmieducarEscola($det_matricula['ref_ref_cod_escola']);
		$det_escola = $obj_escola->detalhe();
		$this->nm_escola = $det_escola['nome'];
		$this->ref_cod_escola = $det_escola['cod_escola'];

		$obj_nivel_ensino = new clsPmieducarNivelEnsino($det_curso['ref_cod_nivel_ensino']);
		$det_nivel_ensino = $obj_nivel_ensino->detalhe();

		$this->nm_ensino = $det_nivel_ensino['nm_nivel'];


		$fonte = 'arial';
		$corTexto = '#000000';

		$this->pdf = new clsPDF("Atestado de Frequência - {$this->ano}", "Atestado de Frequência", "A4", "", false, false);

		$this->pdf->OpenPage();
		$obj_escola_complemento = new clsPmieducarEscolaComplemento( $this->ref_cod_escola );
		$det_escola_complemento = $obj_escola_complemento->detalhe();
		if( $det_escola_complemento )
		{
			// NOME DA ESCOLA
			$nm_escola = str2upper($det_escola_complemento['nm_escola']);
			// ENDERECO DA ESCOLA
			$logradouro = str2upper($det_escola_complemento['logradouro']);
			$numero = $det_escola_complemento['numero'];
			$complemento = str2upper($det_escola_complemento['complemento']);

			$bairro = str2upper($det_escola_complemento['bairro']);
			$municipio = str2upper($det_escola_complemento['municipio']);
			$cep = $det_escola_complemento['cep'];
			$cep = int2CEP($cep);

			$this->endereco = "{$logradouro} {$complemento},{$numero} CEP {$cep} {$municipio}";

		}
		else
		{
			$obj_escola = new clsPmieducarEscola( $this->ref_cod_escola );
			$det_escola = $obj_escola->detalhe();

			$obj_juridica = new clsJuridica( $det_escola['ref_idpes'] );
			$det_juridica = $obj_juridica->detalhe();

			$nm_escola = $det_juridica['fantasia'];

			if( !$nm_escola )
			{
				if($det_escola['ref_idpes'])
				{
					$obj_pessoa_ = new clsPessoa_( $det_escola['ref_idpes'] );
					$det_pessoa_ = $obj_pessoa_->detalhe();

					$nm_escola = $det_pessoa_['nome'];
				}
			}
			$this->nm_escola = $nm_escola;

			$obj_endereco = new clsPessoaEndereco($det_escola["ref_idpes"]);
			if ( $det_escola["ref_idpes"] )
			{
				$tipo = 1;
				$endereco_lst = $obj_endereco->lista($det_escola["ref_idpes"]);

				if ( $endereco_lst )
				{
					foreach ($endereco_lst as $endereco)
					{
						$cep = $endereco["cep"]->cep;
						$idlog = $endereco["idlog"]->idlog;
						$obj = new clsLogradouro($idlog);
						$obj_det = $obj->detalhe();
						$logradouro = $obj_det["nome"];
						$idtlog = $obj_det["idtlog"]->detalhe();
						$tipo_logradouro = strtoupper($idtlog["descricao"]);
						$bairro = $idbai = $endereco["idbai"]->detalhe();
						$idbai = $idbai['nome'];
						$numero = $endereco["numero"];
						$complemento = $endereco["complemento"];
						$andar = $endereco["andar"];
					}
					$obj_log = new clsLogradouro($idlog );
					$obj_log_det = $obj_log->detalhe();
					if($obj_log_det)
					{
						$logradouro = str2upper($obj_log_det["nome"]);

						$obj_mun = new clsMunicipio( $obj_log_det["idmun"]);
						$det_mun = $obj_mun->detalhe();

						if($det_mun)
						{
							$municipio = str2upper($det_mun["nome"]);
						}
						$estado = $det_mun['sigla_uf']->sigla_uf;

					}

					$cep = int2CEP($cep);
					$this->endereco = "{$tipo_logradouro} {$logradouro} {$complemento},{$numero} CEP {$cep} {$municipio} {$estado}";

				}
				else if ( class_exists( "clsEnderecoExterno" ) )
				{
					$tipo = 2;
					$obj_endereco = new clsEnderecoExterno();
					$endereco_lst = $obj_endereco->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $det_escola["ref_idpes"]);
					if ( $endereco_lst )
					{
						foreach ($endereco_lst as $endereco)
						{

							$cep = $endereco["cep"];
							$estado =  $endereco["sigla_uf"]->sigla_uf;
							$sigla_uf = $endereco["sigla_uf"]->detalhe();
							$sigla_uf = $sigla_uf["nome"];
							$cidade = $endereco["cidade"];
							$idtlog = $endereco["idtlog"]->detalhe();
							$tipo_logradouro = $idtlog["descricao"];
							$logradouro = $endereco["logradouro"];
							$bairro = $endereco["bairro"];
							$numero = $endereco["numero"];
							$complemento = $endereco["complemento"];
							$andar = $endereco["andar"];
							$municipio = str2upper($endereco['cidade']);
							$bairro = str2upper($endereco_lst['bairro']);
						}
					}
					$cep = int2CEP($cep);
					$this->endereco = "{$tipo_logradouro} {$logradouro} {$complemento},{$numero}{$bairro} CEP {$cep} {$municipio} - {$sigla_uf}";
				}
			}
		}

		$this->addCabecalho();


		//titulo
		$this->pdf->escreve_relativo( "Atestado de Freqüência", 30, 220, 535, 80, $fonte, 16, $corTexto, 'center' );

		$texto = "Atesto para os devidos fins que o aluno {$this->nm_aluno}, código de aluno nº {$det_matricula['ref_cod_aluno']}, matriculado regularmente no {$this->nm_ensino}, frequentou a {$this->nm_serie} até a presente data.";
		$this->pdf->escreve_relativo( $texto, 30, 350, 535, 80, $fonte, 14, $corTexto, 'justify' );
		$mes = date('n');
		$mes = strtolower($this->meses_do_ano["{$mes}"]);
		$data = date('d')." de $mes de ".date('Y');
		$this->pdf->escreve_relativo( "Brasilia, $data", 30, 600, 535, 80, $fonte, 14, $corTexto, 'center' );
		$this->rodape();
		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();
		//echo "<script>window.location='$this->get_link';</script>";
		//header("location:download.php?filename=".$this->get_link);
		//echo "location:download.php?filename=".$this->get_link;die;
		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<center><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";




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
    $altura = 30;
    $fonte = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $altura, 535, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, "i-Educar");
    $this->pdf->escreve_relativo($titulo, 30, 45, 535, 80,
      $fonte, 18, $corTexto, 'center');
    $this->pdf->escreve_relativo("Secretaria Municipal da Educação", 30, 65,
      535, 80, $fonte, 12, $corTexto, 'center');

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie,etapa_curso');
    $lista_serie_curso = $obj->lista(NULL, NULL, NULL, $this->ref_cod_curso,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    // Dados escola
    $this->pdf->escreve_relativo( "Escola: {$this->nm_escola}\n$this->endereco",
      120, 85, 300, 80, $fonte, 10, $corTexto, 'left' );

    $dataAtual = date("d/m/Y");
    $this->pdf->escreve_relativo("Data: ".$dataAtual, 480, 100, 535, 80, $fonte,
      10, $corTexto, 'left' );
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
