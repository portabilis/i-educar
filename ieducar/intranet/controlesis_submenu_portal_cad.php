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
require_once( "include/pmicontrolesis/clsPmicontrolesisSubmenuPortal.inc.php" );
require_once( "include/pmicontrolesis/clsPmicontrolesisMenuPortal.inc.php" );
class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Submenu Portal" );
		$this->processoAp = "613";
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

	var $cod_submenu_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_menu_portal;
	var $nm_submenu;
	var $arquivo;
	var $_target;
	var $title;
	var $ordem;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_submenu_portal=$_GET["cod_submenu_portal"];

		if( is_numeric( $this->cod_submenu_portal ) )
		{

			$obj = new clsPmicontrolesisSubmenuPortal( $this->cod_submenu_portal );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->_target = $this->target;
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->target = '_self';
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_submenu_portal_det.php?cod_submenu_portal={$registro["cod_submenu_portal"]}" : "controlesis_submenu_portal_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_submenu_portal", $this->cod_submenu_portal );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmicontrolesisMenuPortal" ) )
		{
			$objTemp = new clsPmicontrolesisMenuPortal();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_menu_portal']}"] = "{$registro['nm_menu']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmicontrolesisMenuPortal nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_menu_portal", "Menu Portal", $opcoes, $this->ref_cod_menu_portal );
		// text
		$this->campoTexto( "nm_submenu", "Nome Submenu", $this->nm_submenu, 30, 255, true );
		$this->campoTexto( "arquivo", "Arquivo(url)", $this->arquivo, 30, 255, true );
		if(!$this->_target)
		{
			$this->_target = 'S';
		}

		$this->campoRadio( "_target", "Target", array("S"=>"_self", "B"=>"_blank"), $this->_target, 30 );
		$this->campoTexto( "title", "Title", $this->title, 30, 255, false );
		$this->campoTexto( "ordem", "Ordem", $this->ordem, 5, 10, true );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisSubmenuPortal( $this->cod_submenu_portal, $this->pessoa_logada, null, $this->ref_cod_menu_portal, $this->nm_submenu, $this->arquivo, $this->_target, $this->title, $this->ordem,null, null,1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: controlesis_submenu_portal_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisSubmenuPortal\nvalores obrigatorios\nis_numeric( $this->ref_funcionario_cad ) && is_numeric( $this->ref_cod_menu_portal ) && is_string( $this->nm_submenu ) && is_string( $this->arquivo ) && is_string( $this->_target ) && is_numeric( $this->ordem ) && is_string( $this->data_cadastro ) && is_numeric( $this->ativo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisSubmenuPortal($this->cod_submenu_portal, null, $this->pessoa_logada, $this->ref_cod_menu_portal, $this->nm_submenu, $this->arquivo, $this->_target, $this->title, $this->ordem, $this->data_cadastro, 'NOW()', 1);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_submenu_portal_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisSubmenuPortal\nvalores obrigatorios\nif( is_numeric( $this->cod_submenu_portal ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisSubmenuPortal($this->cod_submenu_portal,null, $this->pessoa_logada, $this->ref_cod_menu_portal, $this->nm_submenu, $this->arquivo, $this->_target, $this->title, $this->ordem, null, 'NOW()', 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_submenu_portal_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisSubmenuPortal\nvalores obrigatorios\nif( is_numeric( $this->cod_submenu_portal ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
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