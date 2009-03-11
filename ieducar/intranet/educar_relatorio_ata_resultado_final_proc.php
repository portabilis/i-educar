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
	var $ref_ref_cod_serie;
	var $ref_cod_curso;
	var $ref_cod_turma;
	
	var $disciplinas = array();
	
	var $ano;

	var $nm_escola;
	var $nm_instituicao;
	var $nm_curso;
	var $nm_serie;
	var $nm_turma;
	var $nm_turno;

	var $pdf;

	var $page_y = 139;

	var $get_link;

	var $campo_assinatura;
	
	var $total = 0;

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
						
	var $array_modulos = array();
	var $dias_letivos;
	var $falta_ch_globalizada;
	
	var $is_padrao;
	var $semestre;

	function renderHTML()
	{
		
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		foreach ($_POST as $key => $value) {
			$this->$key = $value;
		}
		$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';
		
		if (is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_turma) && is_numeric($this->ano))
		{
			$sql = "SELECT 
						cod_matricula,
						aprovado,
						ref_ref_cod_serie,
						(SELECT 
						 	nome 
						 FROM 
							pmieducar.aluno a, 
							cadastro.pessoa p
						 WHERE 
							a.cod_aluno = m.ref_cod_aluno
							AND p.idpes = a.ref_idpes
						) as nome
						FROM 
							pmieducar.matricula m, 
							pmieducar.matricula_turma mt
						WHERE 
							mt.ref_cod_turma = {$this->ref_cod_turma} 
							AND mt.ref_cod_matricula = m.cod_matricula
							AND m.ativo = 1 
							AND mt.ativo = 1
							AND aprovado IN (1, 2)
							AND ano = {$this->ano}
						ORDER BY 
							ref_ref_cod_serie, nome";
			//verificar se a turma é multiseriada
			$obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
			$det_turma = $obj_turma->detalhe();
			$ref_ref_cod_serie_mult = $det_turma["ref_ref_cod_serie_mult"];
 
			/*$db2 = new clsBanco();
			$alunos_multiseriados = array();
			
			if (is_numeric($ref_ref_cod_serie_mult))
			{
				$sql2 = "SELECT 
							cod_matricula, 
							aprovado, 
							(SELECT 
								nome 
							 FROM 
							 	pmieducar.aluno a, 
							 	cadastro.pessoa p 
							 WHERE 
							 	a.cod_aluno = m.ref_cod_aluno 
							 	AND p.idpes = a.ref_idpes ) as nome
							 FROM 
							 	pmieducar.matricula m 
							WHERE 
								m.ref_ref_cod_serie = {$ref_ref_cod_serie_mult} 
								AND m.ref_ref_cod_escola = {$this->ref_cod_escola}
								AND m.ativo = 1  
								AND aprovado IN (1, 2) 
								AND ano = 2007
	 						ORDER BY 
	 							nome";
				$db2->Consulta($sql2);
				if ($db2->Num_Linhas())
				{
					while ($db2->ProximoRegistro()) {
						list($cod_matricula, $aprovado, $nome) = $db2->Tupla();
						$alunos_multiseriados[$cod_matricula] = array("aprovado" => $aprovado, "nome" => $nome);
					}
				}
			}*/
			
			$db = new clsBanco();
			$db->Consulta($sql);
			if ($db->numLinhas())
			{
				$numAlunos = $db->numLinhas();
				$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
				$det_instituicao = $obj_instituicao->detalhe();
				$this->nm_instituicao = $det_instituicao["nm_instituicao"];
	
				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				$det_escola = $obj_escola->detalhe();
				$this->nm_escola = $det_escola['nome'];
				
	
				$obj_escola_serie_disciplina = new clsPmieducarEscolaSerieDisciplina();
				$obj_escola_serie_disciplina->setCamposLista("ref_cod_disciplina");
				$lst_escola_serie_disciplina = $obj_escola_serie_disciplina->lista($this->ref_cod_serie, $this->ref_cod_escola, null, 1);
				foreach ($lst_escola_serie_disciplina as $escola_serie_disciplina)
				{
					$obj_disciplina = new clsPmieducarDisciplina($escola_serie_disciplina);
					$obj_disciplina->setCamposLista("cod_disciplina, carga_horaria, nm_disciplina, abreviatura");
					$det_disciplina = $obj_disciplina->detalhe();
					$this->disciplinas[$det_disciplina["cod_disciplina"]]["nm_disciplina"] = $det_disciplina["nm_disciplina"];
					$this->disciplinas[$det_disciplina["cod_disciplina"]]["abreviatura"] = $det_disciplina["abreviatura"];
					$this->disciplinas[$det_disciplina["cod_disciplina"]]["carga_horaria"] = $det_disciplina["carga_horaria"];
				}
				$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
				$det_curso = $obj_curso->detalhe();
				$this->nm_curso = $det_curso["nm_curso"];
				$this->falta_ch_globalizada = $det_curso["falta_ch_globalizada"];
								
				$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao($det_curso['ref_cod_tipo_avaliacao']);
				$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
				$curso_conceitual = $det_tipo_avaliacao['conceitual'];
								
				$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
				$obj_serie->setOrderby("nm_serie");
				$det_serie = $obj_serie->detalhe();
				$this->nm_serie = $det_serie["nm_serie"];
				
				/*if (is_numeric($ref_ref_cod_serie_mult))
				{
					$obj_serie2 = new clsPmieducarSerie($ref_ref_cod_serie_mult);
					$obj_serie2->setOrderby("nm_serie");
					$det_serie2 = $obj_serie2->detalhe();
					$this->nm_serie .= " / ".$det_serie2["nm_serie"];
				}*/
				
				$obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
				$obj_turma->setCamposLista("nm_turma, hora_inicial");
				$det_turma = $obj_turma->detalhe();
				if ($det_turma["hora_inicial"] < "12:00")
					$this->nm_turno = "Matutino";
				elseif ($det_turma["hora_inicial"] < "18:00")
					$this->nm_turno = "Vespertino";
				else
					$this->nm_turno = "Noturno";
				$this->nm_turma = $det_turma["nm_turma"];
				$this->buscaDiasLetivos();
				
				asort($this->disciplinas);
				
				$this->pdf = new clsPDF("Ata de Resultado Final - {$this->ano}", "Ata de Resultado Final", "A4", "", false, false);
				$this->pdf->largura  = 842.0;
			  	$this->pdf->altura = 595.0;
				$this->pdf->OpenFile();
				$this->addCabecalho();
				
				$esquerda = 3;
				$altura = 30;
				$direita = 834;
				$tam_texto = 10;
				$altura = 130;
				
				if ($det_curso["padrao_ano_escolar"] == 1)
				{
					$this->array_modulos = array(array('nm_tipo' => 'Bimestre'),array('nm_tipo' => 'Bimestre'),array('nm_tipo' => 'Bimestre'),array('nm_tipo' => 'Bimestre'));
				}
				else 
				{
					$obj_turma_modulo = new clsPmieducarTurmaModulo();
					$lst_turma_modulo = $obj_turma_modulo->lista($this->ref_cod_turma);
					foreach ($lst_turma_modulo as $modulo)
					{
						$obj_modulo = new clsPmieducarModulo($modulo);
						$det_modulo = $obj_modulo->detalhe();
						$this->array_modulos[] = $det_modulo;
					}
				}
				
				$altura += 50;
				$espessura_linha = 0.3;
				
				$alunos_matriculados = array();
				while ($db->ProximoRegistro()) {
					list($cod_matricula, $aprovado, $ref_cod_serie, $nome) = $db->Tupla();
					$alunos_matriculados[$cod_matricula] = array("aprovado" => $aprovado, "nome" => $nome, "ref_cod_serie" => $ref_cod_serie);
				}
				/*if (is_array($alunos_multiseriados))
				{
					foreach ($alunos_multiseriados as $cod_matricula => $dados) {
						$alunos_matriculados[$cod_matricula] = array("aprovado" => $dados["aprovado"], "nome" => $dados["nome"]);
					}
				}*/
				if (is_array($alunos_matriculados) && count($alunos_matriculados)) {
					if ($this->falta_ch_globalizada) {
						$this->montaAlunoGlobalizados($alunos_matriculados, $det_curso, $curso_conceitual);
					} else {
						$this->montaAlunoNaoGlobalizados($alunos_matriculados, $det_curso, $curso_conceitual);
					}
				}			
				
				$this->rodape();
				
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
			else 
			{
				echo '<script>
	     					alert("A turma não possui nenhum aluno com situação final definida");
	     					window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
			    return true;
			}
		}
		else 
		{
			echo '<script>
	     					alert("A turma não possui nenhum aluno com situação final definida");
	     					window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
			    return true;
		}
	}
	
	function rodape() {			
//		$controle = 0;
		$texto = "";
		$esquerda = 3;
		$altura = 22;
		$direita = 834;
		$tam_texto = 10;
		$this->pdf->escreve_relativo("Legenda", $esquerda + 2, $this->page_y += 30, 150, 100, $fonte, $tam_texto);
		if (count($this->disciplinas) > 10) {
			$legenda_por_linha = true;
		} else {
			$legenda_por_linha = false;
		}
		foreach ($this->disciplinas as $disciplina) {
//			$controle++;
			$texto .= "{$disciplina["abreviatura"]}:{$disciplina["nm_disciplina"]}        ";
			if ($legenda_por_linha) {
				$this->pdf->escreve_relativo($texto, $esquerda + 2, $this->page_y += $altura, $direita, 200, $fonte, $tam_texto+1);
				$texto = "";
				if ($this->page_y > $this->pdf->altura - 50) // || $this->page_y + 50 > $this->pdf->altura - 120)
				{
					$texto = "";
					$esquerda = 3;
					$altura = 18;
					$direita = 834;
					$tam_texto = 10;
					$this->pdf->ClosePage();
					$this->pdf->OpenPage();
					$this->addCabecalho();
				}
			}
		}
		if (!$legenda_por_linha) {
			$this->pdf->escreve_relativo($texto, $esquerda + 2, $this->page_y += $altura, $direita, 200, $fonte, $tam_texto+1);
		}
		if ($this->page_y + $altura * 2 > $this->pdf->altura - 50) {
			$texto = "";
			$esquerda = 3;
			$altura = 18;
			$direita = 834;
			$tam_texto = 10;
			$this->pdf->ClosePage();
			$this->pdf->OpenPage();
			$this->addCabecalho();
		}
		$this->pdf->quadrado_relativo($esquerda, $this->page_y += $altura*2, $direita, 60);//, $espessura_linha);
		$this->pdf->escreve_relativo("Observações:", $esquerda + 1, $this->page_y + 1, 150, 200, $fonte, $tam_texto);
		$this->pdf->linha_relativa($esquerda + 200, $this->page_y += 120, 150, 0);//, $espessura_linha);
		$this->pdf->escreve_relativo("Assinatura do(a) Secretário(a)", $esquerda + 220, $this->page_y + 2, 150, 200, $fonte, 7);
		$this->pdf->linha_relativa($esquerda + 450, $this->page_y, 150, 0);//, $espessura_linha);
		$this->pdf->escreve_relativo("Assinatura do(a) Diretor(a)", $esquerda + 480, $this->page_y + 2, 150, 200, $fonte, 7);
	}
		
	//monta alunos que a serie nao tem falta globalizada
	function montaAlunoNaoGlobalizados($alunos_matriculados, $det_curso, $curso_conceitual) {
		$fonte = 'arial';
		$corTexto = '#000000';
		/*$esquerda = */$esquerda_original = 3;
		$espessura_linha = 0.3;
		$tam_texto = 9;
		$direita = 834;
		$altura = 20;
		$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
		$det_serie = $obj_serie->detalhe();
		foreach ($alunos_matriculados as $matricula => $aluno) {
//			break;
			$esquerda = $esquerda_original;
			$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
			$this->pdf->escreve_relativo($matricula, $esquerda, $this->page_y + 2, 45, 45, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);//, $espessura_linha);
			$espaco_nome = 150;
			$this->pdf->escreve_relativo($aluno["nome"], $esquerda, $this->page_y + 2, $espaco_nome, 45, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda += $espaco_nome, $this->page_y, 0, $altura);//, $espessura_linha);
			$this->pdf->escreve_relativo($aluno["aprovado"] == 1 ? "Aprovado" : "Reprovado", $esquerda, $this->page_y + 4, 45, 45, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);//, $espessura_linha);
			$espaco_disciplinas = ceil(($direita - $esquerda) / (count($this->disciplinas)));
			foreach ($this->disciplinas as $cod_disciplina => $disciplina) {
				$obj_dispensa_disciplina = new clsPmieducarDispensaDisciplina($matricula, $aluno["ref_cod_serie"], $this->ref_cod_escola, $cod_disciplina);
				if ($obj_dispensa_disciplina->detalhe())
				{
					$media = $faltas = "D";
				}
				else 
				{
					$nota_exame = false;
					if ($curso_conceitual) {
						$obj_nota_aluno = new clsPmieducarNotaAluno();
						$obj_nota_aluno->setOrderby("modulo ASC");
//						$lst_nota_aluno = $obj_nota_aluno->lista(null, null, $det_curso["ref_cod_tipo_avaliacao"], $aluno["ref_cod_serie"], $this->ref_cod_escola,
						$lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, $aluno["ref_cod_serie"], $this->ref_cod_escola,
																 $cod_disciplina, $matricula, null, null, null, null, null, null, 1);
						if (is_array($lst_nota_aluno) && count($lst_nota_aluno)) {
							foreach ($lst_nota_aluno as $key => $nota) {
								$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota["ref_ref_cod_tipo_avaliacao"], $nota["ref_sequencial"], null, null, null, null, 1);
								$det_tipo_av_val = $obj_tipo_av_val->detalhe();
								$media = $det_tipo_av_val["nome"];
							}
						}
					} else {
						if (dbBool($det_serie["ultima_nota_define"])) {
							$obj_nota_aluno = new clsPmieducarNotaAluno();
							$media = $obj_nota_aluno->getUltimaNotaModulo($matricula, $cod_disciplina, $aluno["ref_cod_serie"], count($this->array_modulos));
						} else {
							
							$obj_nota_aluno = new clsPmieducarNotaAluno();
							$obj_nota_aluno->setOrderby("modulo ASC");
//							$lst_nota_aluno = $obj_nota_aluno->lista(null, null, $det_curso["ref_cod_tipo_avaliacao"], $aluno["ref_cod_serie"], $this->ref_cod_escola,
							$lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, $aluno["ref_cod_serie"], $this->ref_cod_escola,
																	$cod_disciplina, $matricula, null, null, null, null, null, null, 1);
							$qtd_modulos = count($this->array_modulos);
							$notas = 0;							
							
							if (is_array($lst_nota_aluno) && count($lst_nota_aluno)) {
								foreach ($lst_nota_aluno as $key => $nota) {
									if ($key < $qtd_modulos) {
										$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota["ref_ref_cod_tipo_avaliacao"], $nota["ref_sequencial"], null, null, null, null);
										$det_tipo_av_val = $obj_tipo_av_val->detalhe();
										$notas += $det_tipo_av_val["valor"];
									} else {
										$notas += $nota["nota"] * 2;
										$nota_exame = true;
									}
								}
							}						
							
							$media = $nota_exame ? $notas / (count($lst_nota_aluno) + 1) : $notas / count($lst_nota_aluno);
							$obj_media = new clsPmieducarTipoAvaliacaoValores();
							$det_media = $obj_media->lista($det_curso["ref_cod_tipo_avaliacao"], $det_curso["ref_sequencial"], null, null, $media, $media);
							if (is_array($det_media) && count($det_media)) {
								$det_media = array_shift($det_media);
								$media = $det_media["valor"];
								$media = str_replace(".", ",", sprintf("%01.1f", $media));
							}
						}
					}
					$obj_falta = new clsPmieducarFaltaAluno();
					$faltas = $obj_falta->total_faltas_disciplina($matricula, $cod_disciplina, $aluno["ref_cod_serie"]);
				}
				$this->pdf->escreve_relativo($media, $esquerda, $this->page_y + 4, $espaco_disciplinas / 2, 100, $fonte, $tam_texto + 1, $corTexto, 'center');
				$this->pdf->linha_relativa($esquerda + $espaco_disciplinas / 2, $this->page_y, 0, $altura);//, $espessura_linha);
				$this->pdf->escreve_relativo($faltas, $esquerda + $espaco_disciplinas / 2, $this->page_y + 4, $espaco_disciplinas / 2, 100, $fonte, $tam_texto + 1, $corTexto, 'center');
				$esquerda += $espaco_disciplinas;
				$this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);//, $espessura_linha);
			}
			$this->page_y += $altura;
			if ($this->page_y > $this->pdf->altura - $altura * 2) {
				$this->pdf->ClosePage();
				$this->pdf->OpenPage();
				$this->addCabecalho();
			}		
		}
	}
	
	//monta alunos que a serie tem falta globalizada
	function montaAlunoGlobalizados($alunos_matriculados, $det_curso, $curso_conceitual) {
		$fonte = 'arial';
		$corTexto = '#000000';
		/*$esquerda =*/ $esquerda_original = 3;
		$espessura_linha = 0.3;
		$tam_texto = 7;
		$direita = 834;
		$altura = 20;
		$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
		$det_serie = $obj_serie->detalhe();
		foreach ($alunos_matriculados as $matricula => $aluno) {
//			break;
			$esquerda = $esquerda_original;
			$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
			$this->pdf->escreve_relativo($matricula, $esquerda, $this->page_y + 2, 45, 45, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);//, $espessura_linha);
			$espaco_nome = 150;
			$this->pdf->escreve_relativo($aluno["nome"], $esquerda, $this->page_y + 2, $espaco_nome, 45, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda += $espaco_nome, $this->page_y, 0, $altura);//, $espessura_linha);
			$this->pdf->escreve_relativo($aluno["aprovado"] == 1 ? "Aprovado" : "Reprovado", $esquerda, $this->page_y + 4, 45, 45, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);//, $espessura_linha);
			$espaco_disciplinas = ceil(($direita - $esquerda) / (count($this->disciplinas) + 1));			
			foreach ($this->disciplinas as $cod_disciplina => $disciplina) {
				$obj_dispensa_disciplina = new clsPmieducarDispensaDisciplina($matricula, $aluno["ref_cod_serie"], $this->ref_cod_escola, $cod_disciplina);
				if ($obj_dispensa_disciplina->detalhe())
				{
					$media = $faltas = "D";
				}
				else 
				{
					$nota_exame = false;
					if ($curso_conceitual) {
						$obj_nota_aluno = new clsPmieducarNotaAluno();
						$obj_nota_aluno->setOrderby("modulo ASC");
						$lst_nota_aluno = $obj_nota_aluno->lista(null, null, $det_curso["ref_cod_tipo_avaliacao"], $aluno["ref_cod_serie"], $this->ref_cod_escola,
																$cod_disciplina, $matricula, null, null, null, null, null, null, 1);
						if (is_array($lst_nota_aluno) && count($lst_nota_aluno)) {
							foreach ($lst_nota_aluno as $key => $nota) {
								$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota["ref_ref_cod_tipo_avaliacao"], $nota["ref_sequencial"], null, null, null, null, 1);
								$det_tipo_av_val = $obj_tipo_av_val->detalhe();
								$media = $det_tipo_av_val["nome"];
							}
						}
					} else {
						if (dbBool($det_serie["ultima_nota_define"])) {
							$obj_nota_aluno = new clsPmieducarNotaAluno();
							$media = $obj_nota_aluno->getUltimaNotaModulo($matricula, $cod_disciplina, $aluno["ref_cod_serie"], count($this->array_modulos));
						} else {
							$obj_nota_aluno = new clsPmieducarNotaAluno();
							$obj_nota_aluno->setOrderby("modulo ASC");
							$lst_nota_aluno = $obj_nota_aluno->lista(null, null, $det_curso["ref_cod_tipo_avaliacao"], $aluno["ref_cod_serie"], $this->ref_cod_escola,
							$cod_disciplina, $matricula, null, null, null, null, null, null, 1);
							$qtd_modulos = count($this->array_modulos);
							$notas = 0;
							if (is_array($lst_nota_aluno) && count($lst_nota_aluno)) {
								foreach ($lst_nota_aluno as $key => $nota) {
									if ($key < $qtd_modulos) {
										$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota["ref_ref_cod_tipo_avaliacao"], $nota["ref_sequencial"], null, null, null, null);
										$det_tipo_av_val = $obj_tipo_av_val->detalhe();
										$notas += $det_tipo_av_val["valor"];
									} else {
										$notas += $nota["nota"] * 2;
										$nota_exame = true;
									}
								}
							}
							$media = $nota_exame ? $notas / (count($lst_nota_aluno) + 1) : $notas / count($lst_nota_aluno);
							$obj_media = new clsPmieducarTipoAvaliacaoValores();
							$det_media = $obj_media->lista($det_curso["ref_cod_tipo_avaliacao"], $det_curso["ref_sequencial"], null, null, $media, $media);
							if (is_array($det_media) && count($det_media)) {
								$det_media = array_shift($det_media);
								$media = $det_media["valor"];
								$media = str_replace(".", ",", sprintf("%01.1f", $media));
							}
						}
					}
					$this->pdf->escreve_relativo($media, $esquerda, $this->page_y + 4, $espaco_disciplinas, 50, $fonte, $tam_texto + 1, $corTexto, 'center');
					$esquerda += $espaco_disciplinas;
					$this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);//, $espessura_linha);
				}
				$obj_falta = new clsPmieducarFaltas();
				$faltas = $obj_falta->somaFaltas($matricula);
				$this->pdf->escreve_relativo($faltas ? $faltas : "0", $esquerda, $this->page_y + 4, $espaco_disciplinas, 50, $fonte, $tam_texto + 1, $corTexto, 'center');
				$esquerda += $espaco_disciplinas;
				$this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);//, $espessura_linha);
			}
			$this->page_y += $altura;
			if ($this->page_y > $this->pdf->altura - $altura * 2) {
				$this->pdf->ClosePage();
				$this->pdf->OpenPage();
				$this->addCabecalho();
			}
		}
	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$this->page_y = 30;
		$fonte = 'arial';
		$corTexto = '#000000';
		$esquerda = $esquerda_original = 3;
		$espessura_linha = 0.3;
		$tam_texto = 9;
		$direita = 834;
		$altura = 20;

		// cabecalho
		$this->pdf->quadrado_relativo( $esquerda, $this->page_y, 834, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 782, 80, $fonte, 18, $corTexto, 'center' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 745, 30, 100, 80, $fonte, 12, $corTexto, 'left' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição: $this->nm_instituicao", 120, 52, 300, 80, $fonte, 9, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola: {$this->nm_escola}",132, 64, 300, 80, $fonte, 9, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Ata de Resultado Final - {$this->ano}", 30, 78, $direita, 80, $fonte, 12, $corTexto, 'center' );
		
		$this->pdf->quadrado_relativo($esquerda, $this->page_y += 100, $direita, $altura);
		$this->pdf->escreve_relativo("Disciplina", $esquerda + 30, $this->page_y+1, 150, 50, $fonte, 9, $corTexto, 'center');
		$this->pdf->escreve_relativo("Carga Horária", $esquerda + 35, $this->page_y+10, 150, 50, $fonte, 7, $corTexto, 'center');
		$this->page_y += $altura;
		$this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);
		$this->pdf->escreve_relativo("Matrícula", $esquerda, $this->page_y + 2, 45, 45, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);//, $espessura_linha);
		$espaco_nome = 150;
		$this->pdf->escreve_relativo("Nome do Aluno", $esquerda, $this->page_y + 2, $espaco_nome, 45, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += $espaco_nome, $this->page_y, 0, $altura);//, $espessura_linha);
		$this->pdf->escreve_relativo("Situação", $esquerda, $this->page_y + 2, 45, 45, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);//, $espessura_linha);
		
		if ($this->falta_ch_globalizada) {
			$carga_global = $this->montaDisciplinasGlobalizada($esquerda, $direita - $esquerda);
		} else {
			$carga_global = $this->montaDisciplinasNaoGlobalizada($esquerda, $direita - $esquerda);
		}
		$nm_curso = "Curso: {$this->nm_curso}                    Série: {$this->nm_serie}     Turma: {$this->nm_turma}     Dias Letivos: {$this->dias_letivos}          Carga Global: {$carga_global}             Turno: {$this->nm_turno}";
		$this->pdf->quadrado_relativo($esquerda_original, $this->page_y, $direita, $altura);
		$this->pdf->escreve_relativo($nm_curso, $esquerda_original + 10, $this->page_y + 5, $direita, 50, $fonte, $tam_texto);
		$this->page_y += $altura;
	}
	
	function montaDisciplinasGlobalizada($esquerda, $espaco_disciplina) {
		$tam_texto = 9;
		$espaco_disciplinas = ceil($espaco_disciplina / (count($this->disciplinas) + 1));
		$altura = 20;
		$tam_texto = 9;
		$fonte = 'arial';
		$corTexto = '#000000';
		$carga_global = 0;
		foreach ($this->disciplinas as $disciplina) {
			$carga_global += $disciplina["carga_horaria"];
			$this->pdf->escreve_relativo($disciplina["abreviatura"], $esquerda, $this->page_y - $altura, $espaco_disciplinas, 100, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->escreve_relativo($disciplina["carga_horaria"]." hrs", $esquerda, $this->page_y + 10 - $altura, $espaco_disciplinas, 50, $fonte, $tam_texto - 2, $corTexto, 'center');
			$this->pdf->escreve_relativo("Nota / Conceito", $esquerda, $this->page_y + 3, $espaco_disciplinas, 50, $fonte, $tam_texto - 2, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda, $this->page_y - $altura, 0, $altura * 2);//, $espessura_linha);
			$esquerda += $espaco_disciplinas;
		}
		$this->pdf->linha_relativa($esquerda, $this->page_y - $altura, 0, $altura * 2);
		$this->pdf->escreve_relativo("Faltas", $esquerda, $this->page_y, $espaco_disciplinas, 50, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += $espaco_disciplinas, $this->page_y - $altura, 0, $altura * 2);
		$this->page_y += $altura;
		return $carga_global;
	}
	
	function montaDisciplinasNaoGlobalizada($esquerda, $espaco_disciplina) {
		$tam_texto = 9;
		$espaco_disciplinas = ceil($espaco_disciplina / (count($this->disciplinas)));
		$altura = 20;
		$tam_texto = 9;
		$fonte = 'arial';
		$corTexto = '#000000';
		$carga_global = 0;
		foreach ($this->disciplinas as $disciplina) {
			$carga_global += $disciplina["carga_horaria"];
			$this->pdf->escreve_relativo($disciplina["abreviatura"], $esquerda, $this->page_y - $altura, $espaco_disciplinas, 100, $fonte, $tam_texto, $corTexto, 'center');
			$this->pdf->escreve_relativo($disciplina["carga_horaria"]." hrs", $esquerda, $this->page_y + 10 - $altura, $espaco_disciplinas, 50, $fonte, $tam_texto - 2, $corTexto, 'center');
			$this->pdf->escreve_relativo("Nota / Conceito", $esquerda, $this->page_y + 3, $espaco_disciplinas / 2, 50, $fonte, $tam_texto - 2, $corTexto, 'center');
			$this->pdf->escreve_relativo("Falta", $esquerda + $espaco_disciplinas / 2, $this->page_y + 3, $espaco_disciplinas / 2, 50, $fonte, $tam_texto - 2, $corTexto, 'center');
			$this->pdf->linha_relativa($esquerda, $this->page_y - $altura, 0, $altura * 2);//, $espessura_linha);
			$this->pdf->linha_relativa($esquerda + $espaco_disciplinas / 2, $this->page_y, 0, $altura);//, $espessura_linha);
			$esquerda += $espaco_disciplinas;
		}
		$this->page_y += $altura;
		return $carga_global;
	}
	
	function buscaDiasLetivos()
	{
		$obj_calendario = new clsPmieducarEscolaAnoLetivo();
		$lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

		$totalDiasUteis = 0;
		$total_semanas = 0;

		$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
		$obj_ano_letivo_modulo->setOrderby("data_inicio asc");

		$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista($this->ano, $this->ref_cod_escola, null, null);
		
		if($lst_ano_letivo_modulo)
		{
			$inicio = $lst_ano_letivo_modulo['0'];
			$fim	= $lst_ano_letivo_modulo[count($lst_ano_letivo_modulo) - 1];

			$mes_inicial = explode("-",$inicio['data_inicio']);
			$mes_inicial = $mes_inicial[1];

			$dia_inicial = $mes_inicial[2];

			$mes_final	 = explode("-",$fim['data_fim']);
			$mes_final	 = $mes_final[1];

			$dia_final   = $mes_final[2];
		}

		for ($mes = $mes_inicial;$mes <= $mes_final;$mes++)
		{
			$obj_calendario_dia = new clsPmieducarCalendarioDia();
			$lista_dias = $obj_calendario_dia->lista($calendario['cod_calendario_ano_letivo'],$mes,null,null,null,null,null,null,null,1);

			$dias_mes = array();

			if($lista_dias)
			{
				foreach ($lista_dias as $dia) {
					$obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);
					$det_motivo = $obj_motivo->detalhe();
					$dias_mes[$dia['dia']] = strtolower($det_motivo['tipo']);
				}
			}
			//Dias previstos do mes

			// Qual o primeiro dia do mes
			$primeiroDiaDoMes = mktime(0,0,0,$mes,1,$this->ano);

			// Quantos dias tem o mes
			$NumeroDiasMes = date('t',$primeiroDiaDoMes);

			//informacoes primeiro dia do mes
			$dateComponents = getdate($primeiroDiaDoMes);
			
			// What is the name of the month in question?
			$NomeMes = $mesesDoAno[$dateComponents['mon']];

			// What is the index value (0-6) of the first day of the
			// month in question.
			$DiaSemana = $dateComponents['wday'];
	
			//total de dias uteis + dias extra-letivos - dia nao letivo - fim de semana
			$DiaSemana = 0;

			if($mes == $mes_inicial)
			{
				$dia_ini = $dia_inicial;
			}
			elseif($mes == $mes_final)
			{
				$dia_ini = $dia_final;
			}
			else
			{
				$dia_ini = 1;
			}

			for($dia = $dia_ini; $dia <= $NumeroDiasMes; $dia++)
			{
				if($DiaSemana >= 7)
				{
					$DiaSemana = 0;
					$total_semanas++;
				}

				if($DiaSemana != 0 && $DiaSemana != 6) {
					if(!(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('n')))
					$totalDiasUteis++;
				} elseif(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('e'))
				$totalDiasUteis++;

				$DiaSemana++;

			}


		}

		$this->dias_letivos = $totalDiasUteis;
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
