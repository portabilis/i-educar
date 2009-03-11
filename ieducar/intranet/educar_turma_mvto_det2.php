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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Turma" );
		$this->processoAp = "650";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_cod_infra_predio_comodo;
	var $nm_turma;
	var $sgl_turma;
	var $max_aluno;
	var $multiseriada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_turma_tipo;
	var $hora_inicial;
	var $hora_final;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;

	var $ref_cod_instituicao;
	var $ref_cod_curso;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Turma - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_turma=$_GET["cod_turma"];

		$tmp_obj = new clsPmieducarTurma();
		$lst_obj = $tmp_obj->lista( $this->cod_turma );
		$registro = array_shift($lst_obj);

		if( ! $registro )
		{
			header( "location: educar_turma_mvto_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarTurmaTipo" ) )
		{
			$obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo( $registro["ref_cod_turma_tipo"] );
			$det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
			$registro["ref_cod_turma_tipo"] = $det_ref_cod_turma_tipo["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_turma_tipo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarTurmaTipo\n-->";
		}

		if( class_exists( "clsPmieducarInfraPredioComodo" ) )
		{
			$obj_ref_cod_infra_predio_comodo = new clsPmieducarInfraPredioComodo( $registro["ref_cod_infra_predio_comodo"] );
			$det_ref_cod_infra_predio_comodo = $obj_ref_cod_infra_predio_comodo->detalhe();
			$registro["ref_cod_infra_predio_comodo"] = $det_ref_cod_infra_predio_comodo["nm_comodo"];
		}
		else
		{
			$registro["ref_cod_infra_predio_comodo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInfraPredioComodo\n-->";
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		if( class_exists( "clsPmieducarEscola" ) )
		{
			$this->ref_ref_cod_escola = $registro["ref_ref_cod_escola"];
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$registro["ref_ref_cod_escola"] = $det_ref_cod_escola["nome"];
		}
		else
		{
			$registro["ref_ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}

		if( class_exists( "clsPmieducarCurso" ) )
		{
			$this->ref_cod_curso = $registro["ref_cod_curso"];
			$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
			$padrao_ano_escolar = $det_ref_cod_curso["padrao_ano_escolar"];
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
		}
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$this->ref_ref_cod_serie = $registro["ref_ref_cod_serie"];
			$obj_ser = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_ser = $obj_ser->detalhe();
			$registro["ref_ref_cod_serie"] = $det_ser["nm_serie"];
		}
		else
		{
			$registro["ref_ref_cod_serie"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $registro["ref_cod_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
			}
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $registro["ref_ref_cod_escola"] )
			{
				$this->addDetalhe( array( "Escola", "{$registro["ref_ref_cod_escola"]}") );
			}
		}
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $registro["ref_ref_cod_serie"] )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$registro["ref_ref_cod_serie"]}") );
		}
		if( $registro["ref_cod_infra_predio_comodo"] )
		{
			$this->addDetalhe( array( "Sala", "{$registro["ref_cod_infra_predio_comodo"]}") );
		}
		if( $registro["ref_cod_turma_tipo"] )
		{
			$this->addDetalhe( array( "Tipo de Turma", "{$registro["ref_cod_turma_tipo"]}") );
		}
		if( $registro["nm_turma"] )
		{
			$this->addDetalhe( array( "Turma", "{$registro["nm_turma"]}") );
		}
		if( $registro["sgl_turma"] )
		{
			$this->addDetalhe( array( "Sigla", "{$registro["sgl_turma"]}") );
		}
		if( $registro["max_aluno"] )
		{
			$this->addDetalhe( array( "M&aacute;ximo de Alunos", "{$registro["max_aluno"]}") );
		}
//		if( $registro["multiseriada"] )
//		{
			if ( $registro["multiseriada"] == 1 )
				$registro["multiseriada"] = "sim";
			else
				$registro["multiseriada"] = "n&atilde;o";

			$this->addDetalhe( array( "Multi-Seriada", "{$registro["multiseriada"]}") );
//		}
		if ($padrao_ano_escolar == 1)
		{
			if( $registro["hora_inicial"] )
			{
				$registro["hora_inicial"] = date("H:i", strtotime( $registro["hora_inicial"]));
				$this->addDetalhe( array( "Hora Inicial", "{$registro["hora_inicial"]}") );
			}
			if( $registro["hora_final"] )
			{
				$registro["hora_final"] = date("H:i", strtotime( $registro["hora_final"]));
				$this->addDetalhe( array( "Hora Final", "{$registro["hora_final"]}") );
			}
			if( $registro["hora_inicio_intervalo"] )
			{
				$registro["hora_inicio_intervalo"] = date("H:i", strtotime( $registro["hora_inicio_intervalo"]));
				$this->addDetalhe( array( "Hora In&iacute;cio Intervalo", "{$registro["hora_inicio_intervalo"]}") );
			}
			if( $registro["hora_fim_intervalo"] )
			{
				$registro["hora_fim_intervalo"] = date("H:i", strtotime( $registro["hora_fim_intervalo"]));
				$this->addDetalhe( array( "Hora Fim Intervalo", "{$registro["hora_fim_intervalo"]}") );
			}

		}
		else if ($padrao_ano_escolar == 0)
		{
			$obj = new clsPmieducarTurmaModulo();
			$obj->setOrderby("data_inicio ASC");
			$lst = $obj->lista( $this->cod_turma );

			if ($lst)
			{
				$tabela = "<TABLE>
						       <TR align=center>
						           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
						           <TD bgcolor=#A1B3BD><B>Data In&iacute;cio</B></TD>
						           <TD bgcolor=#A1B3BD><B>Data Fim</B></TD>
						       </TR>";
				$cont = 0;

				foreach ( $lst AS $valor )
				{
					if ( ($cont % 2) == 0 )
					{
						$color = " bgcolor=#E4E9ED ";
					}
					else
					{
						$color = " bgcolor=#FFFFFF ";
					}
					$obj_modulo = new clsPmieducarModulo( $valor["ref_cod_modulo"] );
					$det_modulo = $obj_modulo->detalhe();
					$nm_modulo = $det_modulo["nm_tipo"];

					$valor["data_inicio"] = dataFromPgToBr($valor["data_inicio"]);
					$valor["data_fim"] = dataFromPgToBr($valor["data_fim"]);

					$tabela .= "<TR>
								    <TD {$color} align=left>{$nm_modulo}</TD>
								    <TD {$color} align=left>{$valor["data_inicio"]}</TD>
								    <TD {$color} align=left>{$valor["data_fim"]}</TD>
								</TR>";
					$cont++;
				}
				$tabela .= "</TABLE>";
			}
			if( $tabela )
			{
				$this->addDetalhe( array( "M&oacute;dulo", "{$tabela}") );
			}

			$dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );

			$obj = new clsPmieducarTurmaDiaSemana();
			$lst = $obj->lista( null, $this->cod_turma );
			if ($lst)
			{
				$tabela1 = "<TABLE>
						       <TR align=center>
						           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
						           <TD bgcolor=#A1B3BD><B>Hora Inicial</B></TD>
						           <TD bgcolor=#A1B3BD><B>Hora Final</B></TD>
						       </TR>";
				$cont = 0;

				foreach ( $lst AS $valor )
				{
					if ( ($cont % 2) == 0 )
					{
						$color = " bgcolor=#E4E9ED ";
					}
					else
					{
						$color = " bgcolor=#FFFFFF ";
					}

					$valor["hora_inicial"] = date("H:i", strtotime( $valor["hora_inicial"]));
					$valor["hora_final"] = date("H:i", strtotime( $valor["hora_final"]));

					$tabela1 .= "<TR>
								    <TD {$color} align=left>{$dias_da_semana[$valor["dia_semana"]]}</TD>
								    <TD {$color} align=left>{$valor["hora_inicial"]}</TD>
								    <TD {$color} align=left>{$valor["hora_final"]}</TD>
								</TR>";
					$cont++;
				}
				$tabela1 .= "</TABLE>";
			}
			if( $tabela1 )
			{
				$this->addDetalhe( array( "Dia da Semana", "{$tabela1}") );
			}

		}
