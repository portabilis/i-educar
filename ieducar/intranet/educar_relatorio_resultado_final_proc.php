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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Resultado Final" );
		$this->processoAp = "823"; //alterar
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

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $pdf;

	var $nm_turma;
	var $nm_serie;
	var $nm_cidade;

	var $array_modulos;
	
	var $is_padrao;
	var $semestre;
	
	var $get_link;

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

		$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
		$det_escola = $obj_escola->detalhe();
		$this->nm_escola = $det_escola['nome'];
 
		$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
		$det_instituicao = $obj_instituicao->detalhe();
		$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		$obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
		$det_turma = $obj_turma->detalhe();
		$this->nm_turma = $det_turma['nm_turma'];

		$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
		$det_serie = $obj_serie->detalhe();
		$this->nm_serie = $det_serie['nm_serie'];
		
		$eh_multi_seriado = false;
		
		if (is_numeric($det_turma["ref_ref_cod_serie_mult"]))
		{
			$series = array();
			$series[$det_serie["cod_serie"]] = $det_serie["nm_serie"];
			$obj_serie = new clsPmieducarSerie($det_turma["ref_ref_cod_serie_mult"]);
			$det_serie = $obj_serie->detalhe();
			$this->nm_serie .= " / {$det_serie["nm_serie"]}";
			$series[$det_serie["cod_serie"]] = $det_serie["nm_serie"];
			$eh_multi_seriado = true;
		}
		
		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();
		$frequencia_minima = $det_curso["frequencia_minima"];
		$hora_falta = $det_curso["hora_falta"];
		//ref_cod_tipo_avaliacao
		$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao($det_curso["ref_cod_tipo_avaliacao"]);
		$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
		
		$eh_conceitual = $det_tipo_avaliacao["conceitual"];

		if($det_curso['padrao_ano_escolar'])
		{
			$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
			$obj_ano_letivo_modulo->setOrderby("data_inicio asc");
			$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista($this->ano,$this->ref_cod_escola,null,null);
			if($lst_ano_letivo_modulo)
			{
				foreach ($lst_ano_letivo_modulo as $modulo) {
					$obj_modulo = new clsPmieducarModulo($modulo['ref_cod_modulo']);
					$det_modulo = $obj_modulo->detalhe();
					$this->array_modulos[] = $det_modulo;
				}
			}
		}
		
		$obj_disc_serie = new clsPmieducarEscolaSerieDisciplina();
		$lst_disc_serie = $obj_disc_serie->lista($this->ref_cod_serie, $this->ref_cod_escola, null, 1);

		$this->pdf = new clsPDF("Resultado Final", "Resultado Final", "A4", "", false, false);

		$this->pdf->OpenPage();

		$this->addCabecalho();

		$this->pdf->linha_relativa(30, 140, 540, 0);
		$this->pdf->linha_relativa(30, 140, 0, 30);
		$this->pdf->linha_relativa(570, 140, 0, 30);
		$this->pdf->linha_relativa(30, 170, 540, 0);

		$this->pdf->linha_relativa(60, 140, 0, 30);
		$this->pdf->linha_relativa(320, 140, 0, 30);

		$this->pdf->linha_relativa(380, 140, 0, 30);
		$this->pdf->linha_relativa(490, 140, 0, 30);

		$this->pdf->linha_relativa(380, 155, 190, 0);
		$this->pdf->linha_relativa(530, 155, 0, 15);

		$this->pdf->linha_relativa(450, 155, 0, 15);

		$this->pdf->escreve_relativo("Ord", 35, 150, 20, 20, null, 10);
		$this->pdf->escreve_relativo("Nome do aluno", 70, 150, 160, 20, null, 10);
		$this->pdf->escreve_relativo("Aprovado", 325, 150, 160, 20, null, 10);
		$this->pdf->escreve_relativo("Reprovado", 410, 142, 160, 20, null, 10);
		$this->pdf->escreve_relativo("Desempenho", 384, 156, 160, 20, null, 10);
		$this->pdf->escreve_relativo("Faltas", 455, 156, 160, 20, null, 10);
		$this->pdf->escreve_relativo("Alf.", 500, 156, 160, 20, null, 10);
		$this->pdf->escreve_relativo("N. Alf.", 535, 156, 160, 20, null, 10);

		$obj_matricula = new clsPmieducarMatriculaTurma();
		$obj_matricula->setOrderby('m.ref_ref_cod_serie, nome_ascii');
		
		if ($this->is_padrao || $this->ano == 2007) {
			$this->semestre = null;
		}
		
		$lst_matricula = $obj_matricula->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array(1,2,3),null,null,$this->ano,null,true,null,null,true,
												null,null, null, $det_turma["ref_ref_cod_serie_mult"], $this->semestre);
		//$total_alunos = 42;
		$qtd_quebra = 43;
		$base = 155;
		$linha = 1;
			
		$total_aprovados = 0;
		$total_reprovados_desempenho = 0;
		$total_reprovados_nota = 0;
		$total_analfabetos = 0;
		$total_nao_analfabetos = 0;
		$ordem_mostra = 0;		
		if(is_array($lst_matricula))
		{
			foreach ($lst_matricula as $ordem => $matricula)
			{
				$obj_matricula = new clsPmieducarMatricula($matricula["ref_cod_matricula"]);
				$det_matricula = $obj_matricula->detalhe();
				if ($det_matricula["aprovado"] == 1 || $det_matricula["aprovado"] == 2)
				{
					$ordem_mostra++;
					$ordem_mostra = sprintf("%02d",$ordem_mostra);
					if($linha % $qtd_quebra == 0)
					{
						//nova pagina
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$base = 30;
						$linha = 0;
						$this->pdf->linha_relativa(30, 30, 540, 0);
						$qtd_quebra = 51;
					}
					$this->pdf->linha_relativa(30, $base+($linha*15), 0, 15);
					$this->pdf->linha_relativa(60, $base+($linha*15), 0, 15);
					$this->pdf->linha_relativa(30, ($base+15)+($linha*15), 540, 0);
					$this->pdf->linha_relativa(570, $base+($linha*15), 0, 15);//fim
					$this->pdf->escreve_relativo($ordem_mostra, 40, ($base+3)+($linha*15), 15, 15, null, 8);
					if ($eh_multi_seriado)	
						$this->pdf->escreve_relativo($matricula['nome']." ({$series[$det_matricula["ref_ref_cod_serie"]]})", 65, ($base+3)+($linha*15), 250, 15, null, 8);
					else 
						$this->pdf->escreve_relativo($matricula['nome'], 65, ($base+3)+($linha*15), 250, 15, null, 8);
					
					if (!$eh_conceitual)
					{
						if ($det_matricula["aprovado"] == 1)
						{
							$this->pdf->escreve_relativo("X", 345, ($base+3)+($linha*15), 250, 15, null, 8);//aprovado
							$total_aprovados++;
						}
						else 
						{
							$reprovou_por_falta = false;
							$reprovou_por_nota  = false;
							if (is_array($lst_disc_serie))
							{
								foreach ($lst_disc_serie as $disciplina) {
									if (!$reprovou_por_falta)
									{
										$obj_falta = new clsPmieducarFaltaAluno();
										if ($det_curso["padrao_ano_escolar"] == 1)
											$lst_falta = $obj_falta->lista(null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, $disciplina["ref_cod_disciplina"], $matricula["ref_cod_matricula"], null, null, null, null, null, 1);
										else 
											$lst_falta = $obj_falta->lista(null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, null, $matricula["ref_cod_matricula"], null, null, null, null, null, 1, null, $disciplina["ref_cod_disciplina"]);
										$total_faltas = 0;
										if(is_array($lst_falta))
										{
											foreach ($lst_falta as $key => $value)
											{
												$total_faltas += $lst_falta[$key]['faltas'];
											}
										}
										$obj_disciplina = new clsPmieducarDisciplina($disciplina["ref_cod_disciplina"]);
										$det_disciplina = $obj_disciplina->detalhe();
										$carga_horaria_disciplina = $det_disciplina["carga_horaria"];
										$max_falta = ($carga_horaria_disciplina * $frequencia_minima)/100;
										$max_falta = $carga_horaria_disciplina - $max_falta;
										$total_faltas *= $hora_falta;
										if ($total_faltas > $max_falta)
										{
											$this->pdf->escreve_relativo("X", 465, ($base+3)+($linha*15), 250, 15, null, 8);//faltas
											$reprovou_por_falta = true;
											$total_reprovados_desempenho++;
										}
									}
									if (!$reprovou_por_nota)
									{
										$obj_nota = new clsPmieducarNotaAluno();
										$obj_nota->setOrderby("modulo asc");
										if($det_curso['padrao_ano_escolar'] == 1)
											$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'],$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null);
										else
											$det_nota = $obj_nota->lista(null,nul,null,$this->ref_cod_serie,$this->ref_cod_escola,null,$matricula['ref_cod_matricula'],null,null,null,null,null,null,1,null,$disciplina['ref_cod_disciplina']);
	
										if (is_array($det_nota))
										{
	//										usort($det_nota, "cmp");
	
											$soma_notas = 0;
											foreach ($det_nota as $key => $nota) {
	
												$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota['ref_ref_cod_tipo_avaliacao'],$nota['ref_sequencial'],null,null,null,null);
												$det_tipo_av_val = $obj_tipo_av_val->detalhe();
	
												if ( count($this->array_modulos) == count($det_nota) )
												{
													$frequencia_minima = $det_curso["frequencia_minima"];
													$hora_falta = $det_curso["hora_falta"];
													$carga_horaria_curso = $det_curso["carga_horaria"];
												}
												if (!dbBool($det_serie["ultima_nota_define"]))
												{
													if($key < (count($this->array_modulos))  )
													{
														$soma_notas	+= $det_tipo_av_val['valor'];
														$media_sem_exame = true;
													}
													else
													{
														$media_sem_exame = false;
														$nota_exame = true;
														$exame_nota = $det_nota[$key]["nota"];
													}
												}
												else 
												{
													$media_sem_exame = true;
													$soma_notas = $det_tipo_av_val["valor"];
												}
											}
										}
										if (!dbBool($det_serie["ultima_nota_define"]))
										{
											if (!$nota_exame)
											{
												$media = $soma_notas / count($det_nota); //soh esta parte eh do codigo original
		//										$media_ = $media;
											}
											else
											{
												$media = ($soma_notas + $exame_nota * 2) / (count($det_nota)+1);
											}
										}
										else 
										{
											$media = $soma_notas;
										}
	
										$obj_media = new clsPmieducarTipoAvaliacaoValores();
										$det_media = $obj_media->lista($det_curso['ref_cod_tipo_avaliacao'],$det_curso['ref_sequencial'],null,null,$media,$media);
										if($det_media)
										{
											$det_media = array_shift($det_media);
											$media = $det_media['valor'];
											$media = sprintf("%01.1f",$media);
											$media = str_replace(".",",",$media);
										}
										if($media_sem_exame)
											$media_curso_ = $det_curso['media'];
										else
											$media_curso_ = $det_curso['media_exame'];
										if (str_replace(",", ".", $media) < $media_curso_)
										{
											$this->pdf->escreve_relativo("X", 410, ($base+3)+($linha*15), 250, 15, null, 8);//desempenho
											$reprovou_por_nota = true;
											$total_reprovados_nota++;
										}
									}
									if ($reprovou_por_falta && $reprovou_por_nota)
										break;
								}
							}
						}
					}
					else 
					{
						if ($det_matricula["aprovado"] == 1)
						{
							$this->pdf->escreve_relativo("X", 345, ($base+3)+($linha*15), 250, 15, null, 8);//aprovado
							$total_aprovados++;
						}
						else 
						{
							$this->pdf->escreve_relativo("X", 410, ($base+3)+($linha*15), 250, 15, null, 8);//desempenho
							$reprovou_por_nota = true;
							$total_reprovados_nota++;
						}
					}
					/*analfabeto*/
					$obj_aluno = new clsPmieducarAluno($det_matricula["ref_cod_aluno"]);
					$obj_aluno->setCamposLista("analfabeto");
					$det_aluno = $obj_aluno->detalhe();
					if ($det_aluno["analfabeto"] == 0) {
						$this->pdf->escreve_relativo("X", 507, ($base+3)+($linha*15), 250, 15, null, 8);//nao alfabetizado
						$total_analfabetos++;
					}
					else {
						$this->pdf->escreve_relativo("X", 545, ($base+3)+($linha*15), 250, 15, null, 8);//alfabetizado
						$total_nao_analfabetos++;
					}
					$this->pdf->linha_relativa(320, $base+($linha*15), 0, 15);
					$this->pdf->linha_relativa(380, $base+($linha*15), 0, 15);
					$this->pdf->linha_relativa(490, $base+($linha*15), 0, 15);
					$this->pdf->linha_relativa(530, $base+($linha*15), 0, 15);
					$this->pdf->linha_relativa(450, $base+($linha*15), 0, 15);
	
	
					$linha++;
				}
			}
		}
		//escrever total
		$this->pdf->linha_relativa(30, $base+($linha*15), 0, 15);

		$this->pdf->escreve_relativo("Total", 35, ($base+3)+($linha*15), 20, 15, null, 8);

		$this->pdf->escreve_relativo($total_aprovados, 345, ($base+3)+($linha*15), 250, 15, null, 8);//aprovado
		$this->pdf->escreve_relativo($total_reprovados_desempenho, 465, ($base+3)+($linha*15), 250, 15, null, 8);//desempenho
		$this->pdf->escreve_relativo($total_reprovados_nota, 410, ($base+3)+($linha*15), 250, 15, null, 8);//faltas	
		$this->pdf->escreve_relativo($total_analfabetos, 507, ($base+3)+($linha*15), 250, 15, null, 8);//nao alfabetizado
		$this->pdf->escreve_relativo($total_nao_analfabetos, 545, ($base+3)+($linha*15), 250, 15, null, 8);//alfabetizado
		
		$this->pdf->linha_relativa(60, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(320, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(380, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(490, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(530, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(450, $base+($linha*15), 0, 15);


		$this->pdf->linha_relativa(570, $base+($linha*15), 0, 15);

		$this->pdf->linha_relativa(30, $base+(($linha+1)*15), 540, 0);

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
		$this->pdf->escreve_relativo( "Instituição: {$this->nm_instituicao}", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola: {$this->nm_escola}",138, 70, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Turma/Série: {$this->nm_turma} - {$this->nm_serie}",112, 82, 300, 80, $fonte, 10, $corTexto, 'left' );

		//titulo
		$this->pdf->escreve_relativo( "RESULTADO FINAL I", 30, 95, 535, 80, $fonte, 14, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Ano Referência: {$this->ano}", 45, 100, 535, 80, $fonte, 10, $corTexto, 'left' );

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

function cmp($a, $b)
{
	return $a["modulo"] > $b["modulo"];
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
