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

/*
depois precisa remover as consultas diretas de SQL
querys personalizadas criadas temporariamente, mudar para que sejam
metodos de suas respectivas classes
*/
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Faltas/Notas Turma " );
		$this->processoAp = "650";
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
	var $passo;
	var $ref_cod_modulo;

	var $lst_matricula_turma;
	var $falta_ch_globalizada;
	var $lst_matriculas;

	var $nm_aluno;
	var $ref_cod_aluno;
	var $ref_cod_matricula;
	var $ref_cod_turma;
	var $ref_ref_cod_serie;
	var $ref_cod_curso;
	var $ref_ref_cod_escola;
	var $ref_cod_instituicao;
	var $modulo;
	var $max_modulos;
	var $ref_cod_disciplina;
	var $ref_cod_serie_disciplina;
	var $nota;
	var $faltas;
	var $disciplina_modulo;
	var $ref_cod_tipo_avaliacao;
	var $qtd_disciplinas;

	var $qtd_modulos;
	var $media;
	var $media_exame;
	var $aluno_exame;
	var $aprovado;
	var $conceitual;
	var $ano_letivo;
	var $padrao_ano_escolar;
	var $num_modulo;
	var $frequencia_minima;
	var $carga_horaria;
	var $hora_falta;
	var $cod_disciplinas;
	var $lst_apura_falta;
	var $exame;
	var $classifica;

	var $is_nota_exame;
	var $media_especial;
	
	var $pula_passo;
	
	var $ultima_disciplina;
	
	function Inicializar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_turma 	  = $_GET["ref_cod_turma"];
		$this->ref_ref_cod_escola = $_GET["ref_ref_cod_escola"];
		$this->ref_ref_cod_serie  = $_GET["ref_ref_cod_serie"];
		$this->ref_cod_curso	  = $_GET["ref_cod_curso"];
		$this->ref_cod_disciplina = $_GET["ref_cod_disciplina"];
		$this->classifica		  = $_GET["classifica"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 650, $this->pessoa_logada, 7, "educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}" );

		$this->passo = 0;

		return "Novo";
	}

	function Gerar()
	{
		$this->url_cancelar 	 = "educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}";
		$this->nome_url_cancelar = "Cancelar";

		// a disciplina vem com a serie junto... vamos separar
		if(strpos($this->ref_cod_disciplina,"_"))
		{
			$arr = explode("_",$this->ref_cod_disciplina);
			$this->ref_cod_disciplina = $arr[1];
			$this->ref_cod_serie_disciplina = $arr[0];
		}
		else
		{
			$this->ref_cod_disciplina = $this->ref_cod_disciplina;
			$this->ref_cod_serie_disciplina = null;
		}
		
		$objTurma = new clsPmieducarTurma($this->ref_cod_turma,null,null,$this->ref_ref_cod_serie);
		$detalhe_turma = $objTurma->detalhe();
		if( $detalhe_turma )
		{
			
			// a turma existe, vamos pegar os dados dela
			$this->ref_ref_cod_serie = $detalhe_turma["ref_ref_cod_serie"];
			$objSerie = new clsPmieducarSerie($this->ref_ref_cod_serie);
			$detalhe_serie = $objSerie->detalhe();
			$this->media_especial = dbBool($detalhe_serie['media_especial']);


			$this->ref_ref_cod_serie_mult = $detalhe_turma["ref_ref_cod_serie_mult"];
			if( $this->ref_ref_cod_serie_mult )
			{
				$objSerieMult = new clsPmieducarSerie($this->ref_ref_cod_serie_mult);
				$detalhe_serie_mult = $objSerieMult->detalhe();
			}

			$this->ref_ref_cod_escola = $detalhe_turma["ref_ref_cod_escola"];

			$this->ref_cod_curso = $detalhe_serie["ref_cod_curso"];
			$objCurso = new clsPmieducarCurso($this->ref_cod_curso);
			$detalhe_curso = $objCurso->detalhe();


			$objEscolaAnoLetivo = new clsPmieducarEscolaAnoLetivo();
			$lstEscolaAnoLetivo = $objEscolaAnoLetivo->lista($this->ref_ref_cod_escola,null,null,null,1,null,null,null,null,1);
			if( is_array($lstEscolaAnoLetivo) )
			{
				foreach ($lstEscolaAnoLetivo as $value)
				{
					$this->ano_letivo = $value["ano"];
				}
			}

			$objTipoAvaliacao = new clsPmieducarTipoAvaliacao($detalhe_curso["ref_cod_tipo_avaliacao"]);
			$detalhe_tipo_avaliacao = $objTipoAvaliacao->detalhe();
			$this->conceitual = $detalhe_tipo_avaliacao["conceitual"];
			$this->ref_cod_tipo_avaliacao = $detalhe_curso["ref_cod_tipo_avaliacao"];

			$this->falta_ch_globalizada = $detalhe_curso["falta_ch_globalizada"];
			$this->num_modulo = $objTurma->moduloMinimo();
//			se o modulo atual for maior que o maximo o ano acabou
			$this->max_modulos = $objTurma->maxModulos();

			if( $this->num_modulo == $this->max_modulos + 1 )
			{
				// ve se vai para a pagina de aprovacao/reprovacao ou se mostra as notas do exame
				if( ! is_null( $detalhe_curso["media_exame"] ) )
				{
					// essa turma pode ter exame
					$detalhe_modulo = array();
					$detalhe_modulo["nm_tipo"] = "Exame";
//					ve se tem algum aluno em exame
					$a = $objTurma->moduloExameAlunos();
					if( $objTurma->moduloExameAlunos())
					{
						// existe algum aluno de exame
						$this->exame = 1;

						// vamos ver se tem alguma excessao que precisa de nota de exame
						$objExcessoes = new clsPmieducarMatriculaExcessao();
						$lista_excessoes = $objExcessoes->lista(null,$this->ref_cod_turma,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,true,true,IS_NULL);
						if( is_array($lista_excessoes) )
						{
							// existe alguma excessao, mostra a tela de excessoes
							$this->passo = 2;
						}
						else
						{
							// nao tem excessao, entao vamos dar as notas do exame
//							$this->passo = 1;

						}
					}
					else
					{
						
						// poderia ter exame, mas nenhum aluno pegou exame, vamos pra aprovaï¿½ï¿½o
						if( $this->conceitual )
						{
							// conceitual aprova manualmente
							$this->passo = 3;
						}
						else
						{
							$objExcessoes = new clsPmieducarMatriculaExcessao();
							$lista_excessoes = $objExcessoes->lista(null,$this->ref_cod_turma,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,true,false);
							if( is_array($lista_excessoes) )
							{
								// existe alguma excessao para aprovacao, vamos para tela de excessoes
								$this->passo = 2;
							}
							else
							{
								// ninguem de exame, a turma nao eh conceitual e nenhuma excessao... todos ja deveriam estar aprovados/reprovados
								// $this->campoRotulo("alerta","Alerta","Erro: [1] Todos os alunos dessa turma jï¿½ foram aprovados/reprovados.");
								// vamos corrigir o problema aprovando o pessoal que esta na espera (ja que eles nao pegaram exame nem reprovaram por falta)
								$db2 = new clsBanco();
								$db2->Consulta("SELECT cod_matricula FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND aprovado = 3 AND ativo = 1");
								while ($db2->ProximoRegistro())
								{
									list($mat) = $db2->Tupla();
									$objAprova = new clsPmieducarMatricula($mat,null,null,null,$this->pessoa_logada,null,null,1);
									$objAprova->edita();
								}
								header("location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}");
								die();
							}
						}
					}
				}
				else
				{
					// nao tem exame, fase de aprovacao dos alunos
					if( $this->conceitual )
					{
						// conceitual aprova manualmente
						$this->passo = 3;
					}
					else
					{
						// nao conceitual
						$objExcessoes = new clsPmieducarMatriculaExcessao();
						$lista_excessoes = $objExcessoes->lista(null,$this->ref_cod_turma,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,true);
						if( is_array($lista_excessoes) )
						{
							// tem excessao, mostra tela de excessoes para aprovacao
							$this->passo = 2;
						}
						else
						{
							// ninguem de exame, a turma nao eh conceitual e nenhuma excessao... todos ja deveriam estar aprovados/reprovados
							// vamos corrigir o problema e aprovar todo mudno que nao pegou exame
							$db2 = new clsBanco();
							$db2->Consulta("SELECT cod_matricula FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND aprovado = 3 AND ativo = 1");
							while ($db2->ProximoRegistro())
							{
								list($mat) = $db2->Tupla();
								$objAprova = new clsPmieducarMatricula($mat,null,null,null,$this->pessoa_logada,null,null,1);
								$objAprova->edita();
							}
							//$this->campoRotulo("alerta","Alerta","Erro: [2] Todos os alunos dessa turma jï¿½ foram aprovados/reprovados.");
							header("location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}");
							die();
						}
					}
				}
			}
			else if( $this->num_modulo > $this->max_modulos )
			{
				
				// jah passou o exame, fase de aprovacao dos alunos
				$objExcessoes = new clsPmieducarMatriculaExcessao();
				$lista_excessoes = $objExcessoes->lista(null,$this->ref_cod_turma,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,true);
				if( is_array($lista_excessoes) )
				{
					// ta na fase de aprovacao mas ainda tem alguma excessao
					$this->passo = 2;
				}
				else
				{
					// fase de aprovacao e sem excessoes
					if( $this->conceitual )
					{
						$this->passo = 3;
					}
					else
					{
						// ja acabou o exame, a turma nao eh conceitual e nenhuma excessao... todos ja deveriam estar aprovados/reprovados
//						$this->campoRotulo("alerta","Alerta","Erro: [3] Todos os alunos dessa turma jï¿½ foram aprovados/reprovados.");

						$db2 = new clsBanco();
						$db2->Consulta("SELECT cod_matricula FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND aprovado = 3 AND ativo = 1");
						while ($db2->ProximoRegistro())
						{
							list($mat) = $db2->Tupla();
							$objAprova = new clsPmieducarMatricula($mat,null,null,null,$this->pessoa_logada,null,null,1);
							$objAprova->edita();
						}
						header("location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}");
						die();
					}
				}
			}
			else
			{
				
			if ($this->ref_cod_disciplina && $this->passo == 1) {
				if ($this->ultima_disciplina == 1 && $this->conceitual && $this->num_modulo == $this->max_modulos)
				{
					$this->pula_passo = true;
				}
			}

				
				$this->exame = 0;
				$cod_modulo = 0;
				$objAnoLetivoModulo = new clsPmieducarAnoLetivoModulo();
				$lstAnoLetivoModulo = $objAnoLetivoModulo->lista($this->ano_letivo,$this->ref_ref_cod_escola,$this->num_modulo);
				if( is_array($lstAnoLetivoModulo) )
				{
					foreach ($lstAnoLetivoModulo AS $value)
					{
						$cod_modulo = $value["ref_cod_modulo"];
						$this->ref_cod_modulo = $cod_modulo;
					}
				}

				if(!$objTurma->moduloMinimoDisciplina())
				{

					/**
					 * nao existem disciplinas sem nota redireciona para listagem de nota turma
					 */
					if(!$_POST)
						echo "<script>alert('Todas as disciplinas já se encontram com nota!');window.location='educar_turma_mvto_det.php?cod_turma=$this->ref_cod_turma';</script>";
					else
						header("location:educar_turma_mvto_det.php?cod_turma=$this->ref_cod_turma");
					die;

				}


				$objModulo = new clsPmieducarModulo($cod_modulo);
				$detalhe_modulo = $objModulo->detalhe();

			}

			$this->campoRotulo("nm_turma","Turma",$detalhe_turma["nm_turma"]);
			$this->campoRotulo("nm_serie","S&eacute;rie",$detalhe_turma["multiseriada"] ? "{$detalhe_serie["nm_serie"]} e {$detalhe_serie_mult["nm_serie"]}": $detalhe_serie["nm_serie"] );
			$this->campoRotulo("nm_modulo","M&oacute;dulo",$detalhe_modulo["nm_tipo"]);
			$this->campoRotulo("num_modulo2","N&uacute;mero do M&oacute;dulo",$this->num_modulo . "
			<style>
				.cell_normal
				{
					border:1px;
					border-color:#000000;
					border-style:solid;
				}
				.cell_separa
				{
					border:1px;
					border-color:#000000;
					border-style:solid;
					border-left-width:2px;
				}
			</style>
			");


			if( $this->passo == 0 )
			{
				// mostra as disciplinas para escolher
				$opcoes_disciplinas = array(""=>"Selecione");
				if( ! $this->exame )
				{

					// modulo normal, acha todas as disciplinas que ainda precisam de nota no menor modulo
					$disciplinas = $objTurma->moduloMinimoDisciplina();

					// print_r($disciplinas);
					$num_disciplinas = 0;
					foreach ($disciplinas AS $value)
					{
						$objDisciplina = new clsPmieducarDisciplina($value["cod_disciplina"]);
						$det_dis = $objDisciplina->detalhe();
						
						$nm_serie = ( $value["cod_serie"] == $this->ref_ref_cod_serie ) ? $detalhe_serie["nm_serie"]: $detalhe_serie_mult["nm_serie"];

						$opcoes_disciplinas["{$value["cod_serie"]}_{$value["cod_disciplina"]}"] = $detalhe_turma["multiseriada"] ? "{$nm_serie} - {$det_dis["nm_disciplina"]}": "{$det_dis["nm_disciplina"]}";
						$num_disciplinas++;
					}
					if ($num_disciplinas == 1)
					{
						$this->campoOculto("ultima_disciplina", 1);
					}
				}
				else
				{
					// exame, acha as disciplinas em que os alunos nao atingiram a media
					$disciplinas = $objTurma->moduloExameDisciplina(true);
					foreach ($disciplinas AS $value)
					{
						$objDisciplina = new clsPmieducarDisciplina($value["cod_disciplina"]);
						$det_dis = $objDisciplina->detalhe();

						$nm_serie = ( $value["cod_serie"] == $this->ref_ref_cod_serie ) ? $detalhe_serie["nm_serie"]: $detalhe_serie_mult["nm_serie"];

						$opcoes_disciplinas["{$value["cod_serie"]}_{$value["cod_disciplina"]}"] = $detalhe_turma["multiseriada"] ? "{$nm_serie} - {$det_dis["nm_disciplina"]}": "{$det_dis["nm_disciplina"]}";
					}
				}
				$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes_disciplinas, $this->ref_cod_disciplina );
			}
			else if( $this->passo == 1 )
			{
				// mostra os alunos que ainda precisam de nota nessa disciplina (nesse modulo)
				$objDisciplina = new clsPmieducarDisciplina($this->ref_cod_disciplina);
				$detalhe_disciplina = $objDisciplina->detalhe();
				// print_r($detalhe_disciplina);
				$this->campoRotulo("disciplina","Disciplina","<strong>{$detalhe_disciplina["nm_disciplina"]}</strong>");
				$this->campoOculto( "ref_cod_disciplina", $this->ref_cod_disciplina );
				$this->campoOculto( "ref_cod_serie_disciplina", $this->ref_cod_serie_disciplina );
				$this->campoQuebra2();

				// seleciona as notas que estilo disponiveis para essa disciplina
				$objTipoAvaliacaoValor = new clsPmieducarTipoAvaliacaoValores();
				$opcoes_notas = array("" => "Selecione");
				$objTipoAvaliacaoValor->setOrderby("sequencial ASC");
				$lista_notas_valores = $objTipoAvaliacaoValor->lista($detalhe_curso["ref_cod_tipo_avaliacao"]);
				if( is_array($lista_notas_valores) )
				{
					foreach ($lista_notas_valores as $value)
					{
						$opcoes_notas[$value["sequencial"]] = $value["nome"];
					}
				}

				// pega as matriculas que vao receber nota
								
				if( ! $this->exame )
				{
					$matriculas = $objTurma->matriculados_modulo_disciplina_sem_nota($this->ref_cod_disciplina,$this->ref_cod_serie_disciplina, $this->num_modulo);
					
					$sql = "SELECT 
								cod_Matricula 
							FROM
								pmieducar.matricula m
							WHERE 
								cod_matricula in (".implode(",",$matriculas).")
								AND ref_ref_cod_serie={$this->ref_cod_serie_disciplina}";
					$banco = new clsBanco();
					$banco->Consulta($sql);
					if ($banco->Num_Linhas()) {
						$matriculas = array();
						while ($banco->ProximoRegistro()) {
							list($cod_matricula) = $banco->Tupla();
							$matriculas[$cod_matricula] = $cod_matricula;
						}
					}
					
				}
				else
				{

					$matriculas = $objTurma->moduloExameAlunos($this->ref_cod_disciplina);
					/**
					 * gera campo para dizer que as notas sao de exame
					 */
					$this->campoOculto("is_nota_exame",true);
				}

				if($matriculas)
				{
					$objMat = new clsPmieducarMatricula();
					$objMat->setOrderby("nome ASC");
					$lista_matriculas = $objMat->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$matriculas);
					$matriculas_exame_disponivel = false;
					if( is_array( $lista_matriculas ) )
					{
						foreach ($lista_matriculas AS $matricula)
						{
							
							if( ! $this->exame )
							{
								$this->campoRotulo("matricula[{$matricula["cod_matricula"]}]","Aluno", "<div style=\"float:left;width:250px;padding-top:2px;\">{$matricula["nome"]}</div>",true);
								$this->campoLista("nota[{$matricula["cod_matricula"]}]","Nota",$opcoes_notas, "","",true,"","",false,true);
							}
							else
							{
								$obj_nota_aluno = new clsPmieducarNotaAluno();
								$lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, null, null, $this->ref_cod_disciplina, $matricula["cod_matricula"], null, null, null, null, null, null, 1, $this->num_modulo);
								if (!is_array($lst_nota_aluno))
								{
									$matriculas_exame_disponivel=true;
									$this->campoRotulo("matricula[{$matricula["cod_matricula"]}]","Aluno", "<div style=\"float:left;width:250px;padding-top:2px;\">{$matricula["nome"]}</div>",true);
									$this->campoMonetario("nota[{$matricula["cod_matricula"]}]","Nota","",5,5,true,"","","onChange",false,true,"",true);
								}
//								$this->campoMonetario("nota[{$matricula["cod_matricula"]}]","Nota","",5,5,true,"","","onChange",false,true,"",true);

							}

							if( ! $this->exame )
							{
								$duplo = false;
								if ($this->pula_passo)
								{
									$duplo = true;
								}
								// nao estamos no exame, vamos ver se precisa apurar as faltas
								if( $detalhe_disciplina["apura_falta"] )
								{
									// materia apura falta, vamos ver se eh pra mostrar pra esse aluno
									if( ! $detalhe_curso["falta_ch_globalizada"] )
									{
										// se nesse curso a falta nao eh globalizada, exibe um campo de falta para todos os alunos
										$this->campoTexto("falta[{$matricula["cod_matricula"]}]"," &nbsp; &nbsp; &nbsp; Faltas", "0", 7, 5,true, false, $duplo);
									}
									else
									{
										// a falta eh globalizada, vamos ver se essa eh a ultima materia (que apura falta) que falta pro aluno
										// receber nota. Se for a ultima mostra o campo, se nao for deixa sem
										$objNotaAluno = new clsPmieducarNotaAluno();
										$restantes = $objNotaAluno->getQtdRestanteNotasAlunoNaoApuraFaltas($matricula["cod_matricula"],$this->ref_cod_serie_disciplina,$this->ref_cod_turma,$this->num_modulo,$this->ref_ref_cod_escola);
										if ($restantes==1)
										{
											$this->campoTexto("falta[{$matricula["cod_matricula"]}]"," &nbsp; &nbsp; &nbsp; Faltas", "0", 7, 5, true,false,$duplo,"","Falta global!" );
											$this->campoOculto("ultima_nota[{$matricula["cod_matricula"]}]","1" );
											
										}
										else
										{
											$this->campoRotulo("espaco[{$matricula["cod_matricula"]}]", " &nbsp; &nbsp; &nbsp; Faltas","Falta Globalizada &eacute; aplicada na ultima nota do aluno");
										}
									}
								}
								else
								{
									// a falta eh globalizada, vamos ver se essa eh a ultima materia (que apura falta) que falta pro aluno
									// receber nota. Se for a ultima mostra o campo, se nao for deixa sem
									if($detalhe_curso["falta_ch_globalizada"] )
									{
										$objNotaAluno = new clsPmieducarNotaAluno();
										$restantes = $objNotaAluno->getQtdRestanteNotasAlunoNaoApuraFaltas($matricula["cod_matricula"],$this->ref_cod_serie_disciplina,$this->ref_cod_turma,$this->num_modulo,$this->ref_ref_cod_escola);
										if ($restantes==1)
										{
											// eh a ultima disciplina
											$this->campoTexto("falta[{$matricula["cod_matricula"]}]"," &nbsp; &nbsp; &nbsp; Faltas", "0", 7, 5, true,false,$duplo,"","Falta global!" );
											$this->campoOculto("ultima_nota[{$matricula["cod_matricula"]}]","1" );
										}
										else
											$this->campoRotulo("espaco[{$matricula["cod_matricula"]}]", " &nbsp; &nbsp; &nbsp; Faltas","Mat&eacute;ria n&atilde;o apura falta");
									}
									else
									{
										$this->campoRotulo("espaco[{$matricula["cod_matricula"]}]", " &nbsp; &nbsp; &nbsp; Faltas","Mat&eacute;ria n&atilde;o apura falta");
									}

								}
								if ($this->pula_passo) 
								{
//									$opcoes_conceito = array("" => "Selecione", 1 => "Aprovado", 2 => "Reprovado");
//									$this->campoLista("conceito[{$matricula["cod_matricula"]}]", " &nbsp; &nbsp; &nbsp; Situação", $opcoes_conceito, "");
									$this->campoLista("resultado_final[{$matricula["cod_matricula"]}]","&nbsp; &nbsp; &nbsp;Resultado final",array(""=>"Selecione","1"=>"Aprovado","2"=>"Reprovado"),"","",false,"","",false,true);
								}
							}
							else
							{
								if (!is_array($lst_nota_aluno))
								{
									// esta no exame, entao nao apura faltas
									$this->campoRotulo("espaco[{$matricula["cod_matricula"]}]", " &nbsp; &nbsp; &nbsp; Faltas","Exame n&atilde;o apura falta");
								}
								// esta no exame, entao nao apura faltas
//								$this->campoRotulo("espaco[{$matricula["cod_matricula"]}]", " &nbsp; &nbsp; &nbsp; Faltas","Exame n&atilde;o apura falta");
							}
						}
						if (!$matriculas_exame_disponivel && $this->exame)
						{
							echo "<script>
										alert('Todos os alunos estão com notas do exame nessa disciplina');
										window.location='educar_turma_nota_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}';
								  </script>";
						}
					}
				}
				else
				{
					$this->mensagem = "Neste m&oacute;dulo n&atilde;o existe nenhum aluno aguardando uma nota nesta disciplina";
					$this->url_cancelar  = "educar_turma_nota_cad2.php?ref_cod_turma={$this->ref_cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}";
					$this->nome_url_cancelar = "Voltar";
					$this->botao_enviar = false;
				}
			}
			else if( $this->passo == 2 )
			{
				// tela onde o professor confirma excessoes
				$objTipoAvaliacaoValor = new clsPmieducarTipoAvaliacaoValores();
							
				$this->campoQuebra2();
				$this->campoRotulo("media_normal","M&eacute;dia",$objTipoAvaliacaoValor->nomeNota( $detalhe_curso["media"], $detalhe_curso["ref_cod_tipo_avaliacao"] ));
				$this->campoRotulo("media_exame","M&eacute;dia Exame",$objTipoAvaliacaoValor->nomeNota( $detalhe_curso["media_exame"], $detalhe_curso["ref_cod_tipo_avaliacao"] ) );

				if( $detalhe_curso["falta_ch_globalizada"] )
				{
					$tipo_falta = "Globalizada";
				}
				else
				{
					$tipo_falta = "por Disciplina";
				}
				$this->campoRotulo("tipo_falta","Tipo de Falta",$tipo_falta);
				$this->campoRotulo("carga_horaria","Carga Hor&aacute;ria",$detalhe_curso["carga_horaria"]);
				$this->campoRotulo("hora_falta","Horas por Falta",$detalhe_curso["hora_falta"]);
				$this->campoRotulo("frequencia_minima1","Frequencia M&aacute;ima (%)",$detalhe_curso["frequencia_minima"] . "%");
				$this->campoRotulo("frequencia_minima2","Frequencia M&iacute;nima (Horas)",ceil( ($detalhe_curso["frequencia_minima"] / 100 ) * $detalhe_curso["carga_horaria"] ) );
				if ($detalhe_curso["hora_falta"])
				{
					$maximo_faltas = floor( ( $detalhe_curso["carga_horaria"] / $detalhe_curso["hora_falta"] ) - ( ( $detalhe_curso["frequencia_minima"] / 100 ) * $detalhe_curso["carga_horaria"] ) / $detalhe_curso["hora_falta"] );
				}
				else
				{
					$maximo_faltas = "Ilimitado";
				}
				$this->campoRotulo("frequencia_minima3","M&aacute;ximo de faltas (quantidade)",$maximo_faltas );

				// descobre o ano letivo em andamento
				$objEscolaAnoLetivo = new clsPmieducarEscolaAnoLetivo();
				$objEscolaAnoLetivo->setOrderby("data_cadastro DESC");
				$objEscolaAnoLetivo->setLimite(1);
				$lista_anoletivo = $objEscolaAnoLetivo->lista($this->ref_ref_cod_escola,null,null,null,1);
				if( is_array($lista_anoletivo) )
				{
					foreach ($lista_anoletivo as $value)
					{
						$ano = $value["ano"];
					}
				}

				$boletim = false;
				$descricao = "";
				$objExcessoes = new clsPmieducarMatriculaExcessao();
				$lista_excessoes = $objExcessoes->lista(null,$this->ref_cod_turma,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,true,true,IS_NULL);
				if( is_array($lista_excessoes) )
				{
					$this->campoQuebra2();
					$this->campoRotulo("descricao","Aviso","Existem alunos que reprovaram por falta e pegaram exame.<br>Você deve decidir se permitirá que eles façam exame ou reprovem.");
					$opcoes_excessao = array(""=>"Selecione","0"=>"Reprovar","1"=>"Permitir Exame");
					// ainda tem alguma excessao que precisa de exame
					foreach ($lista_excessoes AS $excessao)
					{
						$this->campoQuebra2();
						$this->campoQuebra2("#E4E9ED",20);

						$objMatricula = new clsPmieducarMatricula($excessao["ref_cod_matricula"]);
						$detalhe_matricula = $objMatricula->detalhe();

						$this->campoRotulo("nome[{$excessao["ref_cod_matricula"]}]","Aluno", "{$detalhe_matricula["nome"]}");

						// boletim
						$boletim = $this->getBoletimAluno($excessao["ref_cod_matricula"],$ano);

						$this->campoRotulo("boletim[{$excessao["cod_aluno_excessao"]}]","Boletim", $boletim["boletim"]);

						$this->campoLista("excessao[{$excessao["cod_aluno_excessao"]}]","Resultado final",$opcoes_excessao,false,"",false,"","",false,false);
						$this->campoRotulo("sugestao[{$excessao["cod_aluno_excessao"]}]","Autom&aacute;tico", $boletim["automatico"] );

						$this->campoQuebra2("#FFFFFF",20);
					}
				}
				else
				{
					$lista_excessoes = $objExcessoes->lista(null,$this->ref_cod_turma,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,true,false);
					if( is_array($lista_excessoes) )
					{
						$this->campoQuebra2();
						$this->campoRotulo("descricao","Aviso","Existem alunos que reprovaram por falta mas foram aprovados em notas.<br>VocÃª deve decidir se eles serï¿½o aprovados ou reprovados.");
						$opcoes_excessao = array(""=>"Selecione","0"=>"Reprovar","2"=>"Aprovar");
						// so existem excessoes para aprovacao direta
						foreach ($lista_excessoes AS $excessao)
						{
							$this->campoQuebra2();
							$this->campoQuebra2("#E4E9ED",20);

							$objMatricula = new clsPmieducarMatricula($excessao["ref_cod_matricula"]);
							$detalhe_matricula = $objMatricula->detalhe();

							$this->campoRotulo("nome[{$excessao["ref_cod_matricula"]}]","Aluno", "{$detalhe_matricula["nome"]}");

							// boletim
							$boletim = $this->getBoletimAluno($excessao["ref_cod_matricula"],$ano);

							$this->campoRotulo("boletim[{$excessao["cod_aluno_excessao"]}]","Boletim", $boletim["boletim"]);

							$this->campoLista("excessao[{$excessao["cod_aluno_excessao"]}]","Resultado final",$opcoes_excessao,false,"",false,"","",false,false);
							$this->campoRotulo("sugestao[{$excessao["cod_aluno_excessao"]}]","Autom&aacute;tico", $boletim["automatico"] );

							$this->campoQuebra2("#FFFFFF",20);
						}
					}
					else
					{
						// por algum motivo ele mandou o cara pra tela de excessoes, mas nao existe nenhuma excessao
						$this->campoRotulo("alerta","Alerta","Erro: [4] Nenhum aluno reprovado por faltas pendente.");
//						header("location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}");
//						die();
					}
				}
			}
			else if( $this->passo == 3 )
			{
				// vamos exibir alguns dados extras sobre o curso (padroes de aprovacao / reprovacao)
				$this->campoQuebra2();
				$objTipoAvaliacaoValor = new clsPmieducarTipoAvaliacaoValores();
				$this->campoRotulo("media_normal","M&eacute;dia",$objTipoAvaliacaoValor->nomeNota( $detalhe_curso["media"], $detalhe_curso["ref_cod_tipo_avaliacao"] ));
				$this->campoRotulo("media_exame","M&eacute;dia Exame",$objTipoAvaliacaoValor->nomeNota( $detalhe_curso["media_exame"], $detalhe_curso["ref_cod_tipo_avaliacao"] ));

				if( $detalhe_curso["falta_ch_globalizada"] )
				{
					$tipo_falta = "Globalizada";
				}
				else
				{
					$tipo_falta = "por Disciplina";
				}
				$this->campoRotulo("tipo_falta","Tipo de Falta",$tipo_falta);
				$this->campoRotulo("carga_horaria","Carga Hor&aacute;ria",$detalhe_curso["carga_horaria"]);
				$this->campoRotulo("hora_falta","Horas por Falta",$detalhe_curso["hora_falta"]);
				$this->campoRotulo("frequencia_minima1","Frequencia M&iacute;nima (%)",$detalhe_curso["frequencia_minima"] . "%");
				$this->campoRotulo("frequencia_minima2","Frequencia M&iacute;nima (Horas)",ceil( ($detalhe_curso["frequencia_minima"] / 100 ) * $detalhe_curso["carga_horaria"] ) );
				if ($detalhe_curso["hora_falta"])
				{
					$maximo_faltas = floor( ( $detalhe_curso["carga_horaria"] / $detalhe_curso["hora_falta"] ) - ( ( $detalhe_curso["frequencia_minima"] / 100 ) * $detalhe_curso["carga_horaria"] ) / $detalhe_curso["hora_falta"] );
				}
				else
				{
					$maximo_faltas = "Ilimitado";
				}
				$this->campoRotulo("frequencia_minima3","M&aacute;ximo de faltas (quantidade)",$maximo_faltas );

//				mostra a lista de todos alunos e a opcao de aprovar/reprovar (exceto os ja aprovados/reprovados)
				$matriculas = $objTurma->matriculados();
				if($matriculas)
				{
					// descobre o ano letivo em andamento
					$objEscolaAnoLetivo = new clsPmieducarEscolaAnoLetivo();
					$objEscolaAnoLetivo->setOrderby("data_cadastro DESC");
					$objEscolaAnoLetivo->setLimite(1);
					$lista_anoletivo = $objEscolaAnoLetivo->lista($this->ref_ref_cod_escola,null,null,null,1);
					if( is_array($lista_anoletivo) )
					{
						foreach ($lista_anoletivo as $value)
						{
							$ano = $value["ano"];
						}
					}

					$objMat = new clsPmieducarMatricula();
					$objMat->setOrderby("nome ASC");
					$lista_matriculas = $objMat->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$matriculas);
					if( is_array( $lista_matriculas ) )
					{
						foreach ($lista_matriculas AS $matricula)
						{
							$this->campoQuebra2();
							$this->campoQuebra2("#E4E9ED",20);

							$this->campoRotulo("nome[{$matricula["cod_matricula"]}]","Aluno", "{$matricula["nome"]}");

							// boletim
							$boletim = $this->getBoletimAluno($matricula["cod_matricula"],$ano);

							$this->campoRotulo("boletim[{$matricula["cod_matricula"]}]","Boletim", $boletim["boletim"]);

							$this->campoRotulo("sugestao[{$matricula["cod_matricula"]}]","Autom&aacute;tico", $boletim["automatico"] );

//							if ($this->pula_passo)
//								$this->campoLista("resultado_final[{$matricula["cod_matricula"]}]","Resultado final",array(""=>"Selecione","1"=>"Aprovado","2"=>"Reprovado"),"","",false,"","",false,true);
//							else
								$this->campoLista("resultado_final[{$matricula["cod_matricula"]}]","Resultado final",array(""=>"Selecione","1"=>"Aprovado","2"=>"Reprovado"),"","",false,"","",false,false);

							$this->campoQuebra2("#FFFFFF",20);
						}
					}
				}
				else
				{
					$this->mensagem = "Erro ao procurar alunos sem nota nessa disciplina";
				}
			}
