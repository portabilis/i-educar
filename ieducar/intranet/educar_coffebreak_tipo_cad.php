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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Coffebreak Tipo" );
		$this->processoAp = "564";
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
	
	var $cod_coffebreak_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $desc_tipo;
	var $custo_unitario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
		 
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();
		
		$obj_permissao->permissao_cadastra(564, $this->pessoa_logada,7,"educar_coffebreak_tipo_lst.php");
		//**
				
		$this->cod_coffebreak_tipo=$_GET["cod_coffebreak_tipo"];

		if( is_numeric( $this->cod_coffebreak_tipo ) )
		{
			
			$obj = new clsPmieducarCoffebreakTipo( $this->cod_coffebreak_tipo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				//** verificao de permissao para exclusao
				$this->fexcluir = $obj_permissao->permissao_excluir(564,$this->pessoa_logada,7);
				//**
				$retorno = "Editar";
			}
			else{
				header("location: educar_coffebreak_tipo_lst.php");
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_coffebreak_tipo_det.php?cod_coffebreak_tipo={$registro["cod_coffebreak_tipo"]}" : "educar_coffebreak_tipo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_coffebreak_tipo", $this->cod_coffebreak_tipo );

		// foreign keys

		// text
		$this->campoTexto( "nm_tipo", "Nome Coffee Break", $this->nm_tipo, 42, 255, true );
		$this->campoMonetario( "custo_unitario", "Custo Unit&aacute;rio", $this->custo_unitario, 15, 255, true );
		$this->campoMemo("desc_tipo", "Descri&ccedil;&atilde;o Coffee Break", $this->desc_tipo, 40, 10, false );		

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->custo_unitario = str_replace(".","",$this->custo_unitario);
		$this->custo_unitario = str_replace(",",".",$this->custo_unitario);
		
		$obj = new clsPmieducarCoffebreakTipo( $this->cod_coffebreak_tipo, null, $this->pessoa_logada, $this->nm_tipo, $this->desc_tipo, $this->custo_unitario, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_coffebreak_tipo_lst.php" );
			die();
			return true;
		}
		
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarCoffebreakTipo\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo ) && is_numeric( $this->custo_unitario )\n-->";
		return false;
	}

	function Editar() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->custo_unitario = str_replace(".","",$this->custo_unitario);
		$this->custo_unitario = str_replace(",",".",$this->custo_unitario);
				
		$obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->desc_tipo, $this->custo_unitario, null, null, 1);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_coffebreak_tipo_lst.php" );
			die();
			return true;
		}
		
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarCoffebreakTipo\nvalores obrigatorios\nif( is_numeric( $this->cod_coffebreak_tipo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->desc_tipo, $this->custo_unitario, null,null,0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_coffebreak_tipo_lst.php" );
			die();
			return true;
		}
		
		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarCoffebreakTipo\nvalores obrigatorios\nif( is_numeric( $this->cod_coffebreak_tipo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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