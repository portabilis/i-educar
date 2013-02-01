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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - M&oacute;dulo" );
		$this->processoAp = "584";
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

	var $cod_modulo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $num_meses;
	var $num_semanas;
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

		$this->cod_modulo=$_GET["cod_modulo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 584, $this->pessoa_logada, 3,  "educar_modulo_lst.php" );

		if( is_numeric( $this->cod_modulo ) )
		{
			$obj = new clsPmieducarModulo( $this->cod_modulo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 584, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}" : "educar_modulo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_modulo", $this->cod_modulo );

		// Filtros de Foreign Keys
		$obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		// foreign keys
//		if ($nivel_usuario == 1)
//		{
//			$opcoes = array( "" => "Selecione" );
//			if( class_exists( "clsPmieducarInstituicao" ) )
//			{
//				$obj_instituicao = new clsPmieducarInstituicao();
//				$lista = $obj_instituicao->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,1);
//				if ( is_array( $lista ) && count( $lista ) )
//				{
//					foreach ( $lista as $registro )
//					{
//						$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
//					}
//				}
//			}
//			else
//			{
//				echo "<!--\nErro\nClasse clsPmieducarInstituicao n&atilde;o encontrada\n-->";
//				$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
//			}
//			$this->campoLista( "ref_cod_instituicao", "Instituic&atilde;o", $opcoes, $this->ref_cod_instituicao);
//		}
//		else if ($nivel_usuario == 2)
//		{
//			$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
//			$obj_usuario_det = $obj_usuario->detalhe();
//			$this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];
//			$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
//		}

		// text
		$this->campoTexto( "nm_tipo", "M&oacute;dulo", $this->nm_tipo, 30, 255, true );
		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
		$this->campoNumero( "num_meses", "N&uacute;mero Meses", $this->num_meses, 2, 2, true );
		$this->campoNumero( "num_semanas", "N&uacute;mero Semanas", $this->num_semanas, 2, 2, true );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 584, $this->pessoa_logada, 3,  "educar_modulo_lst.php" );


		$obj = new clsPmieducarModulo( null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, $this->num_meses, $this->num_semanas, null, null, 1, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_modulo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarModulo\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo ) && is_numeric( $this->num_meses ) && is_numeric( $this->num_semanas ) && is_numeric( $this->ref_cod_instituicao )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 584, $this->pessoa_logada, 3,  "educar_modulo_lst.php" );


		$obj = new clsPmieducarModulo($this->cod_modulo, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, $this->num_meses, $this->num_semanas, null, null, 1, $this->ref_cod_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_modulo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarModulo\nvalores obrigatorios\nif( is_numeric( $this->cod_modulo ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 584, $this->pessoa_logada, 3,  "educar_modulo_lst.php" );


		$obj = new clsPmieducarModulo($this->cod_modulo, $this->pessoa_logada, null,null,null,null,null,null,null, 0 );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_modulo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarModulo\nvalores obrigatorios\nif( is_numeric( $this->cod_modulo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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