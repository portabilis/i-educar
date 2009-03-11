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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Iniciar/Finalizar Ano Letivo" );
		$this->processoAp = "561";
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

	var $ref_cod_escola;
	var $tipo_acao;
	var $ano;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		/**
		 * verifica permissao para realizar operacao
		 */
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 561, $this->pessoa_logada, 3,  "educar_escola_lst.php" );

		/**
		 * Somente inicia ano por POST
		 */
		if(!$_POST)
		{
			header("location: educar_escola_lst.php");
			die;
		}

		foreach ($_POST as $key => $value)
			$this->$key = $value;

		/**
		 *  Os 3 campos devem estar preenchidos para poder realizar acao
		 */
		if(!$this->ref_cod_escola || !$this->tipo_acao || !$this->ano)
		{
			header("location: educar_escola_lst.php");
			die;
		}

		/**
		 * verifica se existe ano letivo
		 */

		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->cod_escola,$this->ano,null,null,null,null,null,null);
		$det_ano = $obj_ano_letivo->detalhe();

		if(!$obj_ano_letivo->detalhe())
		{
			header("location: educar_escola_lst.php");
			die;
		}


		/**
		 * verifica se ano letivo da escola nao possui nenhuma matricula
		 */

		if($this->tipo_acao == "iniciar" && $det_ano['andamento'] == 0)
			$this->iniciarAnoLetivo();
		elseif($this->tipo_acao == "finalizar"  && $det_ano['andamento'] == 1)
			$this->finalizarAnoLetivo();
		else
		{
			header("location: educar_escola_det.php?cod_escola={$this->ref_cod_escola}'");
			die;
		}

		/**
		 * exibe mensagem e redireciona para detalhe da escola
		 */

		echo "<script>
				alert('Ação realizada com sucesso');
				window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
			  </script>";

		header("location: educar_escola_lst.php");
		die;
	}

	function iniciarAnoLetivo()
	{



	}

	function finalizarAnoLetivo()
	{
		/**
		 * VERIFICA se não existem matriculas em andamento
		 */
		$obj_matriculas = new clsPmieducarMatricula();
		$existe_matricula_andamento = $obj_matriculas->lista(null,null,$this->ref_cod_escola,null,null,null,null,3,null,null,null,null,1,$this->ano,null,null,1);
		if($existe_matricula_andamento)
		{
			echo "<script>
					alert('Não é possível finalizar o ano letivo existem matrículas em andamento!');
					window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
				  </script>";
		}

		/**
		 *  busca matriculas para sakvar historico escolar
		 * 	var $cod_matricula;
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
			var $ultima_matricula;
			var $etapa;
		 */
		$matriculas = $obj_matriculas->lista(null,null,$this->ref_cod_escola,null,null,null,null,1,null,null,null,null,1,$this->ano,null,null,1);

		if($matriculas)
		{
			foreach ($matriculas as $matricula) {

				$obj_historico_escolar = new clsPmieducarHistoricoEscolar();
				if(!$obj_historico_escolar->lista($matricula['ref_cod_aluno'],null,null,null,$matricula['ref_ref_cod_serie'],$matricula['ano'],null,null,null,null,null,null,$matricula['aprovado'],null,null,null,null,1))
				{
					/**
					 * busca carga horaria
					 */

					$obj_matricula_turma = new clsPmieducarMatriculaTurma();
					$det_matricula_turma = array_shift($obj_matricula_turma->lista($matricula['cod_matricula'],null,null,null,null,null,null,null,1,$matricula['ref_ref_cod_serie'],null,$matricula['ref_ref_cod_escola'],null));

					$obj_turma = new clsPmieducarTurma($det_matricula_turma['ref_cod_matricula_turma'],null,null,$matricula['ref_ref_cod_serie'],$matricula['ref_ref_cod_serie'],null,null,null,null,null,null,null,1,null,null,null,null,null);
					$det_turma = $obj_turma->detalhe();

					$obj_serie = new clsPmieducarSerie($det_turma['ref_ref_cod_serie']);
					$det_serie = $obj_serie->detalhe();


					/**
					 * Busca dias letivos
					 */

					$obj_calendario = new clsPmieducarCalendarioAnoLetivo();
					$obj_calendario->_campos_lista .= ",termino_ano_letivo -inicio_ano_letivo as dias_letivos";
					$det_calendario = array_shift($obj_calendario->lista(null,$matricula['ref_ref_cod_escola'],null,null,$matricula['ano'],null,null,null,null,1,null,null,null,null,null));
					$dias_letivos = $det_calendario['dias_letivos'];

					/**
					 * Busca dias nao letivos
					 */

					$obj_dias_nao_letivos = new clsPmieducarCalendarioDia();
					$dias_nao_letivos = count($obj_dias_nao_letivos->lista($det_calendario['cod_calendario_ano_letivo'],null,null,null,null,null,null,null,null,null,null,null,1,'n'));

					$dias_letivos = $dias_letivos - $dias_nao_letivos;

					/**
					 * busca nome da escola
					 */

					$obj_escola = new clsPmieducarEscola($matricula['ref_ref_cod_escola']);
					$det_escola = $obj_escola->detalhe();

					if ($det_escola["ref_idpes"])
					{
						$obj_escola1 = new clsPessoaJuridica($det_escola["ref_idpes"]);
						$obj_escola_det1 = $obj_escola1->detalhe();
						$nm_escola = $obj_escola_det1["fantasia"];

						$obj_endereco = new clsPessoaEndereco($det_escola["ref_idpes"]);

						if ( class_exists( "clsEnderecoExterno" ) )
						{
							$obj_endereco = new clsEnderecoExterno();
							$endereco_lst = $obj_endereco->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$det_escola["ref_idpes"]);
							foreach ($endereco_lst as $endereco)
							{
								$sigla_uf = $endereco["sigla_uf"]->detalhe();
								$sigla_uf = $sigla_uf["nome"];
								$cidade = $endereco["cidade"];
							}
						}
					}
					else
					{
						if ( class_exists( "clsPmieducarEscolaComplemento" ) )
						{
							$obj_escola = new clsPmieducarEscolaComplemento($this->cod_escola);
							$obj_escola_det = $obj_escola->detalhe();
							$nm_escola = $obj_escola_det["nm_escola"];
							$cidade = $obj_escola_det["municipio"];
						}
					}

				}

				$obj_historico_escolar = new clsPmieducarHistoricoEscolar($matricula['ref_cod_aluno'],null,null,null,$matricula['ref_ref_cod_serie'],$matricula['ano'],$det_serie['carga_horaria'],$dias_letivos,$det_escola['nome'],$nm_escola,$sigla_uf,'',$matricula['aprovado'],null,null,1);
				$cadastrou = $obj_historico_escolar->cadastra();
				if(!$cadastrou)
				{
					echo "<script>
						alert('Ocorreu um erro ao cadastrar histórico escolar,\ncontate o administrador!');
						window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
					  </script>";
				}

				/**
				 *  cadastra historico disciplinas
				 */
				$obj_disciplinas = new clsPmieducarTurmaDisciplina();
				$lista_disciplinas = $obj_disciplinas->lista(null,null,$matricula['ref_ref_cod_escola'],$matricula['ref_ref_cod_serie']);
				if($lista_disciplinas)
				{
					foreach ($lista_disciplinas as $disciplina) {

						$obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
						$det_disciplina = $obj_disciplina->detalhe();

						$obj_nota_aluno = new clsPmieducarNotaAluno();
						$det_aluno = $obj_nota_aluno->lista(null,null,null,$matricula['ref_ref_cod_serie'],$matricula['ref_ref_cod_serie'],$disciplina['ref_cod_disciplina'],$det_turma['cod_turma'],$matricula['cod_matricula'],$det_turma['cod_turma'],null,null,null,null,null,null,1);
						$obj_historico_disciplina = new clsPmieducarHistoricoDisciplinas(null,$matricula['ref_cod_aluno'],$cadastrou,$det_disciplina['nm_disciplina'],)
					}

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