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
		$this->SetTitulo( "{$this->_instituicao} Serviços" );
		$this->processoAp = "616";
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

	var $cod_servicos;
	var $ref_cod_funcionario_cad;
	var $ref_cod_funcionario_exc;
	var $url;
	var $caminho;
	var $imagem;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $title;
	var $descricao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_servicos=$_GET["cod_servicos"];


		if( is_numeric( $this->cod_servicos ) )
		{

			$obj = new clsPmicontrolesisservicos( $this->cod_servicos );
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
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_servicos_det.php?cod_servicos={$registro["cod_servicos"]}" : "controlesis_servicos_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_servicos", $this->cod_servicos );
		$this->campoOculto( "imagem", $this->imagem );

		// text
		$this->campoTexto( "url", "Url", $this->url, 30, 255, true );
		$this->campoArquivo( "caminho", "Caminho", $this->caminho, 30);
		$this->campoTexto( "title", "Title", $this->title, 30, 255, false );
		$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 10, false );

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
			$obj = new clsPmicontrolesisservicos( $this->cod_servicos, $this->pessoa_logada, null, $this->url, $this->caminho, null, null, 1, $this->title, $this->descricao );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: controlesis_servicos_lst.php" );
				die();
				return true;
			}
		}
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisservicos\nvalores obrigatorios\nis_numeric( $this->ref_cod_funcionario_cad )\n-->";
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
		$obj = new clsPmicontrolesisservicos($this->cod_servicos, null, $this->pessoa_logada, $this->url, $this->caminho, null, null, 1, $this->title, $this->descricao);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_servicos_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisservicos\nvalores obrigatorios\nif( is_numeric( $this->cod_servicos ) )\n-->";
		return false;
	}

	function Excluir()
	{
		//echo "$this->imagem";die();
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
		$obj = new clsPmicontrolesisservicos($this->cod_servicos, null, $this->pessoa_logada, $this->url, $this->caminho, null, null, 0, $this->title, $this->descricao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_servicos_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisservicos\nvalores obrigatorios\nif( is_numeric( $this->cod_servicos ) )\n-->";
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