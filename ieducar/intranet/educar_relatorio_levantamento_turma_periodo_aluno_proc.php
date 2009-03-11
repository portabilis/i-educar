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
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - i-Educar - Levantamento Turma por Período e Aluno" );
		$this->processoAp = "933";
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
	var $ref_cod_curso;
	var $ano;
	var $mes;

	var $pdf;

	var $page_y = 125;

	var $cursos = array();

	var $get_link;

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
	
	var $lst_escola = array();
	var $lst_cursos = array();
	
	var $total_geral_localizacao = array();
	
	var $escola_sem_avaliacao;
	
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
		$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
		$det_instituicao = $obj_instituicao->detalhe();
		$this->nm_instituicao = $det_instituicao["nm_instituicao"];
		
		$obj_curso2 = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso2 = $obj_curso2->detalhe();
		$this->nm_curso = $det_curso2["nm_curso"];
		
		if ($this->escola_sem_avaliacao == 1) {
			$this->escola_sem_avaliacao = true;
		} elseif ($this->escola_sem_avaliacao == 2) {
			$this->escola_sem_avaliacao = false;
		} else {
			$this->escola_sem_avaliacao = null;
		}
		
		$obj_escolas = new clsPmieducarEscola();
		$obj_escolas->setOrderby("ref_cod_escola_localizacao, nome");
		$this->lst_escola = $obj_escolas->lista($this->ref_cod_escola, null, null, $this->ref_cod_instituicao, null, null,null, null, null, null, 1, null, $this->escola_sem_avaliacao);

		$lst_curso = array();
		if (is_numeric($this->ref_cod_escola))
		{
			$obj_escola_curso = new clsPmieducarEscolaCurso();
			$lst_escola_curso = $obj_escola_curso->lista($this->ref_cod_escola, $this->ref_cod_curso, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao, true);
			foreach ($lst_escola_curso as $escola_curso) {
				$obj_curso = new clsPmieducarCurso($escola_curso["ref_cod_curso"]);
				$lst_curso[] = $obj_curso->detalhe();
			}
		}
		else
		{			
			if (is_numeric($this->ref_cod_curso)) {
				$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
				$lst_curso[] = $obj_curso->detalhe();
			} else {
				$obj_curso = new clsPmieducarCurso();
				$lst_curso = $obj_curso->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, 
													null, null, null, null, null, null, null, null, null, 1, null, 
													$this->ref_cod_instituicao);
			}
		}
		$this->pdf = new clsPDF("Levantamento Turma por Período e Aluno - {$this->ano}", "Levantamento Turma por Período e Aluno - {$this->ano}", "A4", "", false, false);

		$this->pdf->largura  = 842.0;
  		$this->pdf->altura = 595.0;

		$fonte = 'arial';
		$corTexto = '#000000';

		$altura_linha = 23;
		$inicio_escrita_y = 175;

		$this->pdf->OpenPage();
		$this->addCabecalho();
		foreach ($lst_curso as $curso) {
			$this->escreveEscolas($curso);
		}
		
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
	
	function escreveEscolas($curso) 
	{
		$fonte = 'arial';
		$corTexto = '#000000';
		$esquerda = 30;
		$direita  = 782;
		$altura = 20;
		$espaco = 150.4;
		$this->novoCabecalho($curso);
		$ref_cod_escola_localizacao = 0;
		$primeiro_loop = true;
		$totais = array();
		$index = 0;
		$total_geral_curso = array();
		foreach ($this->lst_escola as $escola) {
			$obj_escola_curso = new clsPmieducarEscolaCurso($escola["cod_escola"], $curso["cod_curso"]);
			$det_escola_curso = $obj_escola_curso->detalhe();
			$index = 0;
			if (is_array($det_escola_curso)) {
				if ($ref_cod_escola_localizacao != $escola["ref_cod_escola_localizacao"] && !$primeiro_loop)
				{
					$this->escreveTotal($totais, $total_geral_curso, $ref_cod_escola_localizacao);
					$ref_cod_escola_localizacao = $escola["ref_cod_escola_localizacao"];
					if ($this->page_y > $this->pdf->altura - 50) {
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->addCabecalho();
						$this->novoCabecalho($curso);
						$fonte = 'arial';
						$corTexto = '#000000';
						$esquerda = 30;
						$direita  = 782;
						$altura = 20;
						$espaco = 150.4;
						$primeiro_loop = true;						
					}
				}
				if ($primeiro_loop)
				{
					$ref_cod_escola_localizacao = $escola["ref_cod_escola_localizacao"];
					$primeiro_loop = false;
				}
				
				$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
				$this->pdf->escreve_relativo($this->substituiNomeEscola($escola["nome"]), $esquerda, $this->page_y, $espaco - 30, 100, $fonte, 8, $corTexto, 'center');
				$obj_serie = new clsPmieducarSerie();
				$obj_serie->setOrderby("nm_serie");				
				$lst_serie = $obj_serie->lista(null, null, null, $curso["cod_curso"], null, null, null, null, null, null, null, null, 1);
				$esquerda = $espaco;
				foreach ($lst_serie as $serie) {
					$horarios = array(0 => "6:00:00", 1 => "12:55:00", 2 => "17:55:00");//, 3 => "TOTAL");
					$espaco = 150.4;
					$qtd_series = 0;
					$qtd_series_relatorio = 4;
					$esquerda_aux = 0;
					$qtd_total_turmas = 0;
					$qtd_total_alunos = 0;
					for ($i = 0; $i < 4; $i++) {
						$obj_turma = new clsPmieducarTurma();
						$lst_turma = $obj_turma->lista(null, null, null, $serie["cod_serie"], $escola["cod_escola"], null, null,
											null, null, null, null, null, null, null, 1, null, $horarios[$i], $horarios[$i+1]);
						$qtd_turma = $obj_turma->_total;
						$qtd_alunos = 0; 
						if (is_array($lst_turma) && count($lst_turma)) {
							foreach ($lst_turma as $turma) {
								$obj_matricula = new clsPmieducarMatricula();
								$lst_matricula = $obj_matricula->lista(null, null, $escola["cod_escola"], $serie["cod_serie"], null, null, null,
													array(1,2,3), null, null, null, null, 1, $this->ano, $curso["cod_curso"], $this->ref_cod_instituicao,
													null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $turma["cod_turma"]);
								$qtd_alunos += $obj_matricula->_total;
							}							
						}
						if ($i > 2) {
							$qtd_turma  = $qtd_total_turmas;
							$qtd_alunos = $qtd_total_alunos;
							$totais[$index++] += $qtd_turma;
							$totais[$index++] += $qtd_alunos;
							$total_geral_serie_escola["turma"] += $qtd_total_turmas;
							$total_geral_serie_escola["aluno"] += $qtd_total_alunos;
							$faz_quadrado = false;
						} else {
							$qtd_total_turmas += $qtd_turma;
							$qtd_total_alunos += $qtd_alunos;
							$totais[$index++] += $qtd_turma;
							$totais[$index++] += $qtd_alunos;
							if (!$qtd_turma) {
								$qtd_turma = "";
							}
							if (!$qtd_alunos) {
								$qtd_alunos = "";
							}
							$faz_quadrado = true;
						}
						$this->pdf->linha_relativa($esquerda + $esquerda_aux, $this->page_y, 0, $altura);
						if (!empty($qtd_turma) || !$faz_quadrado) {
							$this->pdf->escreve_relativo($qtd_turma, $esquerda + $esquerda_aux, $this->page_y + 4, $espaco / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
						} else {
							$this->pdf->quadrado_relativo(  $esquerda + $esquerda_aux + 1, $this->page_y + 1, $espaco / $qtd_series_relatorio / 2 - 1, $altura - 1,0.5,"#A1B3BD","");
						}
						$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y, 0, $altura);
						if (!empty($qtd_alunos) || !$faz_quadrado) {
							$this->pdf->escreve_relativo($qtd_alunos, $esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y + 4, $espaco / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
						} else {
							$this->pdf->quadrado_relativo($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2 + 1, $this->page_y + 1, $espaco / $qtd_series_relatorio / 2 - 1, $altura - 1,0.5,"#A1B3BD","");
						}
						
						$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio, $this->page_y, 0, $altura);
						
						$esquerda_aux += $espaco / $qtd_series_relatorio;
					}
					$esquerda += $espaco;
				}
				$aux = 91;
				$total_geral_curso["turma"] += $total_geral_serie_escola["turma"];
				$total_geral_curso["aluno"] += $total_geral_serie_escola["aluno"];
				$this->pdf->escreve_relativo($total_geral_serie_escola["turma"], $esquerda, $this->page_y + 4, ($espaco + $aux) / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
				$this->pdf->linha_relativa($esquerda + ($espaco - $aux) / 2, $this->page_y, 0, $altura);
				$this->pdf->escreve_relativo($total_geral_serie_escola["aluno"], $esquerda + ($espaco - $aux) / 2, $this->page_y + 4, ($espaco + $aux) / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
				$this->pdf->linha_relativa($esquerda + $espaco - $aux, $this->page_y, 0, $altura);
				$total_geral_serie_escola = array();
				$this->page_y += 20;
				$esquerda = 30;
			}
			if ($this->page_y > $this->pdf->altura - 50) {
				$this->pdf->ClosePage();
				$this->pdf->OpenPage();
				$this->addCabecalho();
				$this->novoCabecalho($curso);
				$fonte = 'arial';
				$corTexto = '#000000';
				$esquerda = 30;
				$direita  = 782;
				$altura = 20;
				$espaco = 150.4;
				$primeiro_loop = true;
			}
		}
		$this->escreveTotal($totais, $total_geral_curso, $ref_cod_escola_localizacao);
		$this->escreveTotalGeral();
	}
	
	function substituiNomeEscola($nome_escola) {
		$nome_escola = str_ireplace("Centro", "C.", $nome_escola);
		$nome_escola = str_ireplace("Educacional", "Edu.", $nome_escola);
		$nome_escola = str_ireplace("Escola", "E.", $nome_escola);
		$nome_escola = str_ireplace("Básica", "B.", $nome_escola);
		$nome_escola = str_ireplace("Grupo", "G.", $nome_escola);
		return str_ireplace("Escolar", "Esc.", $nome_escola);
	}
	
	function escreveTotalGeral() 
	{
		if ($this->page_y > $this->pdf->altura - 50) {
			$this->pdf->ClosePage();
			$this->pdf->OpenPage();
			$this->addCabecalho();
			$this->novoCabecalho($curso);
			$fonte = 'arial';
			$corTexto = '#000000';
			$esquerda = 30;
			$direita  = 782;
			$altura = 20;
			$espaco = 150.4;
			$primeiro_loop = true;
		}
		$fonte = 'arial';
		$corTexto = '#000000';
		$esquerda = 30;
		$direita  = 782;
		$altura = 20;
		$espaco = 150.4;
		$qtd_series = 0;
		$qtd_series_relatorio = 4;
		$esquerda_aux = 0;
		$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
		$this->pdf->escreve_relativo("Total Geral", $esquerda + $esquerda_aux + $aux, $this->page_y + 5, 150, 100, $fonte, 8, $corTexto/*, 'center'*/);
		$esquerda = $espaco;
		$this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);
		$tam_total_geral_localizacao = count($this->total_geral_localizacao);
		for ($i = 0; $i < $tam_total_geral_localizacao - 2; $i++) {
			$this->pdf->escreve_relativo($this->total_geral_localizacao[$i], $esquerda + $esquerda_aux, $this->page_y + 5, $espaco / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y, 0, $altura);
			$this->pdf->escreve_relativo($this->total_geral_localizacao[++$i], $esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y + 5,  $espaco / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio, $this->page_y, 0, $altura);
			
			$esquerda_aux += $espaco / $qtd_series_relatorio;
		}
		$esquerda = $esquerda_aux + $espaco;
		$aux = 91;
		$this->pdf->escreve_relativo($this->total_geral_localizacao[$i++], $esquerda, $this->page_y + 4, ($espaco + $aux) / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda + ($espaco - $aux) / 2, $this->page_y, 0, $altura);
		$this->pdf->escreve_relativo($this->total_geral_localizacao[$i], $esquerda + ($espaco - $aux) / 2, $this->page_y + 4, ($espaco + $aux) / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda + $espaco - $aux, $this->page_y, 0, $altura);

		$this->page_y += 20;	
	}
	
	function escreveTotal(&$totais, &$total_geral_curso, $ref_cod_localizacao) {
		$obj_localizacao = new clsPmieducarEscolaLocalizacao($ref_cod_localizacao);
		$det_localizacao = $obj_localizacao->detalhe();
		$fonte = 'arial';
		$corTexto = '#000000';
		$esquerda = 30;
		$direita  = 782;
		$altura = 20;
		$espaco = 150.4;
		$qtd_series = 0;
		$qtd_series_relatorio = 4;
		$esquerda_aux = 0;
		$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
		$this->pdf->escreve_relativo("Total - {$det_localizacao["nm_localizacao"]}", $esquerda + $esquerda_aux + $aux, $this->page_y + 5, 150, 100, $fonte, 8, $corTexto/*, 'center'*/);
		$esquerda = $espaco;
		$this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);
		$tam_totais = count($totais);
		for ($i = 0; $i < $tam_totais-1; $i++) {
			$this->total_geral_localizacao[$i] += $totais[$i];
			$this->pdf->escreve_relativo($totais[$i], $esquerda + $esquerda_aux, $this->page_y + 4, $espaco / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y, 0, $altura);
			$this->total_geral_localizacao[++$i] += $totais[$i];
			$this->pdf->escreve_relativo($totais[$i], $esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y + 4,  $espaco / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio, $this->page_y, 0, $altura);
			
			$esquerda_aux += $espaco / $qtd_series_relatorio;
		}
		$esquerda = $esquerda_aux + $espaco;
		$aux = 91;
		$this->total_geral_localizacao[$i++] += $total_geral_curso["turma"];
		$this->total_geral_localizacao[$i]   += $total_geral_curso["aluno"];
		$this->pdf->escreve_relativo($total_geral_curso["turma"], $esquerda, $this->page_y + 4, ($espaco + $aux) / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda + ($espaco - $aux) / 2, $this->page_y, 0, $altura);
		$this->pdf->escreve_relativo($total_geral_curso["aluno"], $esquerda + ($espaco - $aux) / 2, $this->page_y + 4, ($espaco + $aux) / $qtd_series_relatorio / 2, 100, $fonte, 8, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda + $espaco - $aux, $this->page_y, 0, $altura);

		$totais = array();
		$total_geral_curso = array();
		$this->page_y += 20;		
	}
	
	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		$esquerda = 15;
		$direita  = 782;
		
		$this->page_y = 125;

		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 782, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 782, 80, $fonte, 18, $corTexto, 'center' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição: $this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Curso: {$this->nm_curso}",136, 70, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 25, 30, 782, 80, $fonte, 10, $corTexto, 'right' );

		//titulo
		$this->pdf->escreve_relativo( "Levantamento Turmas por Período e Alunos - {$this->ano} ", 30, 85, 782, 80, $fonte, 12, $corTexto, 'center' );

	}

	function novoCabecalho($curso)
	{
		if (is_array($curso))
		{
				$obj_serie = new clsPmieducarSerie();
				$obj_serie->setOrderby("nm_serie");
				$lst_serie = $obj_serie->lista(null, null, null, $curso["cod_curso"], null, null, null, null, null, null, null, null, 1);
				if (is_array($lst_serie) && count($lst_serie))
				{
					$periodos = array(0 => "MAT", 1 => "VESP", 2 => "NOT", 3 => "TOTAL");
					$turmas_aluno = array(
										0 => array("turma" => eregi_replace("([^\n\r\t])","\n\\1", "Nº TURMAS"),   "aluno" => eregi_replace("([^\n\r\t])","\n\\1", "Nº ALUNOS")),
										1 => array("turma" => eregi_replace("([^\n\r\t])","\n\\1", "Nº TURMAS"),   "aluno" => eregi_replace("([^\n\r\t])","\n\\1", "Nº ALUNOS")),
										2 => array("turma" => eregi_replace("([^\n\r\t])","\n\\1", "Nº TURMAS"),   "aluno" => eregi_replace("([^\n\r\t])","\n\\1", "Nº ALUNOS")),
										3 => array("turma" => eregi_replace("([^\n\r\t])","\n\\1", "TOT TURMAS"),  "aluno" => eregi_replace("([^\n\r\t])","\n\\1", "TOT ALUNOS"))
										);
					$fonte = 'arial';
					$corTexto = '#000000';
					$esquerda = 30;
					$direita  = 782;
					$this->page_y = 125;
					$altura = 90;
					$espaco = 150.4;
					$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
					$esquerda -= 30;
					$this->pdf->linha_relativa($esquerda += $espaco, $this->page_y, 0, $altura);
					$qtd_series = 0;
					$qtd_series_relatorio = 4;
					foreach ($lst_serie as $serie) {
						$this->pdf->quadrado_relativo($esquerda, $this->page_y, $espaco, $altura);
						$this->pdf->escreve_relativo($serie["nm_serie"], $esquerda, $this->page_y + 1, $espaco, 100, $fonte, 8, $corTexto, 'center');
						$this->pdf->linha_relativa($esquerda, $this->page_y + 10, $espaco, 0);
						$this->pdf->linha_relativa($esquerda + $espaco / $qtd_series_relatorio, $this->page_y + 22, 0, $altura - 22);
						$esquerda_aux = 0;
						for ($i = 0; $i < 4; $i++) {
							$this->pdf->escreve_relativo($serie["nm_serie"], $esquerda, $this->page_y + 1, $espaco, 100, $fonte, 8, $corTexto, 'center');
							$this->pdf->escreve_relativo($periodos[$i], $esquerda + $esquerda_aux, $this->page_y + 12, $espaco / $qtd_series_relatorio, 100, $fonte, 8, $corTexto, 'center');
							$this->pdf->linha_relativa($esquerda + $esquerda_aux, $this->page_y + 22, $espaco / $qtd_series_relatorio, 0);
														
							$this->pdf->escreve_relativo($turmas_aluno[$i]["turma"], $esquerda + $esquerda_aux + 5, $this->page_y + 20, 50, 100, $fonte, 6, $corTexto);//, 'center');
							$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio / 2, $this->page_y + 22, 0, $altura - 22);
							$this->pdf->escreve_relativo($turmas_aluno[$i]["aluno"], $esquerda + $esquerda_aux + 25, $this->page_y + 20, 50, 100, $fonte, 6, $corTexto);//, 'center');
							
							$this->pdf->linha_relativa($esquerda + $esquerda_aux + $espaco / $qtd_series_relatorio, $this->page_y + 10, 0, $altura - 10);
							$esquerda_aux += $espaco / $qtd_series_relatorio;
						}
						/*$qtd_series++;
						if ($qtd_series > $qtd_series_relatorio) {
//							$this->novoCabecalho1($esquerda, $direita, $qtd_series, $espaco, $altura);
//							$esquerda_aux = 0;
						}	*/			
						$esquerda += $espaco;				
					}
					$aux = 91;
					$this->pdf->quadrado_relativo($esquerda, $this->page_y, $espaco  - $aux, $altura);
					$this->pdf->escreve_relativo("TOTAL", $esquerda, $this->page_y + 5, $espaco - $aux, 100, $fonte, 10, $corTexto, 'center');
					$this->pdf->linha_relativa($esquerda, $this->page_y + 22, $espaco - $aux, 0);
					
					$this->pdf->escreve_relativo(eregi_replace("([^\n\r\t])","\n\\1", "TOT TURMAS"), $esquerda, $this->page_y + 20, ($espaco - $aux) / 2, 100, $fonte, 6, $corTexto, 'center');
					$this->pdf->escreve_relativo(eregi_replace("([^\n\r\t])","\n\\1", "TOT ALUNOS"), $esquerda + ($espaco - $aux) / 2, $this->page_y + 20, ($espaco - $aux) / 2, 100, $fonte, 6, $corTexto, 'center');
					
					$this->pdf->linha_relativa($esquerda + ($espaco - $aux) / 2, $this->page_y + 22, 0, $altura - 22);
//					/*if (++$qtd_series > 4) {
//						$this->novoCabecalho1($esquerda, $direita, $qtd_series, $espaco, $altura);
//					}*/
				}
		}
		$this->page_y += $altura;
	}
	
	function novoCabecalho1(&$esquerda, &$direita, &$qtd_series, $espaco, $altura) {
		$esquerda 	= 30;
		$direita    = 782;
		$qtd_series = 0;
		$this->page_y = 125;
		$this->pdf->ClosePage();
		$this->pdf->OpenPage();
		$this->addCabecalho();
		$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
		$this->pdf->linha_relativa($esquerda += $espaco + 30, $this->page_y, 0, $altura);
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
