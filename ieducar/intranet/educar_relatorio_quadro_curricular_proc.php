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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro Curricular" );
		$this->processoAp = "696";
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

		$obj_escola_instituicao = new clsPmieducarEscola();
		$lst_escola_instituicao = $obj_escola_instituicao->lista($this->ref_cod_escola, null, null, $this->ref_cod_instituicao, null, null, null, null, null, null,1);

		$this->pdf = new clsPDF("Registro de Matrículas - {$this->ano}", "Registro de Matrículas", "A4", "", false, false);

		foreach ($lst_escola_instituicao as $escola)
		{

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

			$obj_cursos = new clsPmieducarCurso();
			$lst_cursos = $obj_cursos->lista($this->ref_cod_curso, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );

			if($lst_cursos)
			{

				$x_quadrado = 30;
				$altura_caixa = 30;

				foreach ($lst_cursos as $curso)
				{
					$this->buscaDiasLetivos();

					$this->nm_curso = $curso['nm_curso'];

					$obj_serie_curso = new clsPmieducarSerie();
					$obj_serie_curso->setOrderby('etapa_curso asc');
					$lst_serie_curso = $obj_serie_curso->lista(null, null, null, $curso['cod_curso'], null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao, null, null, null, $this->ref_cod_escola );

					if($lst_serie_curso)
					{

						$this->page_y = 170;
						$x_quadrado = 30;

						if (!$page_open) {
							$this->pdf->OpenPage();
							$this->addCabecalho();
							$page_open = true;
						}

						foreach ($lst_serie_curso as $serie)
						{

							$obj_disc_serie = new clsPmieducarDisciplinaSerie();
							$lst_disc_serie = $obj_disc_serie->lista(null, $serie['cod_serie'], 1);

							if($lst_disc_serie)
							{

								$obj_turmas_serie = new clsPmieducarTurma();
								$obj_turmas_serie->setOrderby("nm_turma");
								$lst_turmas_serie = $obj_turmas_serie->lista(null, null, null, $serie['cod_serie'], $escola['cod_escola'], null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $curso['cod_curso'], $this->ref_cod_instituicao, null, null);

								if(!$lst_turmas_serie)
								{
									// verifica se a serie esta marcada em multiseriada
									$lst_turmas_serie = $obj_turmas_serie->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $curso['cod_curso'], $this->ref_cod_instituicao, null, null, $escola['cod_escola'], $serie['cod_serie']);
								}

								if($lst_turmas_serie)
								{
									foreach ($lst_turmas_serie as $turma) {

										$total_geral_horas = $total_geral_aulas_semana = 0;

										$x_quadrado = 30;

										$this->pdf->escreve_relativo( "{$serie['nm_serie']} - Turma: {$turma['nm_turma']}", 40, $this->page_y + 4, 258, $altura_caixa, $fonte, 12, $corTexto, 'left' );
										$this->page_y += $altura_caixa;

										$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa );
										$this->pdf->escreve_relativo( "Conteúdos Curriculares", 40, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
										$this->pdf->escreve_relativo( "A.S", 470, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
										$this->pdf->escreve_relativo( "H.R.", 525, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
										$this->page_y += $altura_caixa;

										if($this->page_y + $altura_caixa >= 800)
										{
											$this->pdf->quadrado_relativo( 30, $inicio_quadro, 70, $fim_quadro - $inicio_quadro );
											$this->pdf->escreve_relativo( "Núcleo Comum \ne Artigo 7º\n(Lei 5692/71)", 30 + 4, $inicio_quadro + ($fim_quadro - $inicio_quadro) / 3 - (count($lst_disc_serie) == 1 ? 8:0), 80, $altura_caixa, $fonte, 8, $corTexto, 'left' );

											$this->pdf->linha_relativa( 450, $inicio_quadro - $altura_caixa, 0, $fim_quadro - $inicio_quadro, '0.1');
											$this->pdf->linha_relativa( 505, $inicio_quadro - $altura_caixa, 0, $fim_quadro - $inicio_quadro, '0.1');

											$this->page_y = 170;

											$inicio_quadro = $this->page_y + $altura_caixa;

											$this->pdf->ClosePage();
											$this->pdf->OpenPage();

											$page_open = true;

											$this->addCabecalho();

											$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa );
											$this->pdf->escreve_relativo( "Conteúdos Curriculares", 40, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
											$this->pdf->escreve_relativo( "A.S", 470, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
											$this->pdf->escreve_relativo( "H.R.", 525, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
											$this->page_y += $altura_caixa;

										}

										$x_quadrado = 100;


										$obj_quadro_horario = new clsPmieducarQuadroHorario();
										$lst_quadro_horario = $obj_quadro_horario->lista(null, null, null, $turma['cod_turma'], null, null, null, null, 1);

										if(is_array($lst_quadro_horario))
										{
											$lst_quadro_horario = array_shift($lst_quadro_horario);
										}

										$obj_disc_semana = new clsPmieducarQuadroHorarioHorarios();
										$obj_disc_semana->setOrderby("dia_semana asc");


										/*********************************************************************/
										//disciplinas que estâo sendo cursadas, eliminando as não cursadas
										$sql = "SELECT distinct(ref_cod_disciplina) FROM pmieducar.quadro_horario_horarios
												WHERE ref_cod_quadro_horario = {$lst_quadro_horario["cod_quadro_horario"]}";
										
										$disciplinas_cursadas = array();
										$db = new clsBanco();
										$db->Consulta($sql);
										while ($db->ProximoRegistro())
										{
											list($ref_disciplina_cursada) = $db->Tupla();
											$disciplinas_cursadas[$ref_disciplina_cursada] = $ref_disciplina_cursada;
										}
										/*********************************************************************/
										
										
										$inicio_quadro = $this->page_y;
										foreach ($lst_disc_serie as $key => $disciplina)
										{
											if (array_search($disciplina["ref_cod_disciplina"], $disciplinas_cursadas))
											{
												$obj_disc = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
												$det_disc = $obj_disc->detalhe();
	
												$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 465, $altura_caixa );
												$this->pdf->escreve_relativo( "{$det_disc['nm_disciplina']}", $x_quadrado + 4, $this->page_y + 4, 350, $altura_caixa, $fonte, 10, $corTexto, 'left' );
	
												//-------
	
												unset($lst_disc_semana);
	
												if($lst_quadro_horario)
	
													$lst_disc_semana = $obj_disc_semana->lista($lst_quadro_horario['cod_quadro_horario'],$serie['cod_serie'], $this->ref_cod_escola, $disciplina['ref_cod_disciplina'], null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1);
	
												$total_dias_semana = 0;
	
												/**
												 * Calcula o total de horas da semana
												 */
												if($lst_disc_semana)
												{
	
													$total_semanas = $this->total_semanas;
	
													$total_dias_semana = count($lst_disc_semana);
	
	
												}
	
												$total_geral_horas += $det_disc['carga_horaria'];
												$total_geral_aulas_semana += $total_dias_semana;
	
												$total_horas =  sprintf("%02d:%02d",$det_disc['carga_horaria'],0);
	
	
												$this->pdf->escreve_relativo( sprintf("%02d","{$total_dias_semana}"), 451, $this->page_y + 10, 52, $altura_caixa, $fonte, 10, $corTexto, 'center' );
												$this->pdf->escreve_relativo( "{$total_horas}", 506, $this->page_y + 10, 65, $altura_caixa, $fonte, 10, $corTexto, 'center' );
	
												$fim_quadro = $this->page_y += $altura_caixa;
	
												if($this->page_y + $altura_caixa >= 800 && $key < count($lst_disc_serie)-1)
												{
													$x_quadrado = 30;
	
													$this->pdf->quadrado_relativo( 30, $inicio_quadro, 70, $fim_quadro - $inicio_quadro );
													$this->pdf->escreve_relativo( "Núcleo Comum \ne Artigo 7º\n(Lei 5692/71)", 30 + 4, $inicio_quadro + ($fim_quadro - $inicio_quadro) / 3 - ($key == 0 ? 8:0), 80, $altura_caixa, $fonte, 8, $corTexto, 'left' );
	
													$this->pdf->linha_relativa( 450, $inicio_quadro - $altura_caixa, 0, $fim_quadro - $inicio_quadro + $altura_caixa, '0.1');
													$this->pdf->linha_relativa( 505, $inicio_quadro - $altura_caixa, 0, $fim_quadro - $inicio_quadro + $altura_caixa, '0.1');
	
													$this->page_y = 170;
	
													$inicio_quadro = $this->page_y + $altura_caixa;
	
													$this->pdf->ClosePage();
													$this->pdf->OpenPage();
	
													$page_open = true;
	
													$this->addCabecalho();
	
													$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa );
													$this->pdf->escreve_relativo( "Conteúdos Curriculares", 40, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
													$this->pdf->escreve_relativo( "A.S", 470, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
													$this->pdf->escreve_relativo( "H.R.", 525, $this->page_y + 10, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
													$this->page_y += $altura_caixa;
	
													$x_quadrado = 100;
	
												}
											}
										}

										$this->pdf->quadrado_relativo( 30, $inicio_quadro, 70, $fim_quadro - $inicio_quadro );

										$this->pdf->linha_relativa( 450, $inicio_quadro - $altura_caixa, 0, $fim_quadro - $inicio_quadro + $altura_caixa, '0.1');
										$this->pdf->linha_relativa( 505, $inicio_quadro - $altura_caixa, 0, $fim_quadro - $inicio_quadro + $altura_caixa, '0.1');

										$this->pdf->escreve_relativo( "Núcleo Comum \ne Artigo 7º\n(Lei 5692/71)", 30 + 4, $inicio_quadro + ($fim_quadro - $inicio_quadro) / 3 - 8, 80, $altura_caixa, $fonte, 8, $corTexto, 'left' );

										$x_quadrado = 450;
										$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 115, $altura_caixa );
										$total_geral_horas =  sprintf("%02d:%02d",$total_geral_horas,0);
										$this->pdf->escreve_relativo( sprintf("%02d","{$total_geral_aulas_semana}"), 451, $this->page_y + 10, 52, $altura_caixa, $fonte, 10, $corTexto, 'center' );
										$this->pdf->escreve_relativo( "{$total_geral_horas}", 506, $this->page_y + 10, 65, $altura_caixa, $fonte, 10, $corTexto, 'center' );

										$this->pdf->linha_relativa( 505, $this->page_y, 0,$altura_caixa, '0.1');

										$this->page_y += $altura_caixa;
									}
								}

								//-------

							}
						}
					}

					if($page_open)
					{
						$this->pdf->ClosePage();
						$page_open = false;
					}

				}
			}

			if($page_open)
			{
				$this->pdf->ClosePage();
				$page_open = false;
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

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 535, 90 );

		$y_quadrado = 30;
		$x_quadrado = 100;
		$y_escrita  = $y_quadrado + 5;
		$altura_caixa = 30;

		//1 linha
		$this->pdf->quadrado_relativo( $x_quadrado, $y_quadrado, 465, $altura_caixa );
		$this->pdf->quadrado_relativo( $x_quadrado, $y_quadrado, 258, $altura_caixa );
		$y_quadrado += $altura_caixa;

		//2 linha
		$this->pdf->quadrado_relativo( $x_quadrado, $y_quadrado, 465, $altura_caixa );
		$this->pdf->quadrado_relativo( $x_quadrado, $y_quadrado, 258, $altura_caixa );
		$this->pdf->quadrado_relativo( $x_quadrado + 258, $y_quadrado, 110, $altura_caixa );
		$y_quadrado += $altura_caixa;

		//3 linha
		$this->pdf->quadrado_relativo( $x_quadrado, $y_quadrado, 465, $altura_caixa );
		$this->pdf->quadrado_relativo( $x_quadrado, $y_quadrado, 70, $altura_caixa );
		$this->pdf->quadrado_relativo( $x_quadrado + 70, $y_quadrado, 298, $altura_caixa );
		$y_quadrado += $altura_caixa;

		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 45, 100, 0.30 );

		$y_quadrado = 30;
		$y_escrita  = $y_quadrado + 13;

		//1 linha
		$this->pdf->escreve_relativo( "QUADRO CURRICULAR", 100, $y_escrita - 8, 268, $altura_caixa, $fonte, 17, $corTexto, 'center' );
		$this->pdf->escreve_relativo( "Curso", 355 + 5, $y_escrita - 13, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$this->nm_curso}", 360 + 10, $y_escrita, 356, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$y_quadrado += $altura_caixa;
		$y_escrita  = $y_quadrado + 15;

		//2 linha
		$this->pdf->escreve_relativo( "SRE", 100 + 5, $y_escrita - 14, 258, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$this->nm_instituicao}", 100 + 10,  $y_escrita, 258, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Município", 355 + 5, $y_escrita - 14, 100, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$this->nm_municipio}", 355 + 10, $y_escrita, 80, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Distrito/Localidade", 465 + 5, $y_escrita - 14, 100, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$this->nm_localidade}", 465 + 10, $y_escrita, 80, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$y_quadrado += $altura_caixa;
		$y_escrita  = $y_quadrado + 13;

		//3 linha
		$this->pdf->escreve_relativo( "Escola", 100 + 5, $y_escrita - 13, 100, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "$this->ref_cod_escola", 80, $y_escrita, 100, $altura_caixa, $fonte, 12, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Denominação", 170 + 5, $y_escrita - 13, 300, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$this->nm_escola}", 170 + 10, $y_escrita, 300, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Ano de Referência", 465 + 5, $y_escrita - 13, 140, $altura_caixa, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$this->ano}", 465 + 10, $y_escrita, 220, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$y_quadrado += $altura_caixa;
		$y_escrita  = $y_quadrado + 2;
		$altura_caixa = 20;

		//indicadores fixos
		$this->pdf->quadrado_relativo( 30, $y_quadrado, 535, $altura_caixa );
		$this->pdf->escreve_relativo( "Indicadores Fixos", 30, $y_escrita, 535, $altura_caixa, $fonte, 12, $corTexto, 'center' );

		$y_quadrado += $altura_caixa;
		$y_escrita  = $y_quadrado + 3;

		$this->pdf->quadrado_relativo( 30, $y_quadrado, 535, $altura_caixa );
		$this->pdf->escreve_relativo( "Número de Dias Letivos Anuais: ".$this->total_dias_uteis, 50, $y_escrita, 300, $altura_caixa, $fonte, 12, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Número de Semanas Letivas: ".$this->total_semanas, 350, $y_escrita, 382, $altura_caixa, $fonte, 12, $corTexto, 'left' );

		$this->pdf->escreve_relativo( date('d/m/Y'), 42, 105,100, 20, $fonte, 10, $corTexto, 'left' );


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

			 	if($DiaSemana != 0 && $DiaSemana != 6){
			 		if(!(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('n')))
			 			$totalDiasUteis++;
			 	}elseif(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('e'))
					$totalDiasUteis++;

			 	$DiaSemana++;

			 }


		}

		 $this->total_dias_uteis = $totalDiasUteis;
		 $this->total_semanas	 = $total_semanas;
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