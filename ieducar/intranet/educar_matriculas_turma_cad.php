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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matriculas Turma" );
		$this->processoAp = "659";
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

	var $ref_cod_matricula;

	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $sequencial;

	var $ref_cod_instituicao;
	var $ref_ref_cod_escola;
	var $ref_cod_curso;
	var $ref_ref_cod_serie;
	var $ref_cod_turma;

	//------INCLUI ALUNO------//
	var $matriculas_turma;
	var $incluir_matricula;

	function Inicializar()
	{
//		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_turma=$_GET["ref_cod_turma"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 659, $this->pessoa_logada, 7,  "educar_matriculas_turma_lst.php" );

		if( is_numeric( $this->ref_cod_turma ) )
		{
			//$obj_matriculas_turma = new clsPmieducarMatriculaTurma();
			//$lst_matriculas_turma = $obj_matriculas_turma->lista( null,$this->ref_cod_turma,null,null,null,null,null,null,1 );

			/*if ( is_array($lst_matriculas_turma) )
			{
				$registro = array_shift($lst_matriculas_turma);
			}
			else
			{*/
				$obj_turma = new clsPmieducarTurma();
				$lst_turma = $obj_turma->lista( $this->ref_cod_turma );

				if (is_array($lst_turma))
				{
					$registro = array_shift($lst_turma);
				}
			//}
			//echo '<pre>';print_r($registro);die;
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$retorno = "Editar";
			}
			$this->url_cancelar = ($retorno == "Editar") ? "educar_matriculas_turma_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}" : "educar_matriculas_turma_lst.php";
			$this->nome_url_cancelar = "Cancelar";
			return $retorno;
		}

		header("location: educar_matriculas_turma_lst.php");
		die;
	}

	function Gerar()
	{
		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;

		$this->campoOculto( "ref_cod_turma", $this->ref_cod_turma );
		$this->campoOculto( "ref_ref_cod_escola", $this->ref_ref_cod_escola );
		$this->campoOculto( "ref_ref_cod_serie", $this->ref_ref_cod_serie );
		$this->campoOculto( "ref_cod_curso", $this->ref_cod_curso );

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			$obj_cod_instituicao = new clsPmieducarInstituicao( $this->ref_cod_instituicao );
			$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
			$nm_instituicao = $obj_cod_instituicao_det["nm_instituicao"];

			$this->campoRotulo( "nm_instituicao", "Institui&ccedil;&atilde;o", $nm_instituicao );
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if ($this->ref_ref_cod_escola)
			{
				$obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_ref_cod_escola );
				$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
				$nm_escola = $det_ref_cod_escola["nome"];

				$this->campoRotulo( "nm_escola", "Escola", $nm_escola );
			}
		}
		if( $this->ref_cod_curso )
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $this->ref_cod_curso );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$nm_curso = $det_ref_cod_curso["nm_curso"];

			$this->campoRotulo( "nm_curso", "Curso", $nm_curso );
		}
		if( $this->ref_ref_cod_serie )
		{
			$obj_ref_cod_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
			$det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
			$nm_serie = $det_ref_cod_serie["nm_serie"];

			$this->campoRotulo( "nm_serie", "S&eacute;rie", $nm_serie );

			// busca o ano em q a escola esta em andamento
			$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
			$lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_ref_cod_escola,null,null,null,1,null,null,null,null,1 );
			if ( is_array($lst_ano_letivo) )
			{
				$det_ano_letivo = array_shift($lst_ano_letivo);
				$ano_letivo = $det_ano_letivo["ano"];
			}
			else
			{
				$this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar o Ano Letivo.";
				return false;
			}

		}
		if( $this->ref_cod_turma )
		{
			$obj_turma = new clsPmieducarTurma( $this->ref_cod_turma );
			$det_turma = $obj_turma->detalhe();
			$nm_turma = $det_turma["nm_turma"];

			$this->campoRotulo( "nm_turma", "Turma", $nm_turma );
		}

		//---------------------INCLUI ALUNO---------------------//
		$this->campoQuebra();

		if ( $_POST["matriculas_turma"] )
			$this->matriculas_turma = unserialize( urldecode( $_POST["matriculas_turma"] ) );

		if( is_numeric($this->ref_cod_turma) && !$_POST)
		{
			$obj_matriculas_turma = new clsPmieducarMatriculaTurma();
			$obj_matriculas_turma->setOrderby("nome_aluno");
			$lst_matriculas_turma = $obj_matriculas_turma->lista( null,$this->ref_cod_turma,null,null,null,null,null,null,1,null,null,null,null,null,null,array( 1, 2, 3 ),null,null,$ano_letivo,null,true,null,1,true );
			
			if( is_array($lst_matriculas_turma) )
			{
				foreach ( $lst_matriculas_turma AS $key => $campo )
				{
					$this->matriculas_turma[$campo["ref_cod_matricula"]]["sequencial_"] = $campo["sequencial"];
				}
			}
		}
		if ( $_POST["ref_cod_matricula"] )
		{
			$obj_matriculas_turma = new clsPmieducarMatriculaTurma( $_POST["ref_cod_matricula"], $this->ref_cod_turma );
			$sequencial = $obj_matriculas_turma->buscaSequencialMax();

			$this->matriculas_turma[$_POST["ref_cod_matricula"]]["sequencial_"] = $sequencial;
			unset( $this->ref_cod_matricula );
		}

		if ( $this->matriculas_turma )
		{
			foreach ( $this->matriculas_turma as $matricula => $campo )
			{
				$obj_matricula = new clsPmieducarMatricula( $matricula );
				$det_matricula = $obj_matricula->detalhe();

				$obj_aluno = new clsPmieducarAluno();
//				$obj_aluno->setOrderby("nome_aluno ASC");
				$lst_aluno = $obj_aluno->lista( $det_matricula["ref_cod_aluno"] );
				$det_aluno = array_shift($lst_aluno);
				$nm_aluno = $det_aluno["nome_aluno"];

				$this->campoTextoInv( "ref_cod_matricula_{$matricula}", "", $nm_aluno, 30, 255, false, false, false,"","","","","ref_cod_matricula" );
			}
		}
		$this->campoOculto( "matriculas_turma", serialize( $this->matriculas_turma ) );

	//-------------------ALUNO----------------------//
		// foreign keys
