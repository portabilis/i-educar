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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matr&iacute;cula" );
		$this->processoAp = "578";
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

	var $cod_matricula;
	var $ref_cod_reserva_vaga;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_aluno;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ano;

	var $ref_cod_instituicao;
	var $ref_cod_curso;
	var $ref_cod_escola;

	var $matricula_transferencia;
	var $semestre;
	var $is_padrao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

//		die('Serviço indisponivel temporariamente!');

		$this->cod_matricula=$_GET["cod_matricula"];
		$this->ref_cod_aluno=$_GET["ref_cod_aluno"];

		$obj_aluno = new clsPmieducarAluno($this->ref_cod_aluno);
		if(!$obj_aluno->existe())
		{
			header("location: educar_matricula_lst.php");
			die;
		}

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

		if ( is_numeric( $this->cod_matricula ) )
		{
			if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
			{
				$this->Excluir();
			}
		}
//		else{
//			header("location: educar_matricula_lst.php");
//			die;
//		}
		$this->url_cancelar = "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_matricula", $this->cod_matricula );
		$this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );

		$obj_aluno = new clsPmieducarAluno();
		$lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
		if ( is_array($lst_aluno) )
		{
			$det_aluno = array_shift($lst_aluno);
			$this->nm_aluno = $det_aluno["nome_aluno"];
//			$this->campoRotulo( "nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false,false,false,"","","","",true );
			$this->campoRotulo( "nm_aluno", "Aluno", $this->nm_aluno );
		}

		/**
		 * verifica se nao existem matriculas
		 * e exibe campo check para informar
		 * se a matricula vem de transferencia
		 * ex: um aluno esta sendo matriculado no 3º ano
		 * ele veio de outra escola mas nao vem como transferencia
		 * isto eh necessario para o relatorio de movimentacao mensal
		 */

		$obj_matricula = new clsPmieducarMatricula();
		$lst_matricula = $obj_matricula->lista(null, null, null, null, null, null, $this->ref_cod_aluno);

		if(!$lst_matricula)
		{
			/**
			 * primeira matricula do sistema exibe campo check
			 */

			$this->campoCheck("matricula_transferencia", "Matr&iacute;cula de Transfer&ecirc;ncia","","Caso seja transfência externa por favor marque esta opção.");
		}

		// foreign keys
//		$obrigatorio = true;
		$instituicao_obrigatorio = true;
		$curso_obrigatorio = true;
		$escola_curso_obrigatorio = true;
		$get_escola = true;
		$get_curso = true;
