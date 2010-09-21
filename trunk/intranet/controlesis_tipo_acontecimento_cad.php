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
		$this->SetTitulo( "{$this->_instituicao} Tipo Acontecimento" );
		$this->processoAp = "604";
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

	var $cod_tipo_acontecimento;
	var $ref_cod_funcionario_cad;
	var $ref_cod_funcionario_exc;
	var $nm_tipo;
	var $caminho;
	var $imagem;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_tipo_acontecimento=$_GET["cod_tipo_acontecimento"];


		if( is_numeric( $this->cod_tipo_acontecimento ) )
		{

			$obj = new clsPmicontrolesisTipoAcontecimento( $this->cod_tipo_acontecimento );
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
			$this->imagem = $this->caminho;
		}
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_tipo_acontecimento_det.php?cod_tipo_acontecimento={$registro["cod_tipo_acontecimento"]}" : "controlesis_tipo_acontecimento_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_tipo_acontecimento", $this->cod_tipo_acontecimento );
		$this->campoOculto( "imagem", $this->imagem );

		// text
		$this->campoTexto( "nm_tipo", "Nome Tipo", $this->nm_tipo, 30, 255, true );
		$this->campoArquivo( "caminho", "Caminho", $this->caminho, 30 );

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

			$obj = new clsPmicontrolesisTipoAcontecimento( $this->cod_tipo_acontecimento, $this->pessoa_logada, null, $this->nm_tipo, $this->caminho, null, null, 1 );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: controlesis_tipo_acontecimento_lst.php" );
				die();
				return true;
			}
		}
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisTipoAcontecimento\nvalores obrigatorios\nis_numeric( $this->ref_cod_funcionario_cad )\n-->";
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

		$obj = new clsPmicontrolesisTipoAcontecimento($this->cod_tipo_acontecimento, null, $this->pessoa_logada, $this->nm_tipo, $this->caminho, null, null, 1);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_tipo_acontecimento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisTipoAcontecimento\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_acontecimento ) )\n-->";
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

		$obj = new clsPmicontrolesisTipoAcontecimento($this->cod_tipo_acontecimento, null, $this->pessoa_logada, $this->nm_tipo, $this->caminho, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_tipo_acontecimento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisTipoAcontecimento\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_acontecimento ) )\n-->";
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