//		$obj = new clsPmieducarDisciplinaSerie();
		$obj = new clsPmieducarEscolaSerieDisciplina();
		$lst = $obj->lista( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola,null,1 );

		if ($lst)
		{
			$tabela3 = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Disciplinas</B></TD>
					       </TR>";
			$cont = 0;

			foreach ( $lst AS $valor )
			{
				if ( ($cont % 2) == 0 )
				{
					$color = " bgcolor=#E4E9ED ";
				}
				else
				{
					$color = " bgcolor=#FFFFFF ";
				}
				$obj_disciplina = new clsPmieducarDisciplina( $valor["ref_cod_disciplina"] );
				$obj_disciplina->setOrderby("nm_disciplina ASC");
				$obj_disciplina_det = $obj_disciplina->detalhe();
				$nm_disciplina = $obj_disciplina_det["nm_disciplina"];

				$tabela3 .= "<TR>
							    <TD {$color} align=left>{$nm_disciplina}</TD>
							</TR>";
				$cont++;
			}
			$tabela3 .= "</TABLE>";
		}
		if( $tabela3 )
		{
			$this->addDetalhe( array( "Disciplinas", "{$tabela3}") );
		}


		$tabela_alunos = "";
		$obj = new clsPmieducarMatriculaTurma();
		$obj->setOrderby("nome ASC");
		$lst = $obj->lista( null, $this->cod_turma, null, null, null, null, null, null, 1,null,null,null,null,null,null,3,null,null,null,null,false,null,1,true );
		if ($lst)
		{
			$tabela_alunos = "<table>
					       <tr align=center>
					           <td bgcolor=#A1B3BD><b>Alunos</b></td>
					       </tr>";
			$cont = 0;

			foreach ( $lst AS $valor )
			{
				if ( ($cont % 2) == 0 )
				{
					$color = " bgcolor=#E4E9ED ";
				}
				else
				{
					$color = " bgcolor=#FFFFFF ";
				}
				$tabela_alunos .= "<tr>
							    <td {$color} align=left>{$valor["ref_cod_matricula"]} {$valor["nome"]}</td>
							</tr>";
				$cont++;
			}
			$tabela_alunos .= "</table>";
		}
		if( $tabela_alunos )
		{
			$this->addDetalhe( array( "Alunos", "{$tabela_alunos}") );
		}


		if ( $obj_permissoes->permissao_cadastra( 650, $this->pessoa_logada, 7 ) )
		{
//			$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
//			$det_escola_ano_letivo = $obj_escola_ano_letivo->detalhe();
//
//			Carrega o ano letivo
//			if ( is_array( $det_escola_ano_letivo ) )
//			{
//				$ano_letivo = $det_escola_ano_letivo["ano"];
//			}
//
//			$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
//			$obj_ano_letivo_modulo->setOrderby( "data_fim" );
//			$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista( $ano_letivo, $this->ref_ref_cod_escola );

//--------------------------------------------------------------------------------------------------------------------------
			$obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
			$det_curso = $obj_curso->detalhe();

//			Verifica se vai seguir o padrão do ano escolar da escola
			if ( $det_curso["padrao_ano_escolar"] == 1 )
			{
//				echo "teste";
				$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
				$det_escola_ano_letivo = $obj_escola_ano_letivo->detalhe();

//				Carrega o ano letivo
				if ( is_array( $det_escola_ano_letivo ) )
				{
//					echo "teste";
					$ano_letivo = $det_escola_ano_letivo["ano"];
				}

				$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
				$obj_ano_letivo_modulo->setOrderby( "data_fim" );
				$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista( $ano_letivo, $this->ref_ref_cod_escola );

//				echo "<pre>";
//				print_r( $lst_ano_letivo_modulo );
				if ( is_array( $lst_ano_letivo_modulo ) )
				{
					$obj_disciplina_serie = new clsPmieducarDisciplinaSerie();
					$lst_disciplina_serie = $obj_disciplina_serie->lista( null, $this->ref_ref_cod_serie );

//					Carrega a quantidade de disciplinas da turma
					$qtd_disciplinas = count( $lst_disciplina_serie );

//					echo "<pre>";
//					print_r($qtd_disciplinas);
					if ( $lst_turma_disciplina )
					{
						foreach ( $lst_disciplina_serie as $disciplina )
						{
//							Carrega o código das disciplinas da turma
							$this->cod_disciplinas[] = $disciplina["ref_cod_disciplina"];
						}

//						Carrega a quantidade de módulos do ano letivo
						$qtd_modulos = count( $lst_ano_letivo_modulo );
						$cont = 1;

						if ( is_array( $lst_ano_letivo_modulo ) )
						{
							$obj_turma_modulo = new clsPmieducarTurmaModulo();

//							Busca em qual módulo a turma está
//							$resultado = $obj_turma_modulo->numModulo( $cont, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_disciplinas, $this->cod_turma, $this->cod_turma );

							$obj_matriculas = new clsPmieducarDisciplinaSerie();
							echo $this->ref_ref_cod_serie;
							$lst_matriculas = $obj_matriculas->lista( null, $this->ref_ref_cod_serie, 1 );

							$resultado = 0;

							if ( is_array( $lst_matriculas ) )
							{
								foreach ( $lst_matriculas as $registro )
								{
									$obj_nota_aluno = new clsPmieducarNotaAluno();
									$aux_min = $obj_nota_aluno->retornaModuloAluno( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $registro["ref_cod_matricula"] );
									if ( $aux_min === false )
									{

									}
									$aux_min = $aux_min + 1;

									if ( $resultado == 0 )
									{
										$resultado = $aux_min;
									}
									else if ( $resultado > $aux_min )
									{
										$resultado = $aux_min;
									}
								}
							}

							$this->num_modulo = $resultado;

							foreach ( $lst_ano_letivo_modulo as $registro )
							{
//								Verifica se a turma está num módulo da turma
								if ( ( $resultado ) == $registro["sequencial"] )
								{
									$obj_modulo 	  = new clsPmieducarModulo( $registro["ref_cod_modulo"] );
									$det_modulo 	  = $obj_modulo->detalhe();

//									Carrega o nome do módulo no qual a turma se encontra
//									$this->modulo 	  = $det_modulo["nm_tipo"];

//									Carrega o número do módulo em que a turma está
									$num_modulo = ( $resultado );
									break;
								}

//								Verifica se a turma está no módulo de exame
								else if ( ( $resultado ) > $qtd_modulos )
								{
//									Carrega o nome do módulo no qual a turma se encontra como "Exame"
									$modulo 	  = "Exame";

//									Carrega o número do módulo igual a quantidade de módulos da turma mais 1
									$num_modulo = ( $resultado );
									break;
								}
								$cont++;
							}
						}
					}
				}
			}

//			Escopo de instruções a serem executadas, caso a turma não siga o padrão ano letivo
			else
			{
				$obj_turma_modulo = new clsPmieducarTurmaModulo();
				$obj_turma_modulo->setOrderby( "data_fim" );
				$lst_turma_modulo = $obj_turma_modulo->lista( $this->cod_turma );

//				$obj_matriculas = new clsPmieducarMatriculaTurma();
//				$lst_matriculas = $obj_matriculas->lista( null, $this->cod_turma, null, null, null, null, null, null, 1, $this->ref_ref_cod_serie, $this->ref_cod_curso, $this->ref_ref_cod_escola );

				$obj_disciplina_serie = new clsPmieducarDisciplinaSerie();
				$lst_disciplina_serie = $obj_disciplina_serie->lista( null, $this->ref_ref_cod_serie,1 );

				$resultado = 0;

				if ( is_array( $lst_disciplina_serie ) )
				{
					foreach ( $lst_disciplina_serie as $registro )
					{
						$obj_nota_aluno = new clsPmieducarNotaAluno();
						$aux_min = $obj_nota_aluno->retornaModuloAluno( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $registro["ref_cod_matricula"] );
						$aux_min = $aux_min + 1;

						if ( $resultado == 0 )
						{
							$resultado = $aux_min;
						}
						else if ( $resultado > $aux_min )
						{
							$resultado = $aux_min;
						}
					}
				}

				$this->num_modulo = $resultado;

				if ( is_array($lst_turma_modulo) )
				{
//					Carrega a quantidade de módulos da turma
					$qtd_modulos = count( $lst_turma_modulo );
					$cont = 1;

					foreach ( $lst_turma_modulo as $registro )
					{
						$obj_turma_modulo = new clsPmieducarTurmaModulo();

//						Busca em qual módulo a turma está
						$resultado = $obj_turma_modulo->numModulo( $cont, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_disciplinas, $this->cod_turma, $this->cod_turma );

//						Verifica se a turma está num módulo da turma
						if ( ( $resultado ) == $cont )
						{
							$obj_modulo 	  = new clsPmieducarModulo( $registro["ref_cod_modulo"] );
							$det_modulo 	  = $obj_modulo->detalhe();

//							Carrega o nome do módulo no qual a turma se encontra
//							$this->modulo 	  = $det_modulo["nm_tipo"];

//							Carrega o número do módulo no qual a turma se encontra
							$num_modulo = ( $resultado );
							break;
						}

//						Verifica se a turma está no módulo de exame
						else if ( ( $resultado ) > $qtd_modulos )
						{
//							Carrega o nome do módulo no qual a turma se encontra como "Exame"
//							$modulo 	  = "Exame";

//							Carrega o número do módulo no qual a turma se encontra igual ao número de módulos da turma mais 1
							$num_modulo = ( $resultado );
							break;
						}
						$cont++;
					}
				}
			}

//--------------------------------------------------------------------------------------------------------------------------

//			Carrega a quantidade de módulos do ano letivo
//			$qtd_modulos = count( $lst_ano_letivo_modulo );

			$obj_nota_aluno = new clsPmieducarNotaAluno();

//			Carrega as médias de cada disciplina de cada aluno
//echo "{$this->ref_ref_cod_serie}, {$this->ref_ref_cod_escola}, {$this->cod_turma}, {$this->cod_turma}, {$qtd_modulos}, {$this->ref_cod_curso}, true, true<br>";
//			$lst_exame		= $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_turma, $this->cod_turma, $qtd_modulos, $this->ref_cod_curso, true, true );

//			echo "<pre>";
//			print_r( $lst_exame );

			$obj_nota_aluno = new clsPmieducarNotaAluno();
			$lst_nota_aluno = $obj_nota_aluno->lista( null, null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, null, $this->cod_turma, null, $this->cod_turma, null, null, null, null, null, null, 1 );

//			echo "<pre>";
//			print_r( $lst_nota_aluno );

			if ( is_array( $lst_nota_aluno ) )
			{
				$qtd_nota_aluno = count( $lst_nota_aluno );
			}
			else
			{
				$qtd_nota_aluno = 0;
			}

//			echo "<pre>";
//			print_r( $lst_exame );
			if ( is_array( $lst_exame ) )
			{
				foreach ( $lst_exame as $exame )
				{
					$obj_notas = new clsPmieducarNotaAluno();

//					Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
					$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $exame["disc_ref_ref_cod_disciplina"], $this->cod_turma, $this->cod_turma );

					if ( $lst_notas === false )
					{
//						$this->mensagem( "Erro ao verificar as notas dos alunos!<br>" );
					}

					$obj_matricula = new clsPmieducarMatricula( $exame["ref_ref_cod_matricula"] );
					$det_matricula = $obj_matricula->detalhe();

					if ( is_array( $det_matricula ) )
					{
						//echo "( {$lst_notas} < {$qtd_modulos} && {$det_matricula["aprovado"]} == 3 ) || ( {$lst_notas} == {$qtd_modulos} && {$det_matricula["aprovado"]} == 7 )<br>";
						if ( ( $lst_notas < $qtd_modulos && $det_matricula["aprovado"] == 3 ) || ( $lst_notas == $qtd_modulos && $det_matricula["aprovado"] == 7 ) )
						{
							$exibe_bt_adicionar = "S";
						}
					}
				}
			}
			else if ( $qtd_nota_aluno == 0 )
			{
				$obj_matricula_turma = new clsPmieducarMatriculaTurma();
				$lst_matricula_turma = $obj_matricula_turma->lista( null, $this->cod_turma );
				if ( is_array( $lst_matricula_turma ) )
				{
					$exibe_bt_adicionar = "S";
				}
			}
			else
			{
				$obj_turma_modulo = new clsPmieducarTurmaModulo();
				$fimAnoLetivo = $obj_turma_modulo->fimAno( $this->cod_turma, $qtd_modulos );

				if ( $fimAnoLetivo != "S" )
				{
					$exibe_bt_adicionar = "S";
				}
			}