//		$opcoes = array( "" => "Selecione" );
		$opcoes = array();
		if( class_exists( "clsPmieducarMatriculaTurma" ) )
		{
			$obj_matriculas_turma = new clsPmieducarMatriculaTurma();
			$alunos = $obj_matriculas_turma->alunosNaoEnturmados( $this->ref_ref_cod_escola, $this->ref_ref_cod_serie, $this->ref_cod_curso, $ano_letivo );
			if ( is_array($alunos) )
			{
				for ($i = 0; $i < count($alunos); $i++)
				{
					$obj_matricula = new clsPmieducarMatricula( $alunos[$i] );
					$det_matricula = $obj_matricula->detalhe();

					$obj_aluno = new clsPmieducarAluno();
//					$obj_aluno->setOrderby("nome_aluno ASC");
					$lst_aluno = $obj_aluno->lista( $det_matricula["ref_cod_aluno"] );
					$det_aluno = array_shift($lst_aluno);

					$opcoes["{$alunos[$i]}"] = "{$det_aluno["nome_aluno"]}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMatriculaTurma nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		
		if(count($opcoes))
		{
			asort($opcoes);
			foreach ($opcoes as $key => $aluno)
			{
				$this->campoCheck("ref_cod_matricula[$key]","Aluno",$key,$aluno,null,null,null);
			}
		}
		else
			$this->campoRotulo("rotulo_1","-","Todos os alunos já se encontram enturmados");
		//$this->campoLista( "ref_cod_matricula", "Aluno", $opcoes, $this->ref_cod_matricula,null,null,null,"<a href='#' onclick=\"getElementById('incluir_matricula').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",null,false );
		//$this->campoOculto( "incluir_matricula", "" );

		$this->campoQuebra();
	//---------------------FIM INCLUI ALUNO---------------------//

	}

	function Novo()
	{

	}

	function Editar()
	{

		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//$this->matriculas_turma = unserialize( urldecode( $this->matriculas_turma ) );
		//echo '<pre>';print_r($this->ref_cod_matricula);die;
		if ($this->matriculas_turma)
		{
//			echo "<pre>";print_r($this->matriculas_turma);die;
			foreach ($this->ref_cod_matricula AS $matricula => $campo)
			{
				$obj = new clsPmieducarMatriculaTurma( $matricula,$this->ref_cod_turma,null,$this->pessoa_logada,null,null,1,null,$campo["sequencial_"] );
				$existe = $obj->existe();
				if (!$existe)
				{
					$cadastrou = $obj->cadastra();
					if( !$cadastrou )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao editar clsPmieducarMatriculaTurma\nvalores obrigatorios\nif( is_numeric( $matricula ) && is_numeric( $this->ref_cod_turma ) && is_numeric( $this->pessoa_logada ) && is_numeric( {$campo["sequencial_"]} ) )\n-->";
						return false;
					}
				}
			}
			$this->mensagem .= "Cadastro efetuada com sucesso.<br>";
			header( "Location: educar_matriculas_turma_lst.php" );
			die();
			return true;
		}

//		$this->mensagem .= "Cadastro efetuada com sucesso.<br>";
		header( "Location: educar_matriculas_turma_lst.php" );
		die();
		return true;
	}

	function Excluir()
	{

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