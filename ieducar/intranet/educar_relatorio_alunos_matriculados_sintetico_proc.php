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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Alunos Benef&iacute;ios" );
		$this->processoAp = "707";
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
	var $ref_ref_cod_serie;
	var $ref_cod_serie;

	var $ano;

	var $nm_escola;
	var $nm_instituicao;
	var $nm_curso;
	var $nm_municipio;
	var $nm_localidade;

	var $pdf;

	var $page_y = 139;

	var $get_link;

	var $total_dias_uteis;
	var $total_semanas;

	var $primeiro_dia_semana;
	var $ultimo_dia_semana;

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
	var $escola_sem_avaliacao;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}
		
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

			$fonte = 'arial';
		$corTexto = '#000000';

		if ($this->escola_sem_avaliacao == 1) {
			$this->escola_sem_avaliacao = true;
		} elseif ($this->escola_sem_avaliacao == 2) {
			$this->escola_sem_avaliacao = false;
		} else {
			$this->escola_sem_avaliacao = null;
		}
		
		$obj_escola_instituicao = new clsPmieducarEscola();
		$lst_escola_instituicao = $obj_escola_instituicao->lista($this->ref_cod_escola, null, null, $this->ref_cod_instituicao, null, null, null, null, null, null,1, null, $this->escola_sem_avaliacao);

		$this->pdf = new clsPDF("Alunos Matriculados - Sintético - {$this->ano}", "Alunos Matriculados - Sintético", "A4", "", false, false);
		
		if ($this->is_padrao || $this->ano == 2007)
		{
			$this->semestre = null;
		}
		if (is_array($lst_escola_instituicao) && count($lst_escola_instituicao))
		{
			foreach ($lst_escola_instituicao as $escola)
			{
	
				$page_open = false;
	
				$this->ref_cod_escola = $escola['cod_escola'];
	
				if($this->ref_cod_escola){
	
					$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
					$det_escola = $obj_escola->detalhe();
					$this->nm_escola = $det_escola['nome'];
	
					$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
					$det_instituicao = $obj_instituicao->detalhe();
					$this->nm_instituicao = $det_instituicao['nm_instituicao'];
	
					if($det_escola['ref_idpes'])
					{
						$obj_endereco_escola = new clsEndereco($det_escola['ref_idpes']);
						$det_enderedo_escola = $obj_endereco_escola->detalhe();
	
						$this->nm_localidade = $this->nm_municipio = $det_enderedo_escola['cidade'];
	
						if(!$det_enderedo_escola)
						{
							$obj_endereco_externo_escola = new clsEnderecoExterno($det_escola['ref_idpes']);
							$det_enderedo_externo_escola = $obj_endereco_externo_escola->detalhe();
	
							$this->nm_localidade = $this->nm_municipio = $det_enderedo_externo_escola['cidade'];
						}
	
					}
					else
					{
						$obj_escola_complemento = new clsPmieducarEscolaComplemento($this->ref_cod_escola);
						$det_escola_complemento = $obj_escola_complemento->detalhe();
						$this->nm_localidade	= $this->nm_municipio = $det_escola_complemento['municipio'];
					}
	
				}
	
				$total_geral_escola_nao_enturmado_feminino  = 0;
				$total_geral_escola_nao_enturmado_masculino = 0;
	
				$total_geral_escola_enturmado_feminino  = 0;
				$total_geral_escola_enturmado_masculino = 0;
	
				$obj_cursos = new clsPmieducarCurso();
				$obj_cursos->setOrderby("cod_curso asc");
				$lst_cursos = $obj_cursos->lista($this->ref_cod_curso, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );
				
				if($lst_cursos)
				{
	
					foreach ($lst_cursos as $curso)
					{
	
						$obj_serie_curso = new clsPmieducarSerie();
						$obj_serie_curso->setOrderby('etapa_curso asc');
						$lst_serie_curso = $obj_serie_curso->lista($this->ref_cod_serie, null, null, $curso['cod_curso'], null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao, null, null, null, $this->ref_cod_escola );
	
						$existe_matriculas = false;
	
						if($lst_serie_curso)
						{
	
							$total = 0;
							foreach ($lst_serie_curso as $key_serie => $serie)
							{
	
								$obj_turmas = new clsPmieducarTurma();
								$lst_turmas = $obj_turmas->lista(null, null, null, $serie['cod_serie'], $escola['cod_escola'], null, null, null, null, null, null, null, null, null, 1, null,      null, null, null, null,
																 null, null, null, null, null, null, null, null, null, null, null, true);
								
								if (!$lst_turmas)
								{
									$obj_turmas = new clsPmieducarTurma();
									$lst_turmas = $obj_turmas->lista(null, null, null, null,null, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, null ,null, null, null, $escola['cod_escola'], $serie['cod_serie']);
								}
								
								if($lst_turmas)
								{
	
									if(!$page_open)
									{
										$x_quadrado = 30;
										$this->page_y = 80;
										$altura_caixa = 20;
										$this->pdf->OpenPage();
										$this->addCabecalho();
										$this->addCabecalho2();
	
										$page_open = true;
									}
	
	
									$existe_matriculas = true;
	
									$total_enturmados_turma_masculino = 0;
									$total_enturmados_turma_feminino  = 0;
	
									$total_nao_enturmados_turma_masculino = 0;
									$total_nao_enturmados_turma_feminino  = 0;
	
									foreach ($lst_turmas as $key_turma => $turma)
									{
	
										if($turma['hora_inicial'])
										{
											if($turma['hora_inicial'] <= '12:00')
												$turno = 'Matutino';
											elseif($turma['hora_inicial'] > '12:00' && $turma['hora_inicial'] <= '18:00')
												$turno = 'Vespert.';
											else
												$turno = 'Noturno';
										}
	
										$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa );
	
										$this->pdf->escreve_relativo( "{$escola['cod_escola']}", 25, $this->page_y + 5, 45, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->linha_relativa( 60, $this->page_y , 0, $altura_caixa, '0.1');
										$this->pdf->escreve_relativo( "{$curso['nm_curso']}", 67, $this->page_y + 5, 258, $altura_caixa, $fonte, 8, $corTexto, 'left' );
										$this->pdf->linha_relativa( 250, $this->page_y , 0, $altura_caixa, '0.1');
										$this->pdf->escreve_relativo( "{$serie['nm_serie']} / {$turma['nm_turma']}", 250, $this->page_y + 2 , 70, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->linha_relativa( 320, $this->page_y , 0, $altura_caixa, '0.1');
										$this->pdf->escreve_relativo( "{$turno}", 323, $this->page_y + 5, 258, $altura_caixa, $fonte, 8, $corTexto, 'left' );
										$this->pdf->linha_relativa( 360, $this->page_y , 0, $altura_caixa, '0.1');
	
																			
										$obj_matriculas_turma = new clsPmieducarMatriculaTurma();
										$lst_matriculas_turma = $obj_matriculas_turma->lista(null, $turma['cod_turma'], null, null, null, null, null, null, 1, $serie['cod_serie'], $curso['cod_curso'], $escola['cod_escola'], $this->ref_cod_instituicao, null, null, array(1,2,3), null, null, $this->ano, null, null, null, 1, true, null, null, null, null, $this->semestre);
	
										$enturmados_turma_masculino = 0;
										$enturmados_turma_feminino  = 0;
	
										if($lst_matriculas_turma)
										{
											$total_enturmados_turma_geral =  count($lst_matriculas_turma);
											
											//aqui verificar aluno que estao na multiseriada
											
											foreach ($lst_matriculas_turma as $matricula)
											{
												$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
												$det_matricula = $obj_matricula->detalhe();
	
												$obj_aluno = new clsPmieducarAluno($det_matricula['ref_cod_aluno']);
												$det_aluno = $obj_aluno->detalhe();
	
												$obj_pessoa = new clsFisica($det_aluno['ref_idpes']);
												$det_pessoa = $obj_pessoa->detalhe();
	
												/**
												 * verifica se o aluno possui transferencia
												 * e nao exibe na enturmacao
												 */
												//$obj_transf = new clsPmieducarTransferenciaSolicitacao();
												//$lst_transf = $obj_transf->lista(null,null,null,null,null,$matricula['ref_cod_matricula'],null,null,null,null,null,null,null,null,null,null,$this->ref_cod_escola,$this->ref_ref_cod_serie);
	
												//if($lst_transf)
												//	continue;
	
												if( strtoupper($det_pessoa['sexo']) == 'M')
													$enturmados_turma_masculino++;
												else
													$enturmados_turma_feminino++;
											}
	
										}
	
										$total_enturmados_turma_masculino += $enturmados_turma_masculino;
										$total_enturmados_turma_feminino  += $enturmados_turma_feminino;
	
										$total_geral_escola_enturmado_masculino += $enturmados_turma_masculino;
										$total_geral_escola_enturmado_feminino  += $enturmados_turma_feminino;
	
										/***************************INVERTIDO ABAIXO*******************************************************/
										//enturmados
										/*$this->pdf->escreve_relativo( "{$enturmados_turma_feminino}", 355, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->escreve_relativo( "{$enturmados_turma_masculino}", 385, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );*/
										$this->pdf->escreve_relativo( "{$enturmados_turma_masculino}", 355, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->escreve_relativo( "{$enturmados_turma_feminino}", 385, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										/**************************************************************************************************/
										
										$this->pdf->escreve_relativo($enturmados_turma_masculino + $enturmados_turma_feminino, 425, $this->page_y + 5, 30, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->linha_relativa( 460, $this->page_y , 0, $altura_caixa, '0.1');
	
										$this->pdf->linha_relativa( 390, $this->page_y, 0, $altura_caixa, '0.1');
	
										$this->pdf->linha_relativa( 420, $this->page_y , 0, $altura_caixa, '0.1');
	
	
										//$obj_matriculas = new clsPmieducarMatricula();
	//									$lst_matriculas = $obj_matriculas->lista(null, null, $escola['cod_escola'], $serie['cod_serie'], null, null, null, null, null, null, null, null, 1, $this->ano, $curso['cod_curso'], $this->ref_cod_instituicao );
										if(!$executou)
										{
											$obj_nao_enturmados = new clsPmieducarMatriculaTurma();
											$lst_nao_enturmados = $obj_nao_enturmados->dadosAlunosNaoEnturmados($escola['cod_escola'],$serie['cod_serie'],$curso['cod_curso'], $this->ano, true);
											$executou = true;
											//$total_nao_enturmados_turma_masculino = $total_nao_enturmados_turma_feminino = 0;
											if($lst_nao_enturmados)
											{
												foreach ($lst_nao_enturmados as $matricula)
												{
	
													////$obj_aluno = new clsPmieducarAluno($matricula['ref_cod_aluno']);
													//$det_aluno = $obj_aluno->detalhe();
	
													//$obj_pessoa = new clsFisica($det_aluno['ref_idpes']);
													//$det_pessoa = $obj_pessoa->detalhe();
	
													if( strtoupper($matricula['sexo']) == 'M')
														$total_nao_enturmados_turma_masculino++;
													else
														$total_nao_enturmados_turma_feminino++;
	
												}
	
											}
										}
	
										if($this->page_y + $altura_caixa >= 800)
										{
	
											$this->page_y = 80;
	
											$this->pdf->ClosePage();
											$this->pdf->OpenPage();
	
											$page_open = true;
	
											$this->addCabecalho();
	
											$this->addCabecalho2();
	
	
										}
	
										$this->page_y += $altura_caixa;
									}
	
									if($key_serie < count($lst_serie_curso) )
									{
										$mult = count($lst_turmas);
	
										$centraliza = ($altura_caixa * ($mult + 1)) / 2;
	
										$this->pdf->quadrado_relativo( 460, $this->page_y - $altura_caixa * $mult , 105, $altura_caixa * $mult );
										$this->pdf->linha_relativa( 498, $this->page_y - $altura_caixa * $mult, 0, $altura_caixa * ($mult ), '0.1');
	
										//nao enturmados
										$total_geral_escola_nao_enturmado_feminino += $tot_fem = $total_nao_enturmados_turma_feminino ;
										$total_geral_escola_nao_enturmado_masculino += $tot_masc = $total_nao_enturmados_turma_masculino ;
										/**********************************INVERTIDO********************************************/
										/*$this->pdf->escreve_relativo( $tot_fem , 463, $this->page_y + 5 - $centraliza , 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->escreve_relativo( $tot_masc, 500, $this->page_y + 5 - $centraliza, 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );*/
										$this->pdf->escreve_relativo( $tot_masc , 463, $this->page_y + 5 - $centraliza , 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										$this->pdf->escreve_relativo( $tot_fem, 500, $this->page_y + 5 - $centraliza, 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );
										/***************************************************************************************/
										
										$this->pdf->linha_relativa( 538, $this->page_y - $altura_caixa * $mult, 0, $altura_caixa * $mult, '0.1');
										$this->pdf->escreve_relativo( $tot_fem + $tot_masc, 530, $this->page_y + 5 - $centraliza , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
									}
	
								}
	
							}
						}
					}
						
				}
	
				
				if($page_open)
				{		
					
					//total geral
					$this->pdf->quadrado_relativo( 320, $this->page_y , 40, $altura_caixa);
					$this->pdf->escreve_relativo( "TOTAL", 327, $this->page_y + 5, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
					$this->pdf->quadrado_relativo( 360, $this->page_y , 205, $altura_caixa);
					
					/***************************************INVERTIDO******************************************/
					//enturmados
					/*$this->pdf->escreve_relativo( "{$total_geral_escola_enturmado_feminino}", 355, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					$this->pdf->escreve_relativo( "{$total_geral_escola_enturmado_masculino}", 385, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );*/
					$this->pdf->escreve_relativo( "{$total_geral_escola_enturmado_masculino}", 355, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					$this->pdf->escreve_relativo( "{$total_geral_escola_enturmado_feminino}", 385, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					/******************************************************************************************/
					
					$this->pdf->escreve_relativo($total_geral_escola_enturmado_feminino + $total_geral_escola_enturmado_masculino, 425, $this->page_y + 5, 30, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					$this->pdf->linha_relativa( 460, $this->page_y , 0, $altura_caixa, '0.1');
	
					$this->pdf->linha_relativa( 390, $this->page_y, 0, $altura_caixa, '0.1');
	
					$this->pdf->linha_relativa( 420, $this->page_y , 0, $altura_caixa, '0.1');
	
					$this->pdf->linha_relativa( 498, $this->page_y , 0, $altura_caixa , '0.1');
	
					/***************************************INVERTIDO******************************************/
					//nao enturmados
					/*$this->pdf->escreve_relativo( "$total_geral_escola_nao_enturmado_feminino", 463, $this->page_y + 5 , 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					$this->pdf->escreve_relativo( "$total_geral_escola_nao_enturmado_masculino", 500, $this->page_y + 5, 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );*/
					$this->pdf->escreve_relativo( "$total_geral_escola_nao_enturmado_masculino", 463, $this->page_y + 5 , 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					$this->pdf->escreve_relativo( "$total_geral_escola_nao_enturmado_feminino", 500, $this->page_y + 5, 35, $altura_caixa, $fonte, 8, $corTexto, 'center' );
					/******************************************************************************************/
					
					$this->pdf->linha_relativa( 538, $this->page_y , 0, $altura_caixa , '0.1');
					$this->pdf->escreve_relativo( $total_geral_escola_nao_enturmado_masculino + $total_geral_escola_nao_enturmado_feminino, 530, $this->page_y + 5 , 40, $altura_caixa, $fonte, 8, $corTexto, 'center' );
	
					if($page_open && $existe_matriculas)
					{
						$this->pdf->ClosePage();
						$page_open = false;
					}
				}
				
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
		else 
		{
				echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');}</script>";
				echo "<script>
							alert('Nenhuma informação a ser apresentada');
							window.parent.fechaExpansivel('div_dinamico_'+(window.parent.DOM_divs.length-1));
					  </script>";
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
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",136, 70, 300, 80, $fonte, 10, $corTexto, 'left' );

		//titulo
		$this->pdf->escreve_relativo( "Alunos Matriculados - Sintético", 30, 85, 535, 80, $fonte, 14, $corTexto, 'center' );

		//Data
		$mes = date('n');
		$this->pdf->escreve_relativo( "{$this->meses_do_ano[$mes]}/{$this->ano}", 45, 100, 535, 80, $fonte, 10, $corTexto, 'left' );


	}

	function addCabecalho2()
	{
		$fonte = 'arial';
		$corTexto = '#000000';
		$x_quadrado = 30;
		$altura_caixa = 40;

		$this->page_y += $altura_caixa;

		$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa );
		$this->pdf->escreve_relativo( "Unid.", 33, $this->page_y + 13, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 60, $this->page_y , 0, $altura_caixa, '0.1');
		$this->pdf->escreve_relativo( "Curso", 145, $this->page_y + 13, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 250, $this->page_y , 0, $altura_caixa, '0.1');
		$this->pdf->escreve_relativo( "Série/\nAno/\nEtapa", 274, $this->page_y + 5, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 320, $this->page_y , 0, $altura_caixa, '0.1');
		$this->pdf->escreve_relativo( "Turno", 327, $this->page_y + 13, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 360, $this->page_y , 0, $altura_caixa, '0.1');

		$this->pdf->escreve_relativo( "Enturmados", 363, $this->page_y + 7, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );

		/*********************************TRECHO COMENTADO SERA REESCRITO ABAIXO DELE**************************************/
		/*$this->pdf->escreve_relativo( "Fem", 365, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 360, $this->page_y +23, 60, 0, '0.1');
		$this->pdf->linha_relativa( 390, $this->page_y +23, 0, 17, '0.1');
		$this->pdf->escreve_relativo( "Masc", 394, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 420, $this->page_y , 0, $altura_caixa, '0.1');*/
		$this->pdf->escreve_relativo( "Masc", 365, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 360, $this->page_y +23, 60, 0, '0.1');
		$this->pdf->linha_relativa( 390, $this->page_y +23, 0, 17, '0.1');
		$this->pdf->escreve_relativo( "Fem", 394, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 420, $this->page_y , 0, $altura_caixa, '0.1');
		/******************************************************************************************************************/

		//$this->pdf->linha_relativa( 421, $this->page_y +23, 40, 0, '0.1');
		$this->pdf->escreve_relativo( "Total", 429, $this->page_y + 13, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 460, $this->page_y , 0, $altura_caixa, '0.1');

		$this->pdf->escreve_relativo( "Não Enturmados", 463, $this->page_y + 7, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );

		/*********************************TRECHO COMENTADO SERA REESCRITO ABAIXO DELE**************************************/
		/*$this->pdf->escreve_relativo( "Fem", 470, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 460, $this->page_y +23, 78, 0, '0.1');
		$this->pdf->linha_relativa( 498, $this->page_y +23, 0, 17, '0.1');
		$this->pdf->escreve_relativo( "Masc", 505, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );*/
		$this->pdf->escreve_relativo( "Masc", 470, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		$this->pdf->linha_relativa( 460, $this->page_y +23, 78, 0, '0.1');
		$this->pdf->linha_relativa( 498, $this->page_y +23, 0, 17, '0.1');
		$this->pdf->escreve_relativo( "Fem", 505, $this->page_y + 25, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );
		/******************************************************************************************************************/

		$this->pdf->linha_relativa( 538, $this->page_y , 0, $altura_caixa, '0.1');
		$this->pdf->escreve_relativo( "Total", 540, $this->page_y + 13, 258, $altura_caixa, $fonte, 9, $corTexto, 'left' );

		$this->page_y += $altura_caixa;

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