//		$get_escola_curso = true;
		$get_escola_curso_serie = true;
		$get_matricula = true;
		$sem_padrao = true;

		include("include/pmieducar/educar_campo_lista.php");
		
		if (is_numeric($this->ref_cod_curso))
		{
			$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
			$det_curso = $obj_curso->detalhe();
			if (is_numeric($det_curso["ref_cod_tipo_avaliacao"]))
			{
				$this->campoOculto("apagar_radios", $det_curso["padrao_ano_escolar"]);
				$this->campoOculto("is_padrao", $det_curso["padrao_ano_escolar"]);
			}
		}

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		$this->acao_enviar = "valida()";
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

		$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
		$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $this->ref_cod_escola,null,null,null,1,null,null,null,null,1 );
		if ( is_array($lst_escola_ano_letivo) )
		{
			$det_escola_ano_letivo = array_shift($lst_escola_ano_letivo);
			$this->ano = $det_escola_ano_letivo["ano"];

			$obj_reserva_vaga = new clsPmieducarReservaVaga();
			$lst_reserva_vaga = $obj_reserva_vaga->lista( null,$this->ref_cod_escola,$this->ref_ref_cod_serie,null,null,$this->ref_cod_aluno,null,null,null,null,1 );
			// verifica se existe reserva de vaga para o aluno
			if ( is_array($lst_reserva_vaga) )
			{
				$det_reserva_vaga = array_shift($lst_reserva_vaga);
				$this->ref_cod_reserva_vaga = $det_reserva_vaga["cod_reserva_vaga"];

				$obj_reserva_vaga = new clsPmieducarReservaVaga( $this->ref_cod_reserva_vaga,null,null,$this->pessoa_logada,null,null,null,null,0 );
				$editou = $obj_reserva_vaga->edita();
				if ( !$editou )
				{
					$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
					echo "<!--\nErro ao editar clsPmieducarReservaVaga\nvalores obrigatorios\nis_numeric( $this->ref_cod_reserva_vaga ) && is_numeric( $this->pessoa_logada )\n-->";
					return false;
				}
			}

			$vagas_restantes = 1;

			if ( !$this->ref_cod_reserva_vaga )
			{
				$obj_turmas = new clsPmieducarTurma();
				$lst_turmas = $obj_turmas->lista( null,null,null,$this->ref_ref_cod_serie, $this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,true );
				//,$this->ref_cod_escola,$this->ref_ref_cod_serie
				//echo "$lst_turmas = $obj_turmas->lista( null,null,null,$this->ref_ref_cod_serie, $this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1 );"
				//print_r($lst_turmas);die;

				if ( is_array($lst_turmas) )
				{
					$total_vagas = 0;
					foreach ( $lst_turmas AS $turmas )
					{
						$total_vagas += $turmas["max_aluno"];
					}
				}
				else
				{
					$this->mensagem = "S&eacute;rie n&atilde;o possui nenhuma Turma cadastrada.<br>";
					return false;
				}

				$obj_matricula = new clsPmieducarMatricula();
				$lst_matricula = $obj_matricula->lista( null,null,$this->ref_cod_escola,$this->ref_ref_cod_serie,null,null,null,3,null,null,null,null,1,$this->ano,$this->ref_cod_curso,$this->ref_cod_instituicao,1 );
				if ( is_array($lst_matricula) )
				{
					$matriculados = count($lst_matricula);
				}

				$obj_reserva_vaga = new clsPmieducarReservaVaga();
				$lst_reserva_vaga = $obj_reserva_vaga->lista( null,$this->ref_cod_escola,$this->ref_ref_cod_serie,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao,$this->ref_cod_curso );
				if ( is_array($lst_reserva_vaga) )
				{
					$reservados = count($lst_reserva_vaga);
				}

				$vagas_restantes = $total_vagas - ($matriculados + $reservados);
			}

			if ($vagas_restantes <= 0)
			{
				echo "<script> if(!confirm('Excedido o número de total de vagas para Matricula! \\n Número total de matriculados:$matriculados \\n Número total de vagas reservadas: $reservados \\n Número total de vagas: $total_vagas \\n Deseja mesmo assim realizar a Matrícula?')) window.location = 'educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';</script>";
//				return;
			}
					
			$obj_matricula_aluno = new clsPmieducarMatricula();
			$lst_matricula_aluno = $obj_matricula_aluno->lista(null, null, null, null, null, null, $this->ref_cod_aluno);
			if(!$lst_matricula_aluno)
			{
				/**
				 * primeira matricula do sistema - consistencia -
				 */
				$this->matricula_transferencia = $this->matricula_transferencia == 'on' ? 'true' : 'false';
			}
			else
			{
				$this->matricula_transferencia = false;
			}

			if ($this->is_padrao == 1)
			{
				$this->semestre = null;
			}
			$obj = new clsPmieducarMatricula( null, $this->ref_cod_reserva_vaga, $this->ref_cod_escola, $this->ref_ref_cod_serie, null, $this->pessoa_logada, $this->ref_cod_aluno, 3, null, null, 1, $this->ano, 1, null, null, null, null, $this->ref_cod_curso, $this->matricula_transferencia, $this->semestre );
			
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				//$obj_matricula = new clsPmieducarMatricula();
			//	$lst_matricula = $obj_matricula->lista(null,null,null,null,null,null,$this->ref_cod_aluno);
				/**
				 * desmarca todas as matriculas como ultima matricula do aluno
				 */
			/*	if($lt_matricula)
				{
					foreach ($lst_matricula as $matricula)
					{
						if($matricula['cod_matricula'] != $cadastrou)
						{
							$obj_matricula = new clsPmieducarMatricula($matricula['cod_matricula'],null,null,null,null,null,null,null,null,null,null,null,0);
							$obj_matricula->edita();
						}
					}
				}*/

				if($this->pessoa_logada == 21317)
				$this->desativaMatriculasSequencia($cadastrou);


				$obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
				$lst_transferencia = $obj_transferencia->lista( null,null,null,null,null,null,null,null,null,null,null,1,null,null,$this->ref_cod_aluno,false,null,null,null,true,false );
				// verifica se existe solicitacao de transferencia do aluno
				if ( is_array($lst_transferencia) )
				{
					//echo "<pre>"; print_r("if"); die();
					$det_transferencia = array_shift($lst_transferencia);

					$obj_transferencia = new clsPmieducarTransferenciaSolicitacao( $det_transferencia['cod_transferencia_solicitacao'],null,$this->pessoa_logada,null,null,null,null,null,null,0 );
					$editou2 = $obj_transferencia->edita();
					if ( $editou2 )
					{
						$obj = new clsPmieducarMatricula( $det_transferencia['ref_cod_matricula_saida'],null,null,null,$this->pessoa_logada,null,null,4,null,null,1,null,0 );
						$editou3 = $obj->edita();
						if (!$editou3)
						{
							$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
							echo "<!--\nErro ao editar clsPmieducarMatricula\nvalores obrigatorios\nis_numeric( {$det_transferencia['ref_cod_matricula_saida']} ) && is_numeric( $this->pessoa_logada )\n-->";
							return false;
						}
					}
					else
					{
						$this->mensagem = "Edição n&atilde;o realizada.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarTransferenciaSolicitacao\nvalores obrigatorios\nis_numeric( {$det_transferencia['cod_transferencia_solicitacao']} ) && is_numeric( {$this->pessoa_logada} ) \n-->";
						return false;
					}
				}
				else
				{
					//echo "<pre>"; print_r("else"); die();
					$obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
					$lst_transferencia = $obj_transferencia->lista( null,null,null,null,null,null,null,null,null,null,null,1,null,null,$this->ref_cod_aluno,false,null,null,null,false,false );
					// verifica se existe solicitacao de transferencia do aluno
					if ( is_array($lst_transferencia) )
					{
						// verifica cada solicitacao de transferencia do aluno
						foreach ($lst_transferencia as $transferencia)
						{
							$obj_matricula = new clsPmieducarMatricula( $transferencia['ref_cod_matricula_saida'] );
							$det_matricula = $obj_matricula->detalhe();

							// caso a solicitacao seja para uma mesma serie
							if ($det_matricula['ref_ref_cod_serie'] == $this->ref_ref_cod_serie)
							{
								$ref_cod_transferencia = $transferencia["cod_transferencia_solicitacao"];
								break;
							}
							else // caso a solicitacao seja para a serie da sequencia
							{
								$obj_sequencia = new clsPmieducarSequenciaSerie( $det_matricula['ref_ref_cod_serie'],$this->ref_ref_cod_serie,null,null,null,null,1 );
								if ($obj_sequencia->existe())
								{
									$ref_cod_transferencia = $transferencia["cod_transferencia_solicitacao"];
									break;
								}
							}
							$ref_cod_transferencia = $transferencia["cod_transferencia_solicitacao"];
						}
//						$ref_cod_transferencia = $det_transferencia["cod_transferencia_solicitacao"];
//						echo "<pre>"; echo "<br><br><br>"; print_r($ref_cod_transferencia); die();
						if ($ref_cod_transferencia)
						{
							$obj_transferencia = new clsPmieducarTransferenciaSolicitacao( $ref_cod_transferencia,null,$this->pessoa_logada,null,$cadastrou,null,null,null,null,1,date("Y-m-d") );
							$editou2 = $obj_transferencia->edita();
							if ( $editou2 )
							{
								$obj_transferencia = new clsPmieducarTransferenciaSolicitacao( $ref_cod_transferencia );
								$det_transferencia = $obj_transferencia->detalhe();
								$matricula_saida = $det_transferencia["ref_cod_matricula_saida"];
								$obj_matricula = new clsPmieducarMatricula( $matricula_saida );
								$det_matricula = $obj_matricula->detalhe();
								// caso a situacao da matricula do aluno esteja em andamento
								if( $det_matricula['aprovado'] == 3 )
								{
									$obj_matricula = new clsPmieducarMatricula( $cadastrou,null,null,null,$this->pessoa_logada,null,null,null,null,null,1,null,null,$det_matricula['modulo'] );
									$editou_mat = $obj_matricula->edita();
									if ($editou_mat)
									{
										$obj_matricula_turma = new clsPmieducarMatriculaTurma();
										$lst_matricula_turma = $obj_matricula_turma->lista( $matricula_saida,null,null,null,null,null,null,null,1 );
										if (is_array($lst_matricula_turma))
										{
											$det_matricula_turma = array_shift($lst_matricula_turma);

											$obj_matricula_turma = new clsPmieducarMatriculaTurma( $matricula_saida,$det_matricula_turma['ref_cod_turma'],$this->pessoa_logada,null,null,null,0,null,$det_matricula_turma['sequencial'] );
											$editou_mat_turma = $obj_matricula_turma->edita();
											if (!$editou_mat_turma)
											{
												$this->mensagem = "N&atilde;o foi poss&iacute;vel editar a Matr&iacute;cula Turma.<br>";
												return false;
											}
										}
										// armazena as disciplinas utilizadas da nova escola
										$obj_esd = new clsPmieducarEscolaSerieDisciplina();
										$lst_esd = $obj_esd->lista( $this->ref_ref_cod_serie,$this->ref_cod_escola,null,1 );
										if (is_array($lst_esd))
										{
											foreach ($lst_esd as $campo)
											{
												$disciplinas[$campo['ref_cod_disciplina']] = $campo['ref_cod_disciplina'];
											}
										}
										// lista as notas que o aluno ja tinha
										$obj_nota_aluno = new clsPmieducarNotaAluno();
										$lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,$this->ref_ref_cod_serie,null,null,$matricula_saida,null,null,null,null,null,null,1 );
										if (is_array($lst_nota_aluno))
										{
											foreach ($lst_nota_aluno as $campo)
											{
												if ($disciplinas[$campo['ref_cod_disciplina']])
												{
													// cadastra as notas existentes na nova matricula
													$obj_nota_aluno = new clsPmieducarNotaAluno( null,$campo['ref_sequencial'],$campo['ref_ref_cod_tipo_avaliacao'],$this->ref_ref_cod_serie,$this->ref_cod_escola,$campo['ref_cod_disciplina'],$cadastrou,null,$this->pessoa_logada,null,null,1,$campo['modulo'] );
													$cadastrou_nota = $obj_nota_aluno->cadastra();
													if (!$cadastrou_nota)
													{
														$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
														echo "<!--\nErro ao cadastrar clsPmieducarNotaAluno\nvalores obrigatorios\nis_numeric( {$campo['ref_sequencial']} ) && is_numeric( {$campo['ref_ref_cod_tipo_avaliacao']} ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo['ref_cod_disciplina']} ) && is_numeric( $cadastrou ) && is_numeric( $this->pessoa_logada ) && is_numeric( {$campo['modulo']} )\n-->";
														return false;
													}
												}
											}
										}
										// lista as faltas que o aluno ja tinha
										$obj_falta_aluno = new clsPmieducarFaltaAluno();
										$lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,$this->ref_ref_cod_serie,null,null,$matricula_saida,null,null,null,null,null,1 );
										if (is_array($lst_falta_aluno))
										{
											foreach ($lst_falta_aluno as $campo)
											{
												if ($disciplinas[$campo['ref_cod_disciplina']])
												{
													// cadastra as faltas existentes na nova matricula
													$obj_falta_aluno = new clsPmieducarFaltaAluno( null,null,$this->pessoa_logada,$this->ref_ref_cod_serie,$this->ref_cod_escola,$campo['ref_cod_disciplina'],$cadastrou,$campo['faltas'],null,null,1,$campo['modulo'] );
													$cadastrou_falta = $obj_falta_aluno->cadastra();
													if (!$cadastrou_falta)
													{
														$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
														echo "<!--\nErro ao cadastrar clsPmieducarFaltaAluno\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo['ref_cod_disciplina']} ) && is_numeric( $cadastrou ) && is_numeric( {$campo['faltas']} ) && is_numeric( {$campo['modulo']} )\n-->";
														return false;
													}
												}
											}
										}
										// lista as notas que o aluno ja tinha (globalizada)
										$obj_faltas = new clsPmieducarFaltas();
										$lst_faltas = $obj_faltas->lista( $matricula_saida );
										if (is_array($lst_faltas))
										{
											foreach ($lst_faltas as $campo)
											{
												// cadastra as faltas existentes na nova matricula
												$obj_faltas = new clsPmieducarFaltas( $cadastrou,$campo['sequencial'],$this->pessoa_logada,$campo['falta'] );
												$cadastrou_faltas = $obj_faltas->cadastra();
												if (!$cadastrou_faltas)
												{
													$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
													echo "<!--\nErro ao cadastrar clsPmieducarFaltas\nvalores obrigatorios\nis_numeric( $cadastrou ) && is_numeric( {$campo['sequencial']} ) && is_numeric( $this->pessoa_logada ) && is_numeric( {$campo['falta']} )\n-->";
													return false;
												}
											}
										}
									}
								}

								$obj = new clsPmieducarMatricula( $matricula_saida,null,null,null,$this->pessoa_logada,null,null,4,null,null,1,null,0 );
								$editou3 = $obj->edita();
								if (!$editou3)
								{
									$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
									echo "<!--\nErro ao editar clsPmieducarMatricula\nvalores obrigatorios\nis_numeric( $matricula_saida ) && is_numeric( $this->pessoa_logada )\n-->";
									return false;
								}
							}
							else
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarTransferenciaSolicitacao\nvalores obrigatorios\nis_numeric( $ref_cod_transferencia ) && is_numeric( $cadastrou ) && is_numeric( $this->pessoa_logada )\n-->";
								return false;
							}
						}
					}
				}
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
				die();
				return true;
			}
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarMatricula\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->ano )\n-->";
			return false;
		}
		else
		{
			$this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar o Ano Letivo em andamento da Escola.<br>";
			return false;
		}
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

		$obj_matricula_turma = new clsPmieducarMatriculaTurma();
		$lst_matricula_turma = $obj_matricula_turma->lista( $this->cod_matricula,null,null,null,null,null,null,null,1 );
		if (is_array($lst_matricula_turma))
		{
			$det_matricula_turma = array_shift($lst_matricula_turma);
			$obj_matricula_turma = new clsPmieducarMatriculaTurma( $det_matricula_turma["ref_cod_matricula"],$det_matricula_turma["ref_cod_turma"],$this->pessoa_logada,null,null,null,0,null,$det_matricula_turma["sequencial"] );
			$editou = $obj_matricula_turma->edita();
			if (!$editou)
			{
				$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
				echo "<!--\nErro ao editar clsPmieducarMatriculaTurma\nvalores obrigatorios\nif( is_numeric( {$det_matricula_turma["ref_cod_matricula"]} ) && is_numeric( {$det_matricula_turma["ref_cod_turma"]} ) && is_numeric( {$det_matricula_turma["sequencial"]} ) && is_numeric( $this->pessoa_logada ) )\n-->";
				return false;
			}
		}

		$obj_matricula = new clsPmieducarMatricula( $this->cod_matricula );
		$det_matricula = $obj_matricula->detalhe();
		$ref_cod_serie = $det_matricula["ref_ref_cod_serie"];

		$obj_sequencia = new clsPmieducarSequenciaSerie();
		$lst_sequencia = $obj_sequencia->lista( null,$ref_cod_serie,null,null,null,null,null,null,1 );
		// verifica se a serie da matricula cancelada eh sequencia de alguma outra serie
		if ( is_array($lst_sequencia) )
		{
			$det_sequencia = array_shift($lst_sequencia);
			$ref_serie_origem = $det_sequencia["ref_serie_origem"];

			$obj_matricula = new clsPmieducarMatricula();
			$lst_matricula = $obj_matricula->lista( null,null,null,$ref_serie_origem,null,null,$this->ref_cod_aluno,null,null,null,null,null,1,null,null,null,0 );
			// verifica se o aluno tem matricula na serie encontrada (sequencia)
			if ( is_array($lst_matricula) )
			{
				$det_matricula = array_shift($lst_matricula);
				$ref_cod_matricula = $det_matricula["cod_matricula"];

				$obj = new clsPmieducarMatricula( $ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,null,null,null,1,null,1 );
				$editou1 = $obj->edita();
				if( !$editou1 )
				{
					$this->mensagem = "N&atilde;o foi poss&iacute;vel editar a &Uacute;ltima Matr&iacute;cula da Sequ&ecirc;ncia.<br>";
					return false;
				}
			}
		}

		$obj = new clsPmieducarMatricula( $this->cod_matricula,null,null,null,$this->pessoa_logada,null,null,null,null,null,0 );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarMatricula\nvalores obrigatorios\nif( is_numeric( $this->cod_matricula ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	/**
	 * marca como zero o campo ultima_matricula
	 * das matriculas da sequencia
	 *
	 */
	function desativaMatriculasSequencia($ultima_matricula)
	{

		$db2 = new clsBanco();

		$db2->Consulta( "
						SELECT
							so.ref_cod_curso as curso_origem
							, ss.ref_serie_origem as serie_origem
							, sd.ref_cod_curso as curso_destino
							, ss.ref_serie_destino as serie_destino
						FROM
							pmieducar.sequencia_serie ss
							, pmieducar.serie so
							, pmieducar.serie sd
						WHERE
							ss.ativo = 1
							AND ref_serie_origem = so.cod_serie
							AND ref_serie_destino = sd.cod_serie
						ORDER BY
							ss.ref_serie_origem ASC
						");

		if ($db2->numLinhas())
		{
			while ( $db2->ProximoRegistro() )
			{
				$sequencias[] = $db2->Tupla();
			}
		}

		$db2->Consulta( "
		SELECT
			distinct( o.ref_serie_origem )
		FROM
			pmieducar.sequencia_serie o
			, pmieducar.escola_serie es
		WHERE NOT EXISTS
		(
			SELECT
				1
			FROM
				pmieducar.sequencia_serie d
			WHERE
				o.ref_serie_origem = d.ref_serie_destino
		)
		");

		if ($db2->numLinhas())
		{
			$pertence_sequencia = false;
			$achou_serie = false;
			$reset = false;

			$serie_sequencia[] = $this->ref_ref_cod_serie;

			while ( $db2->ProximoRegistro() )
			{
				list( $ini_sequencia ) = $db2->Tupla();

				$ini_serie = $ini_sequencia;
				reset($sequencias);

				do
				{
					if( $reset )
					{
						reset($sequencias);
						$reset = false;
					}

					$sequencia = current($sequencias);
					$aux_serie = $sequencia['serie_origem'];

					if ($ini_serie == $aux_serie)
					{
						if ($this->ref_ref_cod_serie == $aux_serie)
						{
							// achou serie da matricula
							$achou_serie = true;
						}
						if ($sequencia['curso_destino'] == $this->ref_cod_curso)
						{
							// curso pertence a sequencia
							$pertence_sequencia = true;
							$serie_sequencia[] = $sequencia['serie_destino'];
							$ini_serie = $sequencia['serie_destino'];
							$reset = true;
						}
						else
						{
							$ini_serie = $sequencia['serie_destino'];
							$reset = true;
						}
					}
				} while ( each($sequencias) );

				if ($achou_serie && $pertence_sequencia)
				{
					// curso escolhido pertence a sequencia da serie da matricula
					$series = implode(",",$serie_sequencia);
					$db2->Consulta("UPDATE pmieducar.matricula SET ultima_matricula = 0 WHERE ref_ref_cod_serie IN ($series) AND ref_cod_aluno = {$this->ref_cod_aluno} AND cod_matricula != $ultima_matricula ");
					die("UPDATE pmieducar.matricula SET ultima_matricula = 0 WHERE ref_ref_cod_serie IN ($series) AND ref_cod_aluno = {$this->ref_cod_aluno} AND cod_matricula != $ultima_matricula ");
				}
			}
		}
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
<script>

function getCursoMatricula()
{
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var cod_aluno = <?=$_GET["ref_cod_aluno"]?>;

	var campoCurso = document.getElementById('ref_cod_curso');
	campoCurso.length = 1;
	campoCurso.disabled = true;
	campoCurso.options[0].text = 'Carregando curso';

	var xml_curso_matricula = new ajax( atualizaCursoMatricula );
	xml_curso_matricula.envia( "educar_curso_matricula_xml.php?ins="+campoInstituicao+"&alu="+cod_aluno );
}

function atualizaCursoMatricula( xml_curso_matricula )
{
	var campoCurso = document.getElementById('ref_cod_curso');
	var DOM_array = xml_curso_matricula.getElementsByTagName( "curso" );

	if(DOM_array.length)
	{
		campoCurso.length = 1;
		campoCurso.options[0].text = 'Selecione um curso';
		campoCurso.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
		}
	}
	else
		campoCurso.options[0].text = 'A instituição não possui nenhum curso';

}

function getSerieMatricula()
{
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoEscola = document.getElementById('ref_cod_escola').value;
	var campoCurso  = document.getElementById('ref_cod_curso').value;
	var cod_aluno = <?=$_GET["ref_cod_aluno"]?>;

	var campoSerie	= document.getElementById('ref_ref_cod_serie');
	campoSerie.length = 1;
	campoSerie.disabled = true;
	campoSerie.options[0].text = 'Carregando série';

	var xml_serie_matricula = new ajax( atualizaSerieMatricula );
	xml_serie_matricula.envia( "educar_serie_matricula_xml.php?ins="+campoInstituicao+"&cur="+campoCurso+"&esc="+campoEscola+"&alu="+cod_aluno );
}

function atualizaSerieMatricula( xml_serie_matricula )
{
	var campoSerie = document.getElementById('ref_ref_cod_serie');
	var DOM_array = xml_serie_matricula.getElementsByTagName( "serie" );

	if(DOM_array.length)
	{
		campoSerie.length = 1;
		campoSerie.options[0].text = 'Selecione uma série';
		campoSerie.disabled = false;
		var series = new Array();
		for( var i = 0; i < DOM_array.length; i++ )
		{
			if(!series[DOM_array[i].getAttribute("cod_serie") + '_'])
			{
				campoSerie.options[campoSerie.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
				series[DOM_array[i].getAttribute("cod_serie") + '_'] = true;
			}
		}
	}
	else
		campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
}

document.getElementById('ref_cod_escola').onchange = function()
{
	if( document.getElementById('ref_cod_escola').value == "" )
		getCursoMatricula();
	else
		getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getSerieMatricula();
}

function valida()
{
	if( document.getElementById('ref_cod_escola').value )
	{
		if( !document.getElementById('ref_ref_cod_serie').value )
		{
			alert("O campo 'Série' deve ser preenchido corretamente!");
			document.getElementById('ref_ref_cod_serie').focus();
			return false;
		}
	}
	if( !acao() )
		return false;

	document.forms[0].submit();
}

</script>