//			guardando dados para os outros passos
			if ($this->pula_passo) 
			{
				$this->campoOculto("passo", 3);
				$this->campoOculto("pula_passo", 1);
				$this->campoOculto("ref_cod_tipo_avaliacao", $this->ref_cod_tipo_avaliacao);
//				$this->pula_passo = false;
			}
			else 
				$this->campoOculto("passo",$this->passo + 1);
			$this->campoOculto("ref_cod_turma",$this->ref_cod_turma);
			$this->campoOculto("ref_ref_cod_escola",$this->ref_ref_cod_escola);
			$this->campoOculto("ref_ref_cod_serie",$this->ref_ref_cod_serie);
			$this->campoOculto("ref_cod_curso",$this->ref_cod_curso);
			$this->campoOculto("falta_ch_globalizada",$this->falta_ch_globalizada);
			$this->campoOculto("num_modulo",$this->num_modulo);
			$this->campoOculto("max_modulos",$this->max_modulos);
			$this->campoOculto("exame",$this->exame);
		}
		else
		{
			$this->campoRotulo("erro","Erro","Turma inexistente");
			return false;
		}
		return true;
	}

	/**
	 * Cria um boletim em HTML para a matricula $matricula
	 *
	 * @param int $matricula
	 * @return array
	 */
	function getBoletimAluno($matricula,$ano)
	{
		$media = false;
		$reprovado = 0;
		$falta_globalizada = 0;
		$array_status = array("Aprovado","Reprovado","Reprovado por faltas");

		$objCurso = new clsPmieducarCurso($this->ref_cod_curso);
		$detalhe_curso = $objCurso->detalhe();

		// monta o boletim do aluno
		$cor = array("#ffffff", "#dce6f1" );
		$corDest = array("#e1c9c9","#efe7e7");
		$boletim = "
		<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" style=\"border-width:2px;color#00000;border-style:solid;\">
			<tr bgcolor=\"#ffffff\">
				<td rowspan=\"2\" class=\"cell_normal\" align=\"center\">Disciplina</td>
		";
		$linha2 = "<tr bgcolor=\"#ffffff\">";
		$objTurmaModulo = new clsPmieducarAnoLetivoModulo();
		$objTurmaModulo->setOrderby("sequencial ASC");
		$lista_modulos = $objTurmaModulo->lista($ano,$this->ref_ref_cod_escola);
		if( is_array($lista_modulos) )
		{
			foreach ($lista_modulos AS $modulo)
			{
				$objModulo = new clsPmieducarModulo( $modulo["ref_cod_modulo"] );
				$detModulo = $objModulo->detalhe();
				$boletim .= "<td colspan=\"2\" class=\"cell_separa\" align=\"center\">{$detModulo["nm_tipo"]}</td>";
				$linha2 .= "
				<td class=\"cell_separa\" align=\"center\" width=\"40\">Nota</td>
				<td class=\"cell_normal\" align=\"center\" width=\"40\">Faltas</td>
				";
			}
		}
		if( ! is_null( $detalhe_curso["media_exame"] ) )
		{
			$boletim .= "<td class=\"cell_separa\" align=\"center\">Exame</td>";
			$linha2 .= "<td class=\"cell_separa\" align=\"center\">Nota</td>";
		}
		$boletim .= "<td colspan=\"2\" class=\"cell_separa\" align=\"center\">Resultado</td>
		</tr>
		";
		$linha2 .= "<td class=\"cell_separa\" align=\"center\" width=\"40\">Media</td><td class=\"cell_normal\" align=\"center\" width=\"40\">Faltas</td></tr>";
		$boletim .= $linha2;
		$i = 0;

		$objDisciplinaSerie = new clsPmieducarDisciplinaSerie();
		$lstDisciplinas = $objDisciplinaSerie->lista(null,$this->ref_ref_cod_serie,1);
		foreach ($lstDisciplinas AS $disciplina)
		{
			$i++;
			$faltas_total = 0;
			$notas_total = 0;
			$objDispensa = new clsPmieducarDispensaDisciplina($matricula,$disciplina["ref_cod_serie"],$this->ref_ref_cod_escola,$disciplina["ref_cod_disciplina"]);
			$dispensa = $objDispensa->existe();

			$objDisciplina = new clsPmieducarDisciplina( $disciplina["ref_cod_disciplina"] );
			$detDisciplina = $objDisciplina->detalhe();

			$boletim .= "
			<tr>
				<td bgcolor=\"" . $cor[$i%2] . "\"  class=\"cell_normal\">{$detDisciplina["nm_disciplina"]}</td>
			";
			if( is_array($lista_modulos) )
			{
				reset($lista_modulos);
				foreach ($lista_modulos AS $modulo)
				{
					if( $dispensa )
					{
						$boletim .= "
						<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\" colspan=\"2\" align=\"center\">dispensa</td>
						";
					}
					else
					{
						// pegando a falta desse modulo
						$faltas = 0;
						$objFaltaAluno = new clsPmieducarFaltaAluno();
						$objFaltaAluno->setOrderby("data_cadastro DESC");
						$objFaltaAluno->setLimite(1);
						$lista_faltas = $objFaltaAluno->lista(null,null,null,$disciplina["ref_cod_serie"],$this->ref_ref_cod_escola,$disciplina["ref_cod_disciplina"],$matricula,null,null,null,null,null,1,$modulo["sequencial"]);
						if( is_array($lista_faltas) )
						{
							foreach ($lista_faltas AS $falta_modulo)
							{
								$faltas = $falta_modulo["faltas"];
							}
						}

						$nota = null;
						$objNotaAluno = new clsPmieducarNotaAluno();
						$objNotaAluno->setOrderby("data_cadastro DESC");
						$objNotaAluno->setLimite(1);
//						echo "<br><br><br>null,null,null,{$this->ref_ref_cod_serie},{$this->ref_ref_cod_escola},{$disciplina["ref_cod_disciplina"]},{$matricula},null,null,null,null,null,1,{$modulo["sequencial"]}\n<br>\n";
						$lista_notas = $objNotaAluno->lista(null,null,null,$disciplina["ref_cod_serie"],$this->ref_ref_cod_escola,$disciplina["ref_cod_disciplina"],$matricula,null,null,null,null,null,null,1,$modulo["sequencial"]);
//						print_r($lista_notas);
//						echo "<br>";
						if( is_array($lista_notas) )
						{
							foreach ($lista_notas AS $nota_modulo)
							{
								$objNotaValor = new clsPmieducarTipoAvaliacaoValores($nota_modulo["ref_ref_cod_tipo_avaliacao"],$nota_modulo["ref_sequencial"]);
//								echo "{$nota_modulo["ref_ref_cod_tipo_avaliacao"]},{$nota_modulo["ref_sequencial"]}\n<br>\n";
								$det_nota_valor = $objNotaValor->detalhe();
								$nota = $det_nota_valor["nome"];
								$notas_total += $det_nota_valor["valor"];
							}
							unset($lista_notas);
						}

						$nota = $nota ? $nota: "&nbsp;";
						$faltas = $faltas ? $faltas: 0;
						$faltas_total += $faltas;

						$boletim .= "
						<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\">{$nota}</td>
						<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_normal\">{$faltas}</td>
						";
					}
				}
				$media = $notas_total / count($lista_modulos);

				$media_aprovacao = $detalhe_curso["media"];
				if( ! is_null( $detalhe_curso["media_exame"] ) )
				{
					// o curso tem exame
					if( $dispensa )
					{
						$boletim .= "
						<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\" align=\"center\">dispensa</td>
						";
					}
					else
					{
						// echo count($lista_modulos);

						$objNotaAluno = new clsPmieducarNotaAluno();
						$objNotaAluno->setLimite(1);
						$objNotaAluno->setOrderby("data_cadastro DESC");
						$listaNotas = $objNotaAluno->lista(null,null,null,$disciplina["ref_cod_serie"],null,$disciplina["ref_cod_disciplina"],$matricula,null,null,null,null,null,null,null,count($lista_modulos) + 1);
						if( $listaNotas )
						{
							// ja recebeu a nota do exame
							foreach ( $listaNotas AS $nota_exame )
							{
								$objNotaValor = new clsPmieducarTipoAvaliacaoValores($nota_exame["ref_ref_cod_tipo_avaliacao"],$nota_exame["ref_sequencial"]);
								$detNotaValor = $objNotaValor->detalhe();
								$nota_exame = $detNotaValor["nome"];
								// print_r($detNotaValor);
								$notas_total += $detNotaValor["valor"];

								$nota_exame = $nota_exame ? $nota_exame: "-";
								$boletim .= "<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\">{$nota_exame}</td>";
							}
							$media = $notas_total / ( count($lista_modulos) + 1);

							$media_aprovacao = $detalhe_curso["media_exame"];
						}
						else
						{
							$nota_exame = $nota_exame ? $nota_exame: "-";
							$boletim .= "<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\">-</td>";
						}
					}
				}

				if( $media !== false )
				{
					$objNotaValor = new clsPmieducarTipoAvaliacaoValores();
					$objNotaValor->setLimite(1);
					$objNotaValor->setOrderby("valor DESC");
					$notas_media = $objNotaValor->lista($detalhe_curso["ref_cod_tipo_avaliacao"],null,null,null,$media,$media);
					foreach ($notas_media AS $nota_media)
					{
						$media = $nota_media["nome"];
						if( $nota_media["valor"] < $media_aprovacao )
						{
							$reprovado = 1;
						}
					}
				}
				else
				{
					$media = "&nbsp;";
				}
				if( $dispensa )
				{
					$boletim .= "
					<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\" colspan=\"2\" align=\"center\">dispensa</td>
					";
				}
				else
				{
					$boletim .= "
					<td bgcolor=\"" . $cor[$i%2] . "\" class=\"cell_separa\">{$media}</td>
					<td bgcolor=\"" . $cor[$i%2] . "\"  class=\"cell_normal\">{$faltas_total}</td>
					";
				}
			}

			if( ! $detalhe_curso["falta_ch_globalizada"] && $maximo_faltas != "Ilimitado" )
			{
				if( $faltas_total > $maximo_faltas )
				{
					$reprovado = 2;
				}
			}

			$falta_globalizada += $faltas_total;
		}

		if( $detalhe_curso["falta_ch_globalizada"] && $maximo_faltas != "Ilimitado" )
		{
			if( $falta_globalizada > $maximo_faltas )
			{
				$reprovado = 2;
			}
		}

		$boletim .= "</table>";
		return array( "boletim"=>$boletim,"automatico"=>$array_status[$reprovado] );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 650, $this->pessoa_logada, 7,  "educar_turma_mvto_det.php" );

		if( $this->passo == 1 )
		{
			// selecionou o curso, nao cadastra nada
			return true;
		}
		else if( $this->passo == 2 )
		{

			// deu notas e faltas aos alunos
			$objCurso = new clsPmieducarCurso($this->ref_cod_curso);
			$detalhe_curso = $objCurso->detalhe();

			$db = new clsBanco();

			$qtd_disciplinas = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_serie = '{$this->ref_cod_serie_disciplina}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ativo = 1");

			foreach ($this->nota as $matricula => $sequencial)
			{
				// cadastra a nota
				if($this->is_nota_exame)
				{
					$nota = str_replace(",",".",$sequencial);
					$objNotaAluno = new clsPmieducarNotaAluno(null,null,null,$this->ref_cod_serie_disciplina,$this->ref_ref_cod_escola,$this->ref_cod_disciplina,$matricula,null,$this->pessoa_logada,null,null,null,$this->num_modulo,null, $nota);
				}
				else
				{
					$objNotaAluno = new clsPmieducarNotaAluno(null,$sequencial,$detalhe_curso["ref_cod_tipo_avaliacao"],$this->ref_cod_serie_disciplina,$this->ref_ref_cod_escola,$this->ref_cod_disciplina,$matricula,null,$this->pessoa_logada,null,null,null,$this->num_modulo, null);
				}

				$existe_nota = $objNotaAluno->lista(null,null,$detalhe_curso["ref_cod_tipo_avaliacao"],$this->ref_cod_serie_disciplina,$this->ref_ref_cod_escola,$this->ref_cod_disciplina,$matricula,null,null,null,null,null,null,1,$this->num_modulo, null,null);

				/**
				 * somente cadastra
				 * se nao tiver nenhuma nota
				 * cadastrada para a disciplina
				 */
				if($existe_nota)
					$cadastrado = $objNotaAluno->edita();
				else
					$cadastrado = $objNotaAluno->cadastra();

				$existe_nota = null;

				$ultima_nota = false;

				if( $cadastrado )
				{

					// nota cadastrada com sucesso
					// verifica se essa eh a ultima nota desse modulo. Se for passa o aluno pro proximo modulo
					$qtd_dispensas = (int) $db->CampoUnico("SELECT COUNT(0) AS dispensas FROM pmieducar.dispensa_disciplina WHERE ref_cod_matricula = '{$matricula}' AND ativo = 1");
					$qtd_notas = (int)$db->CampoUnico("SELECT COUNT(0) AS notas FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$matricula}' AND ativo = 1 AND modulo = '{$this->num_modulo}'");

					if( $qtd_dispensas + $qtd_notas >= $qtd_disciplinas )
					{
						// eh a ultima nota do modulo, vamos passar o aluno adiante
						$ultima_nota = true;
						$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada);
						$det_matricula = $objMatricula->detalhe();
						$max_modulo_nota = (int)$db->CampoUnico("SELECT max(modulo) FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$matricula}' AND ativo = 1");
						/**
						 * so avança o modulo
						 * caso ele seja igual ao da maior nota
						 * e que seja a ultima disciplina
						 */
						if($det_matricula['modulo'] <= $max_modulo_nota)
						{
							$objMatricula->avancaModulo();

						}
					}

					// tratamento para faltas
					if( ! $this->falta_ch_globalizada )
					{
						// se a falta nao for globalizada adiciona falta
						$this->falta[$matricula] = ($this->falta[$matricula]) ? $this->falta[$matricula] : 0;
						$objFaltaAluno = new clsPmieducarFaltaAluno(null,null,$this->pessoa_logada,$this->ref_cod_serie_disciplina,$this->ref_ref_cod_escola,$this->ref_cod_disciplina,$matricula,$this->falta[$matricula],null,null,null,$this->num_modulo);
						$existe_falta = $objFaltaAluno->lista(null,null,null,$this->ref_cod_serie_disciplina,$this->ref_ref_cod_escola,$this->ref_cod_disciplina,$matricula,null,null,null,null,null,1,$this->num_modulo);
						/**
						 * somente cadastra se
						 * jah nao tiver sido cadastrado
						 */
						if($existe_falta)
							$objFaltaAluno->edita();
						else
							$objFaltaAluno->cadastra();
					}
					else
					{
						// falta eh globalizada
						if($this->ultima_nota[$matricula])
						{
							// essa eh a ultima nota do aluno nesse modulo, vamos adicionar a falta globalizada dele
							$this->falta[$matricula] = ($this->falta[$matricula]) ? $this->falta[$matricula] : 0;
							$objFaltas = new clsPmieducarFaltas($matricula,$this->num_modulo,$this->pessoa_logada,$this->falta[$matricula]);
							if($objFaltas->existe())
								$objFaltas->edita();
							else
								$objFaltas->cadastra();
						}
					}
					
					// quando for o ultimo modulo o aluno deve ser aprovado, reprovado, marcado como excessao, ou colocado em exame
					if( $ultima_nota && $this->num_modulo == $this->max_modulos ) 
					{
						// esta na ultima nota do ultimo modulo
						$objEscolaSerieDisciplina = new clsPmieducarEscolaSerieDisciplina();
						$listaEscolaSerieDisciplina = $objEscolaSerieDisciplina->lista($this->ref_cod_serie_disciplina,$this->ref_ref_cod_escola,null,1);
						//$this->ref_cod_serie_disciplina $this->ref_ref_cod_serie
						$reprovado_por_nota = false;
						$existiu_excessao = false;

						$obj_serie = new clsPmieducarSerie($this->ref_cod_serie_disciplina);
						$det_serie = $obj_serie->detalhe();
						$media_especial = dbBool($det_serie['media_especial']);
						if( is_array($listaEscolaSerieDisciplina) )
						{
							// percorre todas as disciplinas
							$reprovou_ultima_nota = false;
							$foi_aprovado = null;
							foreach ($listaEscolaSerieDisciplina as $value)
							{
								$excessao = false;
								$nota = false;

								//  ve as faltas
								if( ! $this->falta_ch_globalizada )
								{
									$objFaltaAluno = new clsPmieducarFaltaAluno();
									$faltas = $objFaltaAluno->total_faltas_disciplina($matricula,$value["ref_cod_disciplina"],$value["ref_ref_cod_serie"]);

									if( $reprovou )
									{
										// se reprovou cria uma excessao
										$existiu_excessao = true;
										$excessao = true;
									}
								}

								/**
								 * se a media for especial somente
								 * verifica se nao esta reprovado por falta
								 */
								if($media_especial)
									continue;

								if (!dbBool($det_serie["ultima_nota_define"]))
								{
									//  ve a media
									/**
									 * @author HY 15-12-2006
									 * @see quando for dar as notas e for calcular a ultima
									 * ao fazer a media e essa nota estiver abaixo nao
									 * pode ser feito o arredondamento, somente se estiver
									 *  acima da media deixando o aluno em exame
									 */
									$objNotaAluno = new clsPmieducarNotaAluno();
									$media = $objNotaAluno->getMediaAluno($matricula,$value["ref_cod_disciplina"],$value["ref_ref_cod_serie"]/*$value["ref_ref_cod_escola"]*/,$this->max_modulos,$detalhe_curso["media"]);
									if( $media < $detalhe_curso["media"] )
									{
										//  se reprovar em alguma marca uma flag de reprovado por nota (nao edita o aluno porque ele ainda vai fazer exame)
										$reprovado_por_nota = true;
										$nota = true;
									}						
								}
								else 
								{
//									echo "<pre>"; print_r($detalhe_curso); die();
									$objNotaAluno = new clsPmieducarNotaAluno();
									$ultima_nota_modulo = $objNotaAluno->getUltimaNotaModulo($matricula, $value["ref_cod_disciplina"], $value["ref_ref_cod_serie"], $this->num_modulo);
									if ($ultima_nota_modulo < $detalhe_curso["media"])
									{
										$foi_aprovado = 2;
										$reprovou_ultima_nota = true;
									}
								}

								if( $excessao )
								{
									// existiu uma excessao entao cria um registro de excessao no banco
									$objExcessao = new clsPmieducarMatriculaExcessao(null,$matricula,$this->ref_cod_turma,1,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$value["ref_cod_disciplina"],true,$nota);
									$objExcessao->cadastra();
								}
							}
							if (dbBool($det_serie["ultima_nota_define"]))
							{
								if ($reprovou_ultima_nota)
								{
									$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,2);
									$objMatricula->edita();
								}
								else 
								{
									$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,1);
									$objMatricula->edita();
									$foi_aprovado = 1;
								}
							}

							/**
							 * calcula outro modo de média se for especial
							 */

							if($media_especial)
							{
								$objNotaAluno = new clsPmieducarNotaAluno();
								$media = $objNotaAluno->getMediaEspecialAluno($matricula,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$this->max_modulos,$detalhe_curso["media"]);

								if( $media < $detalhe_curso["media"] )
								{
									//  se reprovar marca uma flag de reprovado por nota (
									$reprovado_por_nota = true;
									$nota = true;
								}

							}
						}
						
						
						if( ! $reprovado_por_nota && ! $existiu_excessao && !dbBool($det_serie["ultima_nota_define"]))
						{
							// nao foi reprovado em nenhuma disciplina e nao teve nenhuma excessao
							//marca como aprovado
							$foi_aprovado = 1;
							$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,1);
							$objMatricula->edita();
						}
						/**
						 * media especial e reprovado por falta.. marca aluno como reprovado se nao marca como aprovado
						 */
						if($media_especial)
						{
							$aprovado =  $reprovado_por_nota ? '2' : '1';
							$foi_aprovado = $aprovado;
							$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,$aprovado);
							$objMatricula->edita();
						}
					}
					else if( $this->num_modulo - 1 == $this->max_modulos )
					{
						// se estiver recebendo nota do exame temos que ver se eh a ultima, se for temos que aprovar/reprovar o aluno
						// num_modulo == max_modulo = ultimo modulo
						// num_modulo - 1 == max_modulo = exame
						// esta no exame
						$objNotaAluno = new clsPmieducarNotaAluno();
						$qtd_exames = $objNotaAluno->getQtdMateriasExame($matricula,$this->max_modulos,$detalhe_curso["media"], true);
						$qtd_notas_exame = $objNotaAluno->getQtdNotasExame($matricula,$this->max_modulos);
						//die("recebendo nota de exame!!!! [{$qtd_exames}] [{$qtd_notas_exame}]");
						if( $qtd_exames == $qtd_notas_exame )
						{
							// eh a ultima disciplina do exame
							$reprovado = false;

							$disciplinas_exame = $objNotaAluno->getDisciplinasExameDoAluno($matricula,$this->max_modulos,$detalhe_curso["media"], true);
							if( is_array($disciplinas_exame) )
							{
								foreach ($disciplinas_exame as $disciplina)
								{
									
									$media_exame = $objNotaAluno->getMediaAlunoExame($matricula, $disciplina["cod_disciplina"], $disciplina["cod_serie"], $this->max_modulos);
									// se reprovou marca como reprovado e sai do loop (break)
									//abaixo original
//									$media_exame = $objNotaAluno->getMediaAluno($matricula,$disciplina["cod_disciplina"],$disciplina["cod_serie"],$this->max_modulos + 1, false, true);
									if( $media_exame < $detalhe_curso["media_exame"] )
									{
										// reprovou
										//remove possiveis excessoes
										$objExcessoes = new clsPmieducarMatriculaExcessao();
										$objExcessoes->excluirPorMatricula($matricula);

										// marca como reprovado
										$foi_aprovado = 2;
										$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,2);
										$objMatricula->edita();
										$reprovado = true;
										break;
									}
								}
							}
							if( ! $reprovado )
							{
								// se chegar aqui sem ter sido reprovado, marca como aprovado
								//remove possiveis excessoes
								$objExcessoes = new clsPmieducarMatriculaExcessao();
								$objExcessoes->excluirPorMatricula($matricula);

								// aprova
								$foi_aprovado = 1;
								$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,1);
								$objMatricula->edita();
							}
							else
							{
								// se chegar aqui sem ter sido reprovado, marca como aprovado
								//remove possiveis excessoes
								$objExcessoes = new clsPmieducarMatriculaExcessao();
								$objExcessoes->excluirPorMatricula($matricula);

								// reprova
								$foi_aprovado = 2;
								$objMatricula = new clsPmieducarMatricula($matricula,null,null,null,$this->pessoa_logada,null,null,2);
								$objMatricula->edita();
							}
						}
					}
				}
					/**************HISTORICO ESCOLAR****************************/
				if ($foi_aprovado == 1 || $foi_aprovado == 2)
				{
					$obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
					$det_serie = $obj_serie->detalhe();
					$carga_horaria_serie = $det_serie["carga_horaria"];

					$obj_escola = new clsPmieducarEscola( $this->ref_ref_cod_escola );
					$det_escola = $obj_escola->detalhe();
					$ref_idpes = $det_escola["ref_idpes"];
					$this->ref_cod_instituicao = $det_escola["ref_cod_instituicao"];

					$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
					$lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_ref_cod_escola,null,null,null,1,null,null,null,null,1 );
					if ( is_array($lst_ano_letivo) )
					{
						$det_ano_letivo = array_shift($lst_ano_letivo);
						$this->ano_letivo = $det_ano_letivo["ano"];
					}

					// busca informacoes da escola
					if ($ref_idpes)
					{
						$obj_escola = new clsPessoaJuridica($ref_idpes);
						$det_escola = $obj_escola->detalhe();
						$nm_escola = $det_escola["fantasia"];
						if($det_escola)
						{
							$cidade = $det_escola["cidade"];
							$uf = $det_escola["sigla_uf"];
						}
					}
					else
					{
						if ( class_exists( "clsPmieducarEscolaComplemento" ) )
						{
							$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
							$det_escola = $obj_escola->detalhe();

							$nm_escola = $det_escola["nm_escola"];
							$cidade = $det_escola["municipio"];
						}
					}
					$this->padrao_ano_escolar = $detalhe_curso["padrao_ano_escolar"];
					if ($this->padrao_ano_escolar)
					{
						$extra_curricular = 0;
					}
					else
					{
						$extra_curricular = 1;
					}

					$sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$matricula}";
					$db5 = new clsBanco();
					$total_faltas = $db5->CampoUnico($sql);

					$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $detalhe_curso["ref_cod_tipo_avaliacao"] );
					$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
					$this->conceitual = $det_tipo_avaliacao["conceitual"];
					$obj_aluno = new clsPmieducarMatricula($matricula);
					$det_aluno = $obj_aluno->detalhe();
					$this->ref_cod_aluno = $det_aluno["ref_cod_aluno"];
					$obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,null,null,$this->pessoa_logada,$det_serie['nm_serie'],$this->ano_letivo,$carga_horaria_serie,null,$nm_escola,$cidade,$uf,null,$foi_aprovado,null,null,1,$total_faltas,$this->ref_cod_instituicao,0,$extra_curricular,$matricula );
					$cadastrou2 = $obj->cadastra();
					if( $cadastrou2 && !$this->conceitual)
					{
						$obj_historico = new clsPmieducarHistoricoEscolar();
						$sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );

						$historico_disciplina = array();

						$obj_disciplina_hist = new clsPmieducarEscolaSerieDisciplina();
						$lst_disciplina_hist = $obj_disciplina_hist->lista($this->ref_ref_cod_serie, $this->ref_ref_cod_escola, null, 1);
						foreach ($lst_disciplina_hist as $disciplina_hist)
						{
							$objFaltaAluno = new clsPmieducarFaltaAluno();
							$faltas = $objFaltaAluno->total_faltas_disciplina($matricula,$disciplina_hist["ref_cod_disciplina"],$this->ref_ref_cod_serie);
							$historico_disciplina[$disciplina_hist["ref_cod_disciplina"]] = $faltas;
							$obj_nota_aluno = new clsPmieducarNotaAluno();
							$obj_nota_aluno->setOrderby("modulo ASC");
							$lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $disciplina_hist["ref_cod_disciplina"], $matricula, null, null, null, null, null, null, 1);
							$possui_nota_exame=false;
							foreach ($lst_nota_aluno as $nota_aluno)
							{
								if (dbBool($det_serie["ultima_nota_define"]))
								{
									$obj_tipo_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores($nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"], null, null, null, null, 1);
									$det_tipo_avaliacao_valores = $obj_tipo_avaliacao_valores->detalhe();
									$soma_notas[$disciplina_hist["ref_cod_disciplina"]] = $det_tipo_avaliacao_valores["valor"];
								}
								else 
								{
									if ($nota_aluno["nota"])
									{
										$soma_notas[$disciplina_hist["ref_cod_disciplina"]] += $nota_aluno["nota"] * 2;
										$possui_nota_exame=true;
									}
									else
									{
										$obj_tipo_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores($nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"], null, null, null, null, 1);
										$det_tipo_avaliacao_valores = $obj_tipo_avaliacao_valores->detalhe();
										$soma_notas[$disciplina_hist["ref_cod_disciplina"]] += $det_tipo_avaliacao_valores["valor"];
									}
								}
							}
							if (!dbBool($det_serie["ultima_nota_define"]))
							{							
								if ($possui_nota_exame)
								{
									$soma_notas[$disciplina_hist["ref_cod_disciplina"]] /= ($this->num_modulo+1);
								}
								else
								{
									$soma_notas[$disciplina_hist["ref_cod_disciplina"]] /= $this->max_modulos;
								}
							}


							/*************FALTAS******************/
							//											$this->falta_ch_globalizada = $detalhe_curso["falta_ch_globalizada"];
							$this->ref_cod_tipo_avaliacao = $detalhe_curso["ref_cod_tipo_avaliacao"];
							if (!$detalhe_curso["falta_ch_globalizada"])
							{
								$obj_falta_aluno = new clsPmieducarFaltaAluno();
								$lst_falta_aluno = $obj_falta_aluno->lista(null,null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $disciplina_hist["ref_cod_disciplina"], $matricula, null, null, null, null, null, 1);
								if (is_array($lst_falta_aluno))
								{
									foreach ($lst_falta_aluno as $key => $falta_aluno)
									{
										$soma_faltas[$disciplina_hist["ref_cod_disciplina"]][$key] = $falta_aluno["faltas"];
									}
								}
							}
						}
						$faltas_media_aluno = array();
						if (is_array($soma_faltas))
						{
							foreach ($soma_faltas as $cod_disciplina => $faltas)
							{
								foreach ($array_faltas as $falta)
								{
									$faltas_media_aluno[$disciplina] += $falta;
								}
							}
						}

						$obj_historico = new clsPmieducarHistoricoEscolar();
						$sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );

						$historico_disciplina = array();
						foreach ($soma_notas as $key => $nota)
						{
							$historico_disciplina[$key] = array( $nota, $faltas_media_aluno[$key] );
						}
						foreach ($historico_disciplina as $cod_disciplina => $campo)
						{
							$obj_disciplina_cad = new clsPmieducarDisciplina($cod_disciplina);
							$det_disciplina_cad = $obj_disciplina_cad->detalhe();
							$nm_disciplina = $det_disciplina_cad["nm_disciplina"];
							$obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
							$lst_avaliacao_valores = $obj_avaliacao_valores->lista($this->ref_cod_tipo_avaliacao, null, null, null, $campo[0], $campo[0]);
							if (is_array($lst_avaliacao_valores))
							{
								$det_avaliacao_valores = array_shift($lst_avaliacao_valores);
								$nm_nota = $det_avaliacao_valores["nome"];
								$obj = new clsPmieducarHistoricoDisciplinas(null, $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1]);
								$cadastrou3 = $obj->cadastra();
								if (!$cadastrou3) {
									die("nao cadastrou");
								}
							}
						}
					}
					else if( !$cadastrou2 )
					{
						$this->mensagem = "Cadastro do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
						return false;
					}
				}
				/**************HISTORICO ESCOLAR****************************/ 
			}
			$this->ref_cod_disciplina = null;
			$this->passo = 0;
			return true;
		}
		else if( $this->passo == 3 )
		{
			if ($this->pula_passo)
			{
				$this->pula_passo = false;
				if (is_array($this->nota) && is_array($this->resultado_final) && is_array($this->falta))
				{
					foreach ($this->nota as $matricula => $value) 
					{
						$objTpAvalValores = new clsPmieducarTipoAvaliacaoValores($this->ref_cod_tipo_avaliacao, $value, null,
																				 null, null, null, 1);
						$valorNota = $objTpAvalValores->detalhe();
						$objNotaAluno = new clsPmieducarNotaAluno(null, $value, $this->ref_cod_tipo_avaliacao, 
												$this->ref_cod_serie_disciplina, $this->ref_ref_cod_escola, 
												$this->ref_cod_disciplina, $matricula, null, $this->pessoa_logada, 
												null, null, null, $this->num_modulo, $this->ref_cod_curso, $valorNota["valor"]);
						if (!$objNotaAluno->cadastra()) {
							die("não cadastrou (nota)");
						}
						$objMatricula = new clsPmieducarMatricula($matricula, null, null, null, $this->pessoa_logada, $this->pessoa_logada, 
																  null, $this->resultado_final[$matricula]);
						if (!$objMatricula->edita()) {
							die("não cadastrou (resultado final)");
						}
						$objFaltaAluno = new clsPmieducarFaltaAluno();										   
						$existe_falta = $objFaltaAluno->lista(null, null, null, $this->ref_cod_serie_disciplina,
															  $this->ref_ref_cod_escola, $this->ref_cod_disciplina,
															  $matricula, null, null, null, null, null, 1,
															  $this->modulo, $this->ref_cod_disciplina);
						if ($existe_falta) {
							$objFaltaAluno = new clsPmieducarFaltaAluno($existe_falta[0]['cod_falta_aluno'], $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_serie_disciplina,
																   $this->ref_ref_cod_escola, $this->ref_cod_disciplina,
																   $matricula, $this->falta[$matricula], null,
																   null, null, $this->num_modulo);		
							if (!$objFaltaAluno->edita()) {
								die("não editou (falta)");
							}
						} else { 				
							$objFaltaAluno = new clsPmieducarFaltaAluno(null, null, $this->pessoa_logada, $this->ref_cod_serie_disciplina,
																   $this->ref_ref_cod_escola, $this->ref_cod_disciplina,
																   $matricula, $this->falta[$matricula], null,
																   null, null, $this->num_modulo);				  
							if (!$objFaltaAluno->cadastra()) {
								die("não cadastrou (falta)");
							}
						}
					}
				} else {
					die("falto parâmetros");	
				}
			}
//			die("nao");
			// o professor decidiu sobre as excessoes
			foreach ($this->excessao as $cod_excessao => $sentenca)
			{
				$objExcessao = new clsPmieducarMatriculaExcessao($cod_excessao);
				$detalhe_excessao = $objExcessao->detalhe();
				// verifica se ela ainda existe porque se houve uma sentenca anterior para o mesmo aluno reprovando ele, todas as outras sentencas sao removidas
				if( $detalhe_excessao )
				{
					if( $sentenca == 0 )
					{
						// aluno reprovado
						// reprova aluno
						$objMatricula = new clsPmieducarMatricula($detalhe_excessao["ref_cod_matricula"],null,null,null,$this->pessoa_logada,null,null,2);
						$objMatricula->edita();
						// remove excessoes
						$objExcessao->excluirPorMatricula($detalhe_excessao["ref_cod_matricula"]);
						$this->passo = 0;
						return true;
					}
					else if( $sentenca == 1 )
					{
						// aluno podera fazer exame
						// remove excessao
						$objExcessao->excluir();
						$this->passo = 0;
						return true;
					}
					else if( $sentenca == 2 )
					{
						// aluno aprovado
						// aprova aluno
						$objMatricula = new clsPmieducarMatricula($detalhe_excessao["ref_cod_matricula"],null,null,null,$this->pessoa_logada,null,null,1);
						$objMatricula->edita();
						// remove excessao
						$objExcessao->excluir();
						$this->passo = 0;
						return true;
					}
				}
			}
		}
		else if( $this->passo == 4 )
		{
//			echo "<pre>"; print_r($this->resultado_final); die();
			// aprovou/reprovou alunos manualmente
			$objCurso = new clsPmieducarCurso($this->ref_cod_curso);
			foreach ($this->resultado_final as $key => $value)
			{
				if( $value )
				{
					// remove excessoes
					$objExcessao = new clsPmieducarMatriculaExcessao();
					$objExcessao->excluirPorMatricula($key);

					// aprova/reprova
					$objMatricula = new clsPmieducarMatricula($key,null,null,null,$this->pessoa_logada,null,null,$value);
					$objMatricula->edita();
				}
			}
			$this->passo = 0;
			return true;
		}
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