//			$obj_matricula = new clsPmieducarMatricula();
//			$lst_matricula = $obj_matricula->lista( null, null, $this->ref_ref_cod_escola, $this->ref_ref_cod_serie, null, null, null, 7, null, null, null, null, 1, null, $this->ref_cod_curso, $this->ref_cod_instituicao, null, null, null, null );
//
//			if ( is_array( $lst_matricula ) )
//			{
//				$exibe_bt_adicionar = "S";
//			}

			if ( $exibe_bt_adicionar == "S" )
			{
				$this->array_botao[] 	 = "Adicionar Nota";
				$this->array_botao_url[] = "educar_turma_nota_cad.php?ref_cod_turma={$this->cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}";
			}

			if ( $num_modulo > $qtd_modulos )
			{
//				Carrega as médias de cada disciplina de cada aluno
//				$lst_exame		= $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_turma, $this->cod_turma, $qtd_modulos, $this->ref_cod_curso, true, true, true );
//
//				if ( is_array( $lst_exame ) )
//				{
//					$obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
//					$det_curso = $obj_curso->detalhe();
//
//					if ( is_array( $det_curso ) )
//					{
//						foreach ( $lst_exame as $exame )
//						{
//							if ( $det_curso["frequencia_minima"] > ( 100 - $exame["faltas"] ) )
//							{
//								$exibe_bt_classificar = "S";
//							}
//						}
//					}
//				}
//				Exibir o botao apenas se existirem alunos com frequencia abaixo da permitida
				if ( $exibe_bt_classificar == "S" )
				{
					$this->array_botao[] 	 = "Classificar Alunos";
					$this->array_botao_url[] = "educar_turma_nota_cad.php?ref_cod_turma={$this->cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&classifica=S";
				}
			}

			$this->array_botao[] 	 = "Cancelar";
			$this->array_botao_url[] = "educar_turma_mvto_lst.php";
			$this->largura = "100%";
		}
		$this->largura = "100%";
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
//echo "<pre>"; print_r(count($mat)); die();

?>