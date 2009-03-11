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
		$this->processoAp = "930";
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
	
	var $ref_ref_cod_serie;
	var $ref_cod_turma;

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

		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		}

		if (is_numeric($this->mes) && is_numeric($this->ano) && is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_cod_escola))
		{
			$sql = "";
			if (is_numeric($this->ref_cod_curso))
				$sql .= " AND m.ref_cod_curso = {$this->ref_cod_curso} ";
			if (is_numeric($this->ref_ref_cod_serie))
				$sql .= " AND m.ref_ref_cod_serie = {$this->ref_ref_cod_serie} ";
			if (is_numeric($this->ref_cod_turma))
				$sql .= " AND mt.ref_cod_turma = {$this->ref_cod_turma} ";

			$primeiroDiaDoMes = mktime(0,0,0,$this->mes,1,$this->ano);
			$NumeroDiasMes = date('t',$primeiroDiaDoMes);
			$ultimoDiaMes =date('d/m/Y',mktime(0,0,0,$this->mes,$NumeroDiasMes,$this->ano));
			$ultimoDiaMes = dataToBanco($ultimoDiaMes,false);

			$primeiroDiaDoMes = date('d/m/Y',$primeiroDiaDoMes);
			$primeiroDiaDoMes = dataToBanco($primeiroDiaDoMes,false);
//				echo $primeiroDiaDoMes."   ".$ultimoDiaMes;
			$sql = "SELECT 	m.cod_matricula, m.ref_cod_curso,
					   	m.ref_ref_cod_serie, mt.ref_cod_turma,
						(SELECT nome FROM cadastro.pessoa p
							WHERE a.cod_aluno = m.ref_cod_aluno AND a.ref_idpes = p.idpes) as nome,
						(SELECT to_char(data_nasc,'DD/MM/YYYY') FROM cadastro.fisica f
							WHERE a.cod_aluno = m.ref_cod_aluno AND a.ref_idpes = f.idpes) as dt_nasc,
						a.cod_aluno, a.analfabeto, s.nm_serie,c.nm_curso,t.nm_turma
						FROM pmieducar.matricula m, pmieducar.curso c, 
							pmieducar.matricula_turma mt, pmieducar.aluno a,
							pmieducar.serie s, pmieducar.turma t WHERE 
							m.ano = {$this->ano}
							AND s.cod_serie = m.ref_ref_cod_serie
							AND a.cod_aluno = m.ref_cod_aluno
							AND m.ref_ref_cod_escola = {$this->ref_cod_escola}
							AND m.ativo = 1
							AND m.ref_cod_curso = c.cod_curso
							AND c.ref_cod_instituicao = 1
							AND mt.ref_cod_matricula = m.cod_matricula
							AND mt.ativo = 1 
							AND mt.ref_cod_turma = t.cod_turma
							AND m.aprovado IN (1,2,3)
							AND (m.data_cadastro <= '{$ultimoDiaMes}' OR (
									a.data_exclusao >= '{$primeiroDiaDoMes}' AND 
									a.data_exclusao <= '{$ultimoDiaMes}')
								)
							{$sql}
						ORDER BY m.ref_cod_curso, m.ref_ref_cod_serie, mt.ref_cod_turma, nome";
			
//			AND (m.data_cadastro <= '2008-02-29 23:59:59' OR (
//a.data_exclusao >= '2008-02-01 23:59:59' AND a.data_exclusao <= '2008-02-29 23:59:59')
//)
//			echo $sql."<br>";
			$fonte = 'arial';
			$corTexto = '#000000';
			$db = new clsBanco();
			$db->Consulta($sql);
			$registros = array();
			while ($db->ProximoRegistro()) {
				$registros[] = $db->Tupla();
			}


			$altura_linha = 23;
			$inicio_escrita_y = 175;

			if (is_array($registros) && count($registros))
			{
				$this->pdf = new clsPDF("Levantamento Alfabetizados e Não Albetizados - {$this->ano}", "Levantamento Alfabetizados e Não Albetizados - {$this->ano}", "A4", "", false, false);
				$this->pdf->OpenPage();
				$this->addCabecalho();
				$this->novoCabecalho();
				$total_serie_analfabeto = 0;
				$total_serie_nao_analfabeto = 0;
				$total_curso_analfabeto = 0;
				$total_curso_nao_analfabeto = 0;
				$total_geral_analfabeto = 0;
				$total_geral_nao_analfabeto = 0;
				$total_turma_analfabeto = 0;
				$total_turma_nao_analfabeto = 0;
				$primeira_vez_loop = true;
				$ref_cod_serie = 0;
				$ref_cod_curso = 0;
				$ref_cod_turma = 0;
				$altura = 20;
				$fonte = 'arial';
				$corTexto = '#000000';
				$left = 33;
				$tam_letra = 8;
				foreach ($registros as $registro) {
					$left = 33;
					if ($ref_cod_turma != $registro["ref_cod_turma"] && !$primeira_vez_loop)
					{
						$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
						$total = $total_turma_analfabeto + $total_turma_nao_analfabeto;
						$this->pdf->escreve_relativo("Total Turma {$nm_turma}", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
						$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
						$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
						//analfabeto
						$this->pdf->escreve_relativo($total_turma_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
						//nao analfabeto
						$this->pdf->escreve_relativo($total_turma_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
						$left = 33 + 60 + 70 + 268;
						$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
						$left += 93;
						$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
						$left = 33;
						$this->page_y += $altura;
						$total_turma_analfabeto = $total_turma_nao_analfabeto = 0;
						if ($this->page_y > $this->pdf->altura - 50)
						{
							$this->pdf->ClosePage();
							$this->pdf->OpenPage();
							$this->addCabecalho();
							$this->novoCabecalho();
						}
					}
					if ($ref_cod_serie != $registro["ref_ref_cod_serie"] && !$primeira_vez_loop)
					{
						$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
						$total = $total_serie_analfabeto + $total_serie_nao_analfabeto;
						$this->pdf->escreve_relativo("Total Série {$nm_serie}", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
						$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
						$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
						//analfabeto
						$this->pdf->escreve_relativo($total_serie_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
						//nao analfabeto
						$this->pdf->escreve_relativo($total_serie_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
						$left = 33 + 60 + 70 + 268;
						$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
						$left += 93;
						$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
						$left = 33;
						$this->page_y += $altura;
						$total_serie_analfabeto = $total_serie_nao_analfabeto = 0;
						if ($this->page_y > $this->pdf->altura - 50)
						{
							$this->pdf->ClosePage();
							$this->pdf->OpenPage();
							$this->addCabecalho();
							$this->novoCabecalho();
						}
					}
					if ($ref_cod_curso != $registro["ref_cod_curso"] && !$primeira_vez_loop)
					{
						$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
						$total = $total_curso_analfabeto + $total_curso_nao_analfabeto;
						$this->pdf->escreve_relativo("Total Curso {$nm_curso}", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
						$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
						$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
						//analfabeto
						$this->pdf->escreve_relativo($total_curso_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
						//nao analfabeto
						$this->pdf->escreve_relativo($total_curso_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
						$left = 33 + 60 + 70 + 268;
						$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
						$left += 93;
						$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
						$left = 33;
						$this->page_y += $altura;
						$total_curso_analfabeto = $total_curso_nao_analfabeto = 0;
						if ($this->page_y > $this->pdf->altura - 50)
						{
							$this->pdf->ClosePage();
							$this->pdf->OpenPage();
							$this->addCabecalho();
							$this->novoCabecalho();
						}
					}
					$ref_cod_serie = $registro["ref_ref_cod_serie"];
					$ref_cod_curso = $registro["ref_cod_curso"];
					$ref_cod_turma = $registro["ref_cod_turma"];
					$nm_turma = $registro["nm_turma"];
					$nm_serie = $registro["nm_serie"];
					$nm_curso = $registro["nm_curso"];
					$primeira_vez_loop = false;
					$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
					$this->pdf->escreve_relativo($registro["nm_turma"], $left, $this->page_y + 3, 50, $this->page_y, $fonte, $tam_letra, $corTexto, 'center');
					$this->pdf->linha_relativa($left + 55, $this->page_y, 0, $altura);
					$this->pdf->escreve_relativo( $registro["cod_aluno"], $left += 60, $this->page_y + 3, 60, 80, $fonte, $tam_letra, $corTexto, 'center' );
					$this->pdf->linha_relativa($left + 65, $this->page_y, 0, $altura);
					$this->pdf->escreve_relativo( $registro["nome"], $left += 70, $this->page_y + 3, 535, 80, $fonte, $tam_letra, $corTexto, 'left' );
					$this->pdf->linha_relativa($left + 262, $this->page_y, 0, $altura);
					$this->pdf->escreve_relativo( $registro["dt_nasc"], $left += 268, $this->page_y + 3, 70, 80, $fonte, $tam_letra, $corTexto, 'center' );
					$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
					$left += 90;
					if ($registro["analfabeto"] == 1)
					{
						$this->pdf->escreve_relativo("X", $left + 29, $this->page_y + 3, 50, 50,$fonte, $tam_letra+2, $corTexto);
						$total_turma_analfabeto++;
						$total_serie_analfabeto++;
						$total_curso_analfabeto++;
						$total_geral_analfabeto++;
					}
					else
					{
						$this->pdf->escreve_relativo("X", $left + 6, $this->page_y + 3, 50, 50,$fonte, $tam_letra+2, $corTexto);
						$total_turma_nao_analfabeto++;
						$total_serie_nao_analfabeto++;
						$total_curso_nao_analfabeto++;
						$total_geral_nao_analfabeto++;
					}
					$left += 3;
					$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
					$this->page_y += $altura;
					if ($this->page_y > $this->pdf->altura - 50)
					{
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->addCabecalho();
						$this->novoCabecalho();
					}
				}
				$ref_cod_turma = $ref_cod_serie = $ref_cod_curso = -1;
				if ($ref_cod_turma != $registro["ref_cod_turma"] && !$primeira_vez_loop)
				{
					$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
					$total = $total_turma_analfabeto + $total_turma_nao_analfabeto;
					$this->pdf->escreve_relativo("Total Turma {$nm_turma}", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
					$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
					$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
					//analfabeto
					$this->pdf->escreve_relativo($total_turma_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
					//nao analfabeto
					$this->pdf->escreve_relativo($total_turma_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
					$left = 33 + 60 + 70 + 268;
					$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
					$left += 93;
					$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
					$left = 33;
					$this->page_y += $altura;
					$total_turma_analfabeto = $total_turma_nao_analfabeto = 0;
					if ($this->page_y > $this->pdf->altura - 50)
					{
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->addCabecalho();
						$this->novoCabecalho();
					}
				}
				if ($ref_cod_serie != $registro["ref_ref_cod_serie"] && !$primeira_vez_loop)
				{
					$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
					$total = $total_serie_analfabeto + $total_serie_nao_analfabeto;
					$this->pdf->escreve_relativo("Total Série {$nm_serie}", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
					$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
					$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
					//analfabeto
					$this->pdf->escreve_relativo($total_serie_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
					//nao analfabeto
					$this->pdf->escreve_relativo($total_serie_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
					$left = 33 + 60 + 70 + 268;
					$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
					$left += 93;
					$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
					$left = 33;
					$this->page_y += $altura;
					$total_serie_analfabeto = $total_serie_nao_analfabeto = 0;
					if ($this->page_y > $this->pdf->altura - 50)
					{
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->addCabecalho();
						$this->novoCabecalho();
					}
				}
				if ($ref_cod_curso != $registro["ref_cod_curso"] && !$primeira_vez_loop)
				{
					$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
					$total = $total_curso_analfabeto + $total_curso_nao_analfabeto;
					$this->pdf->escreve_relativo("Total Curso {$nm_curso}", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
					$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
					$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
					//analfabeto
					$this->pdf->escreve_relativo($total_curso_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
					//nao analfabeto
					$this->pdf->escreve_relativo($total_curso_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
					$left = 33 + 60 + 70 + 268;
					$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
					$left += 93;
					$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
					$left = 33;
					$this->page_y += $altura;
					$total_curso_analfabeto = $total_curso_nao_analfabeto = 0;
					if ($this->page_y > $this->pdf->altura - 50)
					{
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->addCabecalho();
						$this->novoCabecalho();
					}
				}
				$this->pdf->quadrado_relativo(30, $this->page_y, 535, $altura);
				$total = $total_geral_analfabeto + $total_geral_nao_analfabeto;
				$this->pdf->escreve_relativo("Total Geral", 33, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
				$this->pdf->escreve_relativo("{$total}", $left + 460, $this->page_y + 3, 500, $this->page_y, $fonte, $tam_letra+1, $corTexto, 'left');
				$this->pdf->linha_relativa($left + 455, $this->page_y, 0, $altura);
				//analfabeto
				$this->pdf->escreve_relativo($total_geral_analfabeto, $left + 29 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
				//nao analfabeto
				$this->pdf->escreve_relativo($total_geral_nao_analfabeto, $left + 6 + 485, $this->page_y + 3, 50, 50,$fonte, $tam_letra+1, $corTexto);
				$left = 33 + 60 + 70 + 268;
				$this->pdf->linha_relativa($left + 85, $this->page_y, 0, $altura);
				$left += 93;
				$this->pdf->linha_relativa($left + 18, $this->page_y, 0, $altura);
				$left = 33;
				$this->page_y += $altura;
				$this->pdf->ClosePage();

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
				echo '<script>
	     			alert("Erro ao gerar relatório!\nNão dados a serem apresentados!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     		return true;
			}
		}
		else 
		{
			echo '<script>
	     			alert("Erro ao gerar relatório!\nFalta dados!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     		return true;
		}
		
	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		$this->page_y = 125;
		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 535, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 535, 80, $fonte, 18, $corTexto, 'center' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",136, 70, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 25, 30, 782, 80, $fonte, 10, $corTexto, 'right' );

		//titulo
		$this->pdf->escreve_relativo( "Levantamento Alfabetizados e Não Albetizados \n {$this->nm_escola} ", 120, 85, 530, 80, $fonte, 12, $corTexto, 'left' );
//		$this->pdf->escreve_relativo( "Levantamento Alfabetizados e Não Albetizados \n {$this->nm_escola} ", 120, 85, 530, 80, $fonte, 12, $corTexto, 'left' );

		$obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
		$det_modulo = $obj_modulo->detalhe();
		//Data
		$this->pdf->escreve_relativo( "{$this->meses_do_ano[$this->mes]}/{$this->ano}", 45, 100, 535, 80, $fonte, 10, $corTexto, 'left' );
	}
	
	function novoCabecalho() {
		$altura = 51;
		$fonte = 'arial';
		$corTexto = '#000000';
		$left = 33;
		$this->pdf->quadrado_relativo( 30, $this->page_y, 535, 20 );
		$this->pdf->escreve_relativo( "Turma", $left + 13, $this->page_y + 8, 535, 80, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa($left + 55, $this->page_y, 0, 20);
		$this->pdf->escreve_relativo( "Código Aluno", $left += 60, $this->page_y + 8, 535, 80, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa($left + 65, $this->page_y, 0, 20);
		$this->pdf->escreve_relativo( "Nome Aluno", $left += 70, $this->page_y + 8, 535, 80, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa($left + 262, $this->page_y, 0, 20);
		$this->pdf->escreve_relativo( "Data Nascimento", $left += 268, $this->page_y + 8, 535, 80, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa($left + 85, $this->page_y, 0, 20);
		$this->pdf->escreve_relativo( "Alfabetizado", $left += 90, $this->page_y + 3, 535, 80, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa($left - 5, $this->page_y + 12, 49, 0);
		$this->pdf->escreve_relativo( "Sim", $left += 3, $this->page_y + 11, 535, 80, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa($left + 18, $this->page_y + 12, 0, 8);
		$this->pdf->escreve_relativo( "Não", $left += 22, $this->page_y + 11, 535, 80, $fonte, 7, $corTexto, 'left' );
		$this->page_y += 20;
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
