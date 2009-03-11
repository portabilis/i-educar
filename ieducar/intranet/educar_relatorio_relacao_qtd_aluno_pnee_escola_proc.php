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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Rela&ccedil;&atilde;o de Alunos ANEEs" );
		$this->processoAp = "900";
 
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
	var $totalDiasUteis;
	var $necessidades;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $cursos = array();
	var $get_link = false;

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

	var $qtd_alunos_def;
	var $preencheu_qtd;

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
		$this->preencheu_qtd = false;
		$this->qtd_alunos_def = array();
		if (is_numeric($this->ref_cod_escola))
		{
			$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
//			$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $this->ref_cod_escola, $this->ano,null,null,1,null,null,null,null,1 );
			$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $this->ref_cod_escola, $this->ano,null,null,null,null,null,null,null,1 );
			if (!is_array($lst_escola_ano_letivo))
			{
		     	echo '<script>
		     			alert("Escola não possui calendário definido para este ano");
		     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));	
		     		</script>';
		     	die();
			}
			$esquerda = 30;
			$cima = 125;
			$direita = 535;
			$baixo = 627;
			$controle_pox_Y = $cima + 102;
			$fonte = 'arial';
			$corTexto = '#000000';
			$tamTexto = 8;
			
			$sql = "SELECT cod_deficiencia, count(0) as quantidade,
							(SELECT fantasia WHERE es.ref_idpes = idpes
								UNION 
							 SELECT nm_escola FROM pmieducar.escola_complemento ec
							 	WHERE cod_escola = ref_cod_escola) as nome_escola
						FROM pmieducar.matricula m, pmieducar.aluno a,
						cadastro.fisica_deficiencia fd, cadastro.deficiencia d, pmieducar.escola es, cadastro.juridica j
						WHERE a.cod_aluno = m.ref_cod_aluno AND a.ref_idpes = fd.ref_idpes
						AND cod_deficiencia = ref_cod_deficiencia AND ano = {$this->ano}
						AND m.ativo = 1 AND m.aprovado IN (1, 2, 3) AND a.ativo = 1
						AND es.cod_escola = ref_ref_cod_escola 
						AND ref_cod_instituicao = {$this->ref_cod_instituicao}
						AND ref_ref_cod_escola = {$this->ref_cod_escola}
						AND j.idpes = es.ref_idpes 
						GROUP BY cod_deficiencia, nome_escola ORDER BY cod_deficiencia";
			$db = new clsBanco();
			$db->Consulta($sql);
			$nome_colocado = false;
			if ($db->Num_Linhas())
			{
				$this->pdf = new clsPDF("Relação de Alunos ANEEs", "Relação de Alunos ANEEs", "A4", "", false, false);
				$this->pdf->OpenPage();
				$this->necessidades = array();
				$this->addCabecalho();
				$this->novaPagina();
				$qtd_mostrar = 0;
				while ($db->ProximoRegistro())
				{
					list($cod_deficiencia, $quantidade, $nome_escola) = $db->Tupla();
					if (!$nome_colocado)
					{
						$nome_colocado = true;
						$this->pdf->escreve_relativo($nome_escola, $esquerda + 1, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					}
					$qtd_mostrar += $quantidade;
					$this->qtd_alunos_def[$cod_deficiencia] += $quantidade;
					if ($quantidade < 100)
						$this->pdf->escreve_relativo($quantidade < 10 ? "0{$quantidade}" : $quantidade, $this->necessidades[$cod_deficiencia] + 45, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					else 
						$this->pdf->escreve_relativo($quantidade, $this->necessidades[$cod_deficiencia] + 43, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
				}
				if ($qtd_mostrar < 100)
					$this->pdf->escreve_relativo($qtd_mostrar < 10 ? "0{$qtd_mostrar}" : $qtd_mostrar, max($this->necessidades) + 45 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
				else
					$this->pdf->escreve_relativo($qtd_mostrar, max($this->necessidades) + 43 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
				$this->pdf->linha_relativa($esquerda, $controle_pox_Y + 4, $direita, 0);
				$controle_pox_Y += 15;
				$this->pdf->escreve_relativo("TOTAL GERAL", $esquerda + 230, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
				$qtd_mostrar = 0;
				foreach ($this->qtd_alunos_def as $cod_deficiencia => $total) {
					$qtd_mostrar += $total;
					if ($total < 100)
						$this->pdf->escreve_relativo($total < 10 ? "0{$total}" : $total, $this->necessidades[$cod_deficiencia] + 45, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					else 
						$this->pdf->escreve_relativo( $total, $this->necessidades[$cod_deficiencia] + 43, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
				}
				if ($qtd_mostrar < 100)
					$this->pdf->escreve_relativo($qtd_mostrar < 10 ? "0{$qtd_mostrar}" : $qtd_mostrar, max($this->necessidades) + 45 + 15, $controle_pox_Y, 0 ,0, $fonte, $tamTexto, $corTexto);
				else
					$this->pdf->escreve_relativo($qtd_mostrar, max($this->necessidades) + 43 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
				$this->pdf->linha_relativa($esquerda, $controle_pox_Y + 4, $direita, 0);
				$this->rodape();

				$this->pdf->ClosePage();
				$this->get_link = $this->pdf->GetLink();
				$this->pdf->CloseFile();

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
	     			alert("A escola não possui nenhum aluno com deficiência!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
				die();
	     		return true;
			}
		}
		else
		{
			$sql = "SELECT
						cod_escola
						, fantasia as nome
						FROM
							pmieducar.escola
							, cadastro.juridica
						WHERE
							ref_cod_instituicao = {$this->ref_cod_instituicao}
							AND idpes = ref_idpes
							AND ativo = 1
					UNION
						SELECT
							cod_escola
							, nm_escola
						FROM
							pmieducar.escola
							, pmieducar.escola_complemento
						WHERE
							ref_cod_instituicao = {$this->ref_cod_instituicao}
							AND cod_escola = ref_cod_escola
							AND escola.ativo = 1
						ORDER BY 2 ASC";
			$db = new clsBanco();
			$db->Consulta($sql);
			$esquerda = 30;
			$cima = 125;
			$direita = 535;
			$baixo = 627;
			$controle_pox_Y = $cima + 102;
			if ($db->Num_Linhas())
			{
				$this->pdf = new clsPDF("Relação de Alunos ANEEs", "Relação de Alunos ANEEs", "A4", "", false, false);
				$this->pdf->OpenPage();
				$this->necessidades = array();
				$this->addCabecalho();
				$this->novaPagina();
				$possui_registro = false;
				while ($db->ProximoRegistro())
				{
					list($ref_cod_escola, $nome_escola) = $db->Tupla();
					if (is_numeric($ref_cod_escola))
					{

						$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
//			$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $this->ref_cod_escola, $this->ano,null,null,1,null,null,null,null,1 );
						$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $ref_cod_escola, $this->ano,null,null,null,null,null,null,null,1 );
						if (is_array($lst_escola_ano_letivo))
						{
							$fonte = 'arial';
							$corTexto = '#000000';
							$tamTexto = 8;
							$sql = "SELECT cod_deficiencia, count(0) as quantidade									
										FROM pmieducar.matricula m, pmieducar.aluno a,
										cadastro.fisica_deficiencia fd, cadastro.deficiencia d, pmieducar.escola es
										WHERE a.cod_aluno = m.ref_cod_aluno AND a.ref_idpes = fd.ref_idpes
										AND cod_deficiencia = ref_cod_deficiencia AND ano = {$this->ano}
										AND m.ativo = 1 AND m.aprovado IN (1, 2, 3) AND a.ativo = 1
										AND es.cod_escola = ref_ref_cod_escola 
										AND ref_cod_instituicao = {$this->ref_cod_instituicao}
										AND ref_ref_cod_escola = {$ref_cod_escola}
										GROUP BY cod_deficiencia ORDER BY cod_deficiencia";
							$db2 = new clsBanco();
							$db2->Consulta($sql);
							if ($db2->Num_Linhas())
							{
								$possui_registro = true;
								$nome_colocado = false;
								$this->pdf->escreve_relativo($nome_escola, $esquerda + 1, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
								$qtd_mostrar = 0;
								while ($db2->ProximoRegistro())
								{
									list($cod_deficiencia, $quantidade) = $db2->Tupla();
									$qtd_mostrar += $quantidade;
									$this->qtd_alunos_def[$cod_deficiencia] += $quantidade;
									if ($quantidade < 100)
										$this->pdf->escreve_relativo($quantidade < 10 ? "0{$quantidade}" : $quantidade, $this->necessidades[$cod_deficiencia] + 45, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
									else 
										$this->pdf->escreve_relativo($quantidade, $this->necessidades[$cod_deficiencia] + 43, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
								}
								if ($qtd_mostrar < 100)
									$this->pdf->escreve_relativo($qtd_mostrar < 10 ? "0{$qtd_mostrar}" : $qtd_mostrar, max($this->necessidades) + 45 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
								else
									$this->pdf->escreve_relativo($qtd_mostrar, max($this->necessidades) + 43 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
								$controle_pox_Y += 15;
								if ($controle_pox_Y >= $this->pdf->altura - 80)
								{
									$this->pdf->ClosePage();
									$this->pdf->OpenPage();
									$this->addCabecalho();
									$this->novaPagina();
									$controle_pox_Y = $cima + 102;
								}
								else
								{
									$this->pdf->linha_relativa($esquerda, $controle_pox_Y - 11, $direita, 0);
								}
							}
						}
					}
				}
				if ($possui_registro)
				{
					$this->pdf->escreve_relativo("TOTAL GERAL", $esquerda + 230, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					$qtd_mostrar = 0;
					foreach ($this->qtd_alunos_def as $cod_deficiencia => $total) {
						$qtd_mostrar += $total;
						if ($total < 100)
							$this->pdf->escreve_relativo($total < 10 ? "0{$total}" : $total, $this->necessidades[$cod_deficiencia] + 45, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
						else
							$this->pdf->escreve_relativo($total, $this->necessidades[$cod_deficiencia] + 43, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					}
					if ($qtd_mostrar < 100)
						$this->pdf->escreve_relativo($qtd_mostrar < 10 ? "0{$qtd_mostrar}" : $qtd_mostrar, max($this->necessidades) + 45 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					else 
						$this->pdf->escreve_relativo($qtd_mostrar, max($this->necessidades) + 43 + 15, $controle_pox_Y, 0, 0, $fonte, $tamTexto, $corTexto);
					$this->pdf->linha_relativa($esquerda, $controle_pox_Y + 4, $direita, 0);
					$this->rodape();

					$this->pdf->ClosePage();
					$this->get_link = $this->pdf->GetLink();
					$this->pdf->CloseFile();

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
		     				alert("Nenhum aluno com deficiência está matriculado em alguma escola!");
			     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
					die();
		     		return true;
				}
			}
		}

	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 535, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 535, 80, $fonte, 18, $corTexto, 'center' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 500, 30, 100, 80, $fonte, 12, $corTexto, 'left' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição: $this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );

		//titulo
		$this->pdf->escreve_relativo( "Relação de Alunos ANEEs por Escola", 30, 85, 535, 80, $fonte, 14, $corTexto, 'center' );

		//Data
		$this->pdf->escreve_relativo( "Ano: {$this->ano}", 45, 100, 535, 80, $fonte, 10, $corTexto, 'left' );
	}

	function novaPagina()
	{
		$altura2 = 630;
		$altura = 86;

		$fonte = 'arial';
		$corTexto = '#000000';

		$esquerda = 30;
		$cima = 125;
		$direita = 535;
		$baixo = 630;
		$this->pdf->quadrado_relativo( $esquerda, $cima, $direita, $baixo); //quadrado principal

		$this->pdf->linha_relativa($esquerda, $cima + 90, $direita, 0); //linha horizontal

		$this->pdf->linha_relativa($esquerda + 300, $cima, 0, $baixo); // linha vertical

		$this->pdf->escreve_relativo( "Nome da(s) Escola(s)", $esquerda, $cima + 30, $esquerda + 300, $cima + 80, $fonte, 14, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Tipo de Necessidade Educacional Especial", $esquerda + 330, $cima + 1, 170, $altura+80, $fonte, 8, $corTexto, 'center');
		$this->pdf->linha_absoluta($esquerda + 300, $cima + 13, $direita + 30, $cima + 13);

		$sql = "SELECT cod_deficiencia, nm_deficiencia FROM cadastro.deficiencia ORDER BY nm_deficiencia";
		$db2 = new clsBanco();
		$db2->Consulta($sql);
		$controle_def = $esquerda + 253;
		
		while ($db2->ProximoRegistro())
		{
			list($cod_deficiencia, $nm_deficiencia) = $db2->Tupla();
			$nm_deficiencia = eregi_replace("([^\n\r\t])","\n\\1",$nm_deficiencia);
			$this->necessidades[$cod_deficiencia] = $controle_def + 5;
			$this->pdf->escreve_relativo(strtoupper($nm_deficiencia), $controle_def + 5, $cima + 13, 100, $altura, $fonte, 5, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda + $controle_def + 32, $cima + 13, 0, $baixo - 13);
			$controle_def += 15;
			if (!$this->preencheu_qtd)
				$this->qtd_alunos_def[$cod_deficiencia] = 0;
		}
		$total = eregi_replace("([^\n\r\t])","\n\\1","Total");
		$this->pdf->escreve_relativo(strtoupper($total), $controle_def + 5, $cima + 55, 100, $altura, $fonte, 5, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda + $controle_def + 32, $cima + 13, 0, $baixo - 13);
		$this->preencheu_qtd = true;
	}

	function rodape()
	{
		$corTexto = '#000000';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,756, 100, 50, $fonte, 7, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Assinatura do Diretor(a)", 68,795, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Assinatura do secretário(a)", 398,795, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa(52,792,130,0);
		$this->pdf->linha_relativa(385,792,130,0);
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
