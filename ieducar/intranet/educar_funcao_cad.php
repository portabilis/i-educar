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
		$this->SetTitulo( "{$this->_instituicao} i-Educar -  Fun&ccedil;&atilde;o Servidor" );
		$this->processoAp = "634";
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

	var $cod_funcao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_funcao;
	var $abreviatura;
	var $professor;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_funcao=$_GET["cod_funcao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );

		if( is_numeric( $this->cod_funcao ) )
		{
			$obj = new clsPmieducarFuncao( $this->cod_funcao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if( $obj_permissoes->permissao_excluir( 634, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}

			if($this->professor == '0')
				$this->professor =  "N";
			elseif($this->professor == '1')
				$this->professor = "S";

		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_funcao_det.php?cod_funcao={$registro["cod_funcao"]}" : "educar_funcao_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_funcao", $this->cod_funcao );

		$obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_funcao", "Func&atilde;o", $this->nm_funcao, 30, 255, true );
		$this->campoTexto( "abreviatura", "Abreviatura", $this->abreviatura, 30, 255, true );
		$opcoes = array('' => 'Selecione',
						'S' => 'Sim',
						'N' => 'N&atilde;o'
						);

		$this->campoLista( "professor", "Professor",$opcoes, $this->professor,"",false,"","",false,true);
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );

		if($this->professor == 'N')
			$this->professor =  "0";
		elseif($this->professor == 'S')
			$this->professor = "1";

		$obj = new clsPmieducarFuncao( null, null, $this->pessoa_logada, $this->nm_funcao, $this->abreviatura, $this->professor, null, null, 1, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_funcao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarFuncao\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_funcao ) && is_string( $this->abreviatura ) && is_numeric( $this->professor )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($this->professor == 'N')
			$this->professor =  "0";
		elseif($this->professor == 'S')
			$this->professor = "1";

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );


		$obj = new clsPmieducarFuncao($this->cod_funcao, $this->pessoa_logada, null, $this->nm_funcao, $this->abreviatura, $this->professor, null, null, 1, $this->ref_cod_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_funcao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarFuncao\nvalores obrigatorios\nif( is_numeric( $this->cod_funcao ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );


		$obj = new clsPmieducarFuncao( $this->cod_funcao, $this->pessoa_logada, null,null,null,null,null,null,0,$this->ref_cod_instituicao );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_funcao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarFuncao\nvalores obrigatorios\nif( is_numeric( $this->cod_funcao ) && is_numeric( $this->pessoa_logada ) )\n-->";
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