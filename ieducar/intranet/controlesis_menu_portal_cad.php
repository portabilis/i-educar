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
require_once( "include/pmicontrolesis/clsPmicontrolesisMenuPortal.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Menu Portal" );
		$this->processoAp = "612";
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

	var $cod_menu_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $nm_menu;
	var $title;
	var $caminho;
	var $imagem;
	var $cor;
	var $posicao;
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

		$this->cod_menu_portal=$_GET["cod_menu_portal"];

		if( is_numeric( $this->cod_menu_portal ) )
		{

			$obj = new clsPmicontrolesisMenuPortal( $this->cod_menu_portal );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir = true;
				$retorno = "Editar";
			}
			$this->imagem = $this->caminho;
		}
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_menu_portal_det.php?cod_menu_portal={$registro["cod_menu_portal"]}" : "controlesis_menu_portal_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_menu_portal", $this->cod_menu_portal );
		$this->campoOculto( "imagem", $this->imagem );

		// foreign keys

		if(!$this->posicao)
		{
			$this->posicao = 'E';
		}
		// text
		$this->campoTexto( "nm_menu", "Nome Menu", $this->nm_menu, 30, 255, true );
		$this->campoTexto( "title", "Title", $this->title, 30, 255, false );
		$this->campoArquivo( "caminho", "Imagem", $this->caminho , 30);
		$this->campoTexto( "cor", "Cor", $this->cor, 8, 7, false );
		$this->campoRadio( "posicao", "Posição", array("E"=>"Esquerda", "D"=>"Direita"), $this->posicao );
		$this->campoTexto("ordem", "Ordem", $this->ordem, 5, 10, true );
		
		$this->campoCheck("ativar", "Cadastrar como ativo", $this->ativo, "Ativar" );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$arquivo = isset($_FILES['caminho']) ? $_FILES['caminho'] : FALSE;
		$diretorio = "imagens/";
		if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $arquivo['name']))
		{
		 	$this->caminho = $arquivo['name'];
		 	if($_POST['ativar'] == "on")
		 		$ativo = 1;
		 	else 
		 		$ativo = 0;
			$obj = new clsPmicontrolesisMenuPortal( null, $this->pessoa_logada, null, $this->nm_menu, $this->title, $this->caminho, $this->cor, $this->posicao, $this->ordem, null, null, $ativo);
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: controlesis_menu_portal_lst.php" );
				die();
				return true;
			}
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisMenuPortal\nvalores obrigatorios\nis_numeric( $this->ref_funcionario_cad ) && is_string( $this->nm_menu ) && is_string( $this->posicao ) && is_numeric( $this->ordem ) && is_string( $this->data_cadastro ) && is_numeric( $this->ativo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($this->caminho['tmp_name'])
		{
			$diretorio = "imagens/";
			unlink("{$diretorio}{$this->imagem}");
			$arquivo = isset($_FILES['caminho']) ? $_FILES['caminho'] : FALSE;
			if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $arquivo['name']))
			{
			 	$this->caminho = $arquivo['name'];
			}
		}
		else
		{
			$this->caminho = null;
		}
		if($_POST['ativar'] == "on")
		 		$ativo = 1;
		 	else 
		 		$ativo = 0;
		$obj = new clsPmicontrolesisMenuPortal($this->cod_menu_portal, null, $this->pessoa_logada, $this->nm_menu, $this->title, $this->caminho, $this->cor, $this->posicao, $this->ordem, null, 'NOW()', $ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_menu_portal_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisMenuPortal\nvalores obrigatorios\nif( is_numeric( $this->cod_menu_portal ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($this->caminho['tmp_name'])
		{
			$diretorio = "imagens/";
			unlink("{$diretorio}{$this->imagem}");
			$arquivo = isset($_FILES['caminho']) ? $_FILES['caminho'] : FALSE;
			if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $arquivo['name']))
			{
			 	$this->caminho = $arquivo['name'];
			}
		}
		else
		{
			$this->caminho = null;
		}

		$obj = new clsPmicontrolesisMenuPortal($this->cod_menu_portal,null,$this->pessoa_logada, $this->nm_menu, $this->title, $this->caminho, $this->cor, $this->posicao, $this->ordem, null, 'NOW()');
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_menu_portal_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisMenuPortal\nvalores obrigatorios\nif( is_numeric( $this->cod_menu_portal ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
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