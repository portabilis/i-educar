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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Falta Aluno" );
		$this->processoAp = "0";
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
	
	var $cod_falta_aluno;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $disc_ref_ref_cod_serie;
	var $disc_ref_ref_cod_escola;
	var $disc_ref_ref_cod_disciplina;
	var $disc_ref_ref_cod_turma;
	var $ref_ref_cod_turma;
	var $ref_ref_cod_matricula;
	var $data_falta;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
		 
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->cod_falta_aluno=$_GET["cod_falta_aluno"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_falta_aluno_lst.php" );

		if( is_numeric( $this->cod_falta_aluno ) )
		{
			
			$obj = new clsPmieducarFaltaAluno( $this->cod_falta_aluno );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 0, $this->pessoa_logada, 0 ) )
			{
				$this->fexcluir = true;
			}

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_falta_aluno_det.php?cod_falta_aluno={$registro["cod_falta_aluno"]}" : "educar_falta_aluno_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_falta_aluno", $this->cod_falta_aluno );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarMatriculaTurma" ) )
		{
			$objTemp = new clsPmieducarMatriculaTurma();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['ref_cod_matricula']}"] = "{$registro['data_cadastro']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMatriculaTurma nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_ref_cod_matricula", "Matricula", $opcoes, $this->ref_ref_cod_matricula );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarMatriculaTurma" ) )
		{
			$objTemp = new clsPmieducarMatriculaTurma();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['ref_cod_matricula']}"] = "{$registro['data_cadastro']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMatriculaTurma nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_ref_cod_turma", "Turma", $opcoes, $this->ref_ref_cod_turma );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTurmaDisciplina" ) )
		{
			$objTemp = new clsPmieducarTurmaDisciplina();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurmaDisciplina nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "disc_ref_ref_cod_turma", "Disc Cod Turma", $opcoes, $this->disc_ref_ref_cod_turma );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTurmaDisciplina" ) )
		{
			$objTemp = new clsPmieducarTurmaDisciplina();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurmaDisciplina nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "disc_ref_ref_cod_disciplina", "Disc Cod Disciplina", $opcoes, $this->disc_ref_ref_cod_disciplina );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTurmaDisciplina" ) )
		{
			$objTemp = new clsPmieducarTurmaDisciplina();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurmaDisciplina nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "disc_ref_ref_cod_escola", "Disc Cod Escola", $opcoes, $this->disc_ref_ref_cod_escola );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTurmaDisciplina" ) )
		{
			$objTemp = new clsPmieducarTurmaDisciplina();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurmaDisciplina nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "disc_ref_ref_cod_serie", "Disc Cod Serie", $opcoes, $this->disc_ref_ref_cod_serie );


		// text

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_falta_aluno_lst.php" );

		
		$obj = new clsPmieducarFaltaAluno( $this->cod_falta_aluno, $this->pessoa_logada, $this->pessoa_logada, $this->disc_ref_ref_cod_serie, $this->disc_ref_ref_cod_escola, $this->disc_ref_ref_cod_disciplina, $this->disc_ref_ref_cod_turma, $this->ref_ref_cod_turma, $this->ref_ref_cod_matricula, $this->data_falta, $this->data_cadastro, $this->data_exclusao, $this->ativo );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_falta_aluno_lst.php" );
			die();
			return true;
		}
		
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarFaltaAluno\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_numeric( $this->disc_ref_ref_cod_serie ) && is_numeric( $this->disc_ref_ref_cod_escola ) && is_numeric( $this->disc_ref_ref_cod_disciplina ) && is_numeric( $this->disc_ref_ref_cod_turma ) && is_numeric( $this->ref_ref_cod_turma ) && is_numeric( $this->ref_ref_cod_matricula ) && ( $this->data_falta )\n-->";
		return false;
	}

	function Editar() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_falta_aluno_lst.php" );

		
		$obj = new clsPmieducarFaltaAluno($this->cod_falta_aluno, $this->pessoa_logada, $this->pessoa_logada, $this->disc_ref_ref_cod_serie, $this->disc_ref_ref_cod_escola, $this->disc_ref_ref_cod_disciplina, $this->disc_ref_ref_cod_turma, $this->ref_ref_cod_turma, $this->ref_ref_cod_matricula, $this->data_falta, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_falta_aluno_lst.php" );
			die();
			return true;
		}
		
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarFaltaAluno\nvalores obrigatorios\nif( is_numeric( $this->cod_falta_aluno ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 0, $this->pessoa_logada, 0,  "educar_falta_aluno_lst.php" );

		
		$obj = new clsPmieducarFaltaAluno($this->cod_falta_aluno, $this->pessoa_logada, $this->pessoa_logada, $this->disc_ref_ref_cod_serie, $this->disc_ref_ref_cod_escola, $this->disc_ref_ref_cod_disciplina, $this->disc_ref_ref_cod_turma, $this->ref_ref_cod_turma, $this->ref_ref_cod_matricula, $this->data_falta, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_falta_aluno_lst.php" );
			die();
			return true;
		}
		
		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarFaltaAluno\nvalores obrigatorios\nif( is_numeric( $this->cod_falta_aluno ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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