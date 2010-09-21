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
 * Ficha de rematrícula.
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Rela&ccedil;&atilde;o de alunos/nota bimestres" );
		$this->processoAp = "807";
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
	var $ref_cod_curso;
	var $ref_cod_matricula;

	var $ano;

	var $cursos = array();

	var $get_link;
	var $pdf;
	var $is_padrao;
	var $semestre;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}
		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		if ($this->is_padrao || $this->ano == 2007)
		{
			$this->semestre = null;
		}

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
		if (is_numeric($this->ref_cod_matricula))
		{
			$sql = "SELECT
						p.idpes
						,m.cod_matricula
						,p.nome
						,to_char(f.data_nasc, 'DD/MM/YYYY') as dt_nascimento
						,f.sexo
						,f.idmun_nascimento
						,a.ref_cod_religiao
					FROM
						pmieducar.matricula_turma mt
						, pmieducar.matricula 	  m
						, pmieducar.aluno 	      a
						, cadastro.pessoa	      p
						, cadastro.fisica         f
					WHERE
						m.cod_matricula = mt.ref_cod_matricula
						AND m.cod_matricula = {$this->ref_cod_matricula}
						AND m.ref_cod_aluno = a.cod_aluno
						AND a.ref_idpes	     = p.idpes
						AND aprovado IN (1,2,3)
						AND ano = {$this->ano}
						AND mt.ativo 	= 1
						AND m.ativo     = 1
						AND f.idpes = p.idpes
					ORDER BY
						to_ascii(p.nome) ASC";
		}
		else
		{
			if (is_numeric($this->semestre))
				$sql = " m.semestre = {$this->semestre} ";
			$sql = "SELECT
						p.idpes
						,m.cod_matricula
						,p.nome
						,to_char(f.data_nasc, 'DD/MM/YYYY') as dt_nascimento
						,f.sexo
						,f.idmun_nascimento
						,a.ref_cod_religiao
					FROM
						   pmieducar.matricula_turma mt
						 , pmieducar.matricula 	     m
						 , pmieducar.aluno 	         a
						 , cadastro.pessoa	         p
						 , cadastro.fisica           f
					WHERE
						{$sql}
						m.cod_matricula = mt.ref_cod_matricula
						AND mt.ref_cod_turma = {$this->ref_cod_turma}
						AND m.ref_cod_aluno = a.cod_aluno
						AND a.ref_idpes	     = p.idpes
						AND aprovado IN (1,2,3)
						AND ano = $this->ano
						AND mt.ativo 	= 1
						AND m.ativo     = 1
						AND f.idpes     = p.idpes
					ORDER BY
						to_ascii(p.nome) ASC";
		}
		$dados = array();
		$db = new clsBanco();
		$db->Consulta($sql);
		while ($db->ProximoRegistro())
		{
			$dados[] = $db->Tupla();
		}
		if (is_array($dados))
		{
			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];
			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

			$this->pdf = new clsPDF("Registro de Matrículas - {$this->ano}", "Registro de Matrículas", "A4", "", false, false);
			$this->pdf->OpenFile();
			$nova_pagina = false;
			foreach ($dados as $dado_aluno)
			{
				if ($nova_pagina)
					$this->pdf->OpenPage();

				$nova_pagina = true;

				$this->addCabecalho();

				$esquerda = 30;
				$direita  = 535;
				$altura   = 125;
				$this->pdf->quadrado_relativo( $esquerda, $altura, 535, $this->pdf->altura - 150 );


				$tam_titulo = 12;
				$tam_letra = 10;
				$espessura_linha = 0.5;

				list($idpes, $cod_matricula, $nome, $dt_nascimento, $sexo, $idmun_nascimento, $ref_cod_religiao) = $dado_aluno;

				$sql = "SELECT nm_raca FROM cadastro.fisica_raca, cadastro.raca WHERE ref_idpes = {$idpes} AND
						ref_cod_raca = cod_raca";
				$db2 = new clsBanco();
				$nm_raca = $db2->CampoUnico($sql);

				if (is_numeric($ref_cod_religiao))
				{
					$sql = "SELECT nm_religiao FROM pmieducar.religiao WHERE cod_religiao = {$ref_cod_religiao}";
					$religiao = $db2->CampoUnico($sql);
				}

				if (is_numeric($idmun_nascimento))
				{
					$sql = "SELECT nome, sigla_uf FROM public.municipio WHERE idmun = {$idmun_nascimento}";
					$db2->Consulta($sql);
					$db2->ProximoRegistro();
					list($naturalidade, $uf) = $db2->Tupla();
				}

				$obj_documento = new clsDocumento($idpes);
				$det_documento = $obj_documento->detalhe();
				if (is_array($det_documento))
				{
					$cert_civil = $det_documento["num_termo"];
					$livro = $det_documento["num_livro"];
					$folha = $det_documento["num_folha"];
					$cartorio = $det_documento["cartorio_cert_civil"];
					$dt_cartorio = $det_documento["data_emissao_cert_civil"] ? dataFromPgToBr($det_documento["data_emissao_cert_civil"]) : "";
					$rg = $det_documento["rg"];
					$dt_rg = $det_documento["data_exp_rg"] ? dataFromPgToBr($det_documento["data_exp_rg"]) : "";
				}

				$this->pdf->escreve_relativo("1. Dados Pessoais", $esquerda+5, $altura+5, 350, 100, $fonte, $tam_titulo);
				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);

				$this->pdf->escreve_relativo("Nome: {$nome}", $esquerda + 5, $altura+=5, 500, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Matrícula: {$cod_matricula}", $esquerda + 350, $altura+5, 350, 100, $fonte, $tam_letra);

				$this->pdf->escreve_relativo("Data de Nascimento: {$dt_nascimento}", $esquerda + 5, $altura += 25, 350, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Sexo: {$sexo}", $esquerda + 180, $altura, 200, 200, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Cor/Raça: {$nm_raca}", $esquerda + 235, $altura, 200, 200, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Religião: {$religiao}", $esquerda + 350, $altura, 200, 200, $fonte, $tam_letra);

				$this->pdf->escreve_relativo("Naturalidade: {$naturalidade}", $esquerda + 5, $altura+=25, 500, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Estado: {$uf}", $esquerda + 350, $altura+5, 350, 100, $fonte, $tam_letra);

				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);
				$this->pdf->escreve_relativo("2. Documentos", $esquerda + 5, $altura +=2, 500, 100, $fonte, $tam_titulo);
				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);

				$this->pdf->escreve_relativo("Certidão de Nascimento: {$cert_civil}", $esquerda + 5, $altura+=5, 500, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Livro: {$livro}", $esquerda + 275, $altura, 500, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Folha: {$folha}", $esquerda + 350, $altura, 500, 100, $fonte, $tam_letra);

				$this->pdf->escreve_relativo("Cartório: {$cartorio}", $esquerda + 5, $altura += 25, 350, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Data de Emissão: {$dt_cartorio}", $esquerda + 350, $altura, 200, 200, $fonte, $tam_letra);

				$this->pdf->escreve_relativo("Carteira de Identidade: {$rg}", $esquerda + 5, $altura += 25, 350, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Data de Expedição: {$dt_rg}", $esquerda + 350, $altura, 200, 200, $fonte, $tam_letra);

				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);
				$this->pdf->escreve_relativo("3. Dados de Matrícula", $esquerda + 5, $altura +=2, 500, 100, $fonte, $tam_titulo);
				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);

				$this->pdf->escreve_relativo("Ano: __________", $esquerda + 5, $altura +=5, 350, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Curso: __________________________________________________________________", $esquerda + 95, $altura, 500, 100, $fonte, $tam_letra);

				$this->pdf->escreve_relativo("Ano/Série: ______________", $esquerda + 5, $altura+=25, 350, 100, $fonte, $tam_letra);
				$this->pdf->escreve_relativo("Turno: __________________________________", $esquerda + 155, $altura, 350, 100, $fonte, $tam_letra);

				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);
				$this->pdf->escreve_relativo("4. Endereço Atual", $esquerda + 5, $altura +=2, 500, 100, $fonte, $tam_titulo);
				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);

				$texto = "Rua ____________________________________________________________________, nº ______________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura +=5, 600, 100, $fonte, $tam_letra);

				$texto = "Complmento: __________________________________     Bairro: _____________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$texto = "Município: __________________________     Estado: _______     CEP: ___________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$texto = "Telefone Residencial: ____________________________     Celular: ____________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);
				$this->pdf->escreve_relativo("5. Dados dos Pais ou Responsáveis", $esquerda + 5, $altura +=2, 500, 100, $fonte, $tam_titulo);
				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);

				$texto = "Nome do Pai/responsável ____________________________________________________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura +=5, 600, 100, $fonte, $tam_letra);

				$texto = "Profissão: ____________________________________________     CPF: _____________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$texto = "Local de Trabalho: ____________________________________________     Telefone: ___________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$texto = "Nome da Mãe/responsável ____________________________________________________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura +=25, 600, 100, $fonte, $tam_letra);

				$texto = "Profissão: ____________________________________________     CPF: _____________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$texto = "Local de Trabalho: ____________________________________________     Telefone: ___________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);
				$this->pdf->escreve_relativo("6. Observação", $esquerda + 5, $altura +=2, 500, 100, $fonte, $tam_titulo);
				$this->pdf->linha_relativa($esquerda, $altura += 20, 535, 0);

				$texto = "______________________________________________________________________________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura +=5, 600, 100, $fonte, $tam_letra);

				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=25, 600, 100, $fonte, $tam_letra);

				$this->pdf->escreve_relativo("Data: _____/_____/_____", $esquerda + 5, $altura+=18, 600, 100, $fonte, $tam_letra);

				$texto = "Assinatura dos Pais/Responsáveis: _______________________________________________________________";
				$this->pdf->escreve_relativo($texto, $esquerda + 5, $altura+=22, 600, 100, $fonte, $tam_letra);

				$this->pdf->ClosePage();

			}

			//quadrado principal



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
			echo "Não possui dados existentes";
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

    $this->pdf->quadrado_relativo(30, $altura, 535, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 535, 80, $fonte, 18,
      $corTexto, 'center');
    $this->pdf->escreve_relativo( date("d/m/Y"), 500, 30, 100, 80, $fonte, 12,
      $corTexto, 'left');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição: $this->nm_instituicao", 120, 58,
      300, 80, $fonte, 10, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola: {$this->nm_escola}",136, 70, 300, 80,
      $fonte, 10, $corTexto, 'left');

    // Título
    $this->pdf->escreve_relativo("Ficha de Rematrícula", 30, 85, 535, 80,
      $fonte, 14, $corTexto, 'center' );
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
