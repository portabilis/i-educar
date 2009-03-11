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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Disciplina" );
		$this->processoAp = "557";
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

	var $cod_disciplina;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $desc_disciplina;
	var $desc_resumida;
	var $abreviatura;
	var $carga_horaria;
	var $apura_falta;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_disciplina;
	//var $cod_disciplina_topico;
	var $ref_cod_curso;

	var $ref_cod_instituicao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_disciplina=$_GET["cod_disciplina"];

		if( is_numeric( $this->cod_disciplina ) )
		{

			$obj = new clsPmieducarDisciplina( $this->cod_disciplina );
			$registro  = $obj->detalhe();

			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if ($this->ref_cod_curso)
				{
					$obj_instituicao = new clsPmieducarCurso($this->ref_cod_curso);
					$obj_instituicao_det = $obj_instituicao->detalhe();
					$this->ref_cod_instituicao = $obj_instituicao_det["ref_cod_instituicao"];
				}

				$obj_permissoes = new clsPermissoes();
				$this->fexcluir = $obj_permissoes->permissao_excluir( 557, $this->pessoa_logada,3 );
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_disciplina_det.php?cod_disciplina={$registro["cod_disciplina"]}" : "educar_disciplina_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(557,$this->pessoa_logada,3,"educar_disciplina_lst.php");

		// primary keys
		$this->campoOculto( "cod_disciplina", $this->cod_disciplina );

		// Foreign Keys
		$obrigatorio = true;
		$get_curso = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_disciplina", "Nome Disciplina", $this->nm_disciplina, 30, 255, true );
		$this->campoMemo( "desc_disciplina", "Descri&ccedil;&atilde;o Disciplina", $this->desc_disciplina, 30, 4, false );
		$this->campoMemo( "desc_resumida", "Descri&ccedil;&atilde;o Resumida", $this->desc_resumida, 30, 4, false );
		$this->campoTexto( "abreviatura", "Abreviatura", $this->abreviatura, 15, 15, true );
		$this->campoNumero( "carga_horaria", "Carga Hor&aacute;ria", $this->carga_horaria, 3, 3, true );

		// list
//		$opcoes = array( "" => "Selecione", 1 => "n&atilde;o", 2 => "sim");
//		$this->campoLista( "apura_falta", "Apura Falta", $opcoes, $this->apura_falta);
		$this->campoCheck( "apura_falta", "Apura Falta", $this->apura_falta);
		/*
		$obj_topicos = new clsPmieducarDisciplinaTopico(null, null, null, null, null, null, null, 1);
		$obj_topicos->setOrderby('nm_topico ASC');
		$lista_topico = $obj_topicos->lista();

		if ( is_array( $lista_topico ) && count( $lista_topico ) )
		{
			foreach ( $lista_topico as $topico )
			{
				$topicos["{$topico['cod_disciplina_topico']}"] = "{$topico['nm_topico']}";
			}
		}
		$this->campoLista( "cod_disciplina_topico", "T&oacute;picos", $topicos, $this->cod_disciplina_topico, null,null,null,null,null,false );
		*/
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if ($this->apura_falta == 'on')
			$this->apura_falta = 1;
		else
			$this->apura_falta = 0;

		$obj = new clsPmieducarDisciplina( null, null, $this->pessoa_logada, $this->desc_disciplina, $this->desc_resumida, $this->abreviatura, $this->carga_horaria, $this->apura_falta, null, null, 1, $this->nm_disciplina, $this->ref_cod_curso );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_disciplina_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarDisciplina\nvalores obrigat&oacute;rios\nif( is_numeric( $this->pessoa_logada ) && is_string( $this->desc_disciplina ) && is_string( $this->desc_resumida ) && is_string( $this->abreviatura ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->apura_falta ) && is_string( $this->nm_disciplina ) && is_numeric( $this->ref_cod_curso ) )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if ($this->apura_falta == 'on')
			$this->apura_falta = 1;
		else
			$this->apura_falta = 0;

		$obj = new clsPmieducarDisciplina( $this->cod_disciplina, $this->pessoa_logada, null, $this->desc_disciplina, $this->desc_resumida, $this->abreviatura, $this->carga_horaria, $this->apura_falta, null, null, 1, $this->nm_disciplina, $this->ref_cod_curso );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_disciplina_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarDisciplina\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_disciplina ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarDisciplina($this->cod_disciplina, $this->pessoa_logada, null, null, null, null, null, null, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_disciplina_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarDisciplina\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_disciplina ) && is_numeric( $this->pessoa_logada ) )\n-->";
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