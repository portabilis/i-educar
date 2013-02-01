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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Topo Portal" );
		$this->processoAp = "694";
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

	var $cod_topo_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_menu_portal;
	var $caminho1;
	var $caminho2;
	var $caminho3;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_topo_portal=$_GET["cod_topo_portal"];

		if( is_numeric( $this->cod_topo_portal ) )
		{

			$obj = new clsPmicontrolesisTopoPortal( $this->cod_topo_portal );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );


				$this->fexcluir = true;

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_topo_portal_det.php?cod_topo_portal={$registro["cod_topo_portal"]}" : "controlesis_topo_portal_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_topo_portal", $this->cod_topo_portal );

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
		$this->campoLista( "ref_cod_menu_portal", "Menu Portal", $opcoes, $this->ref_cod_menu_portal, "", false, "", "", false, false );


		// text
		$this->campoArquivo( "caminho1", "Topo 1", $this->caminho1, 30);
		$this->campoArquivo( "caminho2", "Topo 2", $this->caminho2, 30);
		$this->campoArquivo( "caminho3", "Topo 3", $this->caminho3, 30);		
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$diretorio = "imagens/topos/";
		$arquivo1 = isset($_FILES['caminho1']) ? $_FILES['caminho1'] : FALSE;
		$arquivo2 = isset($_FILES['caminho2']) ? $_FILES['caminho2'] : FALSE;
		$arquivo3 = isset($_FILES['caminho3']) ? $_FILES['caminho3'] : FALSE;
		if ( (move_uploaded_file($arquivo1['tmp_name'], $diretorio . $arquivo1['name'])) && (move_uploaded_file($arquivo2['tmp_name'], $diretorio . $arquivo2['name']))&& (move_uploaded_file($arquivo3['tmp_name'], $diretorio . $arquivo3['name'])))
		{
		 	$this->caminho1 = $arquivo1['name'];
		 	$this->caminho2 = $arquivo2['name'];
		 	$this->caminho3 = $arquivo3['name'];
	 	 	$obj = new clsPmicontrolesisTopoPortal( null, $this->pessoa_logada, null, $this->ref_cod_menu_portal, $this->caminho1, $this->caminho2, $this->caminho3, 'NOW()', null, 1);
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: controlesis_topo_portal_lst.php" );
				die();
				return true;
			}
			
		}
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisTopoPortal\nvalores obrigatorios\nis_numeric( $this->ref_funcionario_cad ) && is_numeric( $this->ref_cod_menu_portal ) && is_string( $this->caminho1 ) && is_string( $this->caminho2 ) && is_string( $this->caminho3 )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$diretorio = "imagens/topos/";
		$arquivo1 = isset($_FILES['caminho1']) ? $_FILES['caminho1'] : FALSE;
		$arquivo2 = isset($_FILES['caminho2']) ? $_FILES['caminho2'] : FALSE;
		$arquivo3 = isset($_FILES['caminho3']) ? $_FILES['caminho3'] : FALSE;
		
	 	$this->caminho1 = $arquivo1['name'];
	 	$this->caminho2 = $arquivo2['name'];
	 	$this->caminho3 = $arquivo3['name'];
	 	
	 	if ( (move_uploaded_file($arquivo1['tmp_name'], $diretorio . $arquivo1['name'])) && (move_uploaded_file($arquivo2['tmp_name'], $diretorio . $arquivo2['name']))&& (move_uploaded_file($arquivo3['tmp_name'], $diretorio . $arquivo3['name'])))
		{	 	
	 	 	$obj = new clsPmicontrolesisTopoPortal($this->cod_topo_portal, null , $this->pessoa_logada, $this->ref_cod_menu_portal, $this->caminho1, $this->caminho2, $this->caminho3, null, 'NOW()', 1);
			$edita = $obj->edita();
			if( $edita )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: controlesis_topo_portal_lst.php" );
				die();
				return true;
			}			
		}
		else
		{			
	 	 	$obj = new clsPmicontrolesisTopoPortal($this->cod_topo_portal, null , $this->pessoa_logada, $this->ref_cod_menu_portal,null, null, null, null, 'NOW()', 1);
			$edita = $obj->edita();
			if( $edita )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: controlesis_topo_portal_lst.php" );
				die();
				return true;
			}
		}
			
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisTopoPortal\nvalores obrigatorios\nif( is_numeric( $this->cod_topo_portal ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsPmicontrolesisTopoPortal($this->cod_topo_portal, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_menu_portal, $this->caminho1, $this->caminho2, $this->caminho3, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_topo_portal_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisTopoPortal\nvalores obrigatorios\nif( is_numeric( $this->cod_topo_portal ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
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