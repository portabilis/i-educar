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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/Geral.inc.php" );
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Escolaridade" );
		$this->processoAp = "632";
		$this->renderBanner = false;
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

	var $idesco;
	var $descricao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->idesco=$_GET["idesco"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 632, $this->pessoa_logada, 3,  "educar_escolaridade_lst.php" );

		if( is_numeric( $this->idesco ) )
		{

			$obj = new clsCadastroEscolaridade( $this->idesco );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if( $obj_permissoes->permissao_excluir( 632, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_escolaridade_det.php?idesco={$registro["idesco"]}" : "educar_escolaridade_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idesco", $this->idesco );

		// foreign keys

		// text
		$this->campoTexto( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 30, 255, true );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsCadastroEscolaridade( null, $this->descricao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			echo "<script>
						parent.document.getElementById('ref_idesco').options[parent.document.getElementById('ref_idesco').options.length] = new Option('$this->descricao', '$cadastrou', false, false);
						parent.document.getElementById('ref_idesco').value = '$cadastrou';
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
//			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
//			header( "Location: educar_escolaridade_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsCadastroEscolaridade\nvalores obrigatorios\nis_numeric( $this->idesco ) && is_string( $this->descricao )\n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsCadastroEscolaridade($this->idesco, $this->descricao);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_escolaridade_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsCadastroEscolaridade\nvalores obrigatorios\nif( is_numeric( $this->idesco ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsCadastroEscolaridade($this->idesco, $this->descricao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_escolaridade_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsCadastroEscolaridade\nvalores obrigatorios\nif( is_numeric( $this->idesco ) )\n-->";
		return false;*/
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