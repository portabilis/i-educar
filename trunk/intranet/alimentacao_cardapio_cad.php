<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja?								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P?blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja?			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  ?  software livre, voc? pode redistribu?-lo e/ou	 *
	*	modific?-lo sob os termos da Licen?a P?blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers?o 2 da	 *
	*	Licen?a   como  (a  seu  crit?rio)  qualquer  vers?o  mais  nova.	 *
	*																		 *
	*	Este programa  ? distribu?do na expectativa de ser ?til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl?cita de COMERCIALI-	 *
	*	ZA??O  ou  de ADEQUA??O A QUALQUER PROP?SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen?a  P?blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc?  deve  ter  recebido uma c?pia da Licen?a P?blica Geral GNU	 *
	*	junto  com  este  programa. Se n?o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Cardápio " );
		$this->processoAp = "10000";
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

	var $idcar;
	var $descricao;
	var $nm_arquivo;
	var $ref_escola;
	var $ref_usuario_cad;
	var $dt_cadastro;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->idcar=$_GET["idcar"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10000, $this->pessoa_logada,3, "alimentacao_cardapio_lst.php" );

		if( is_numeric( $this->idcar ) )
		{
			$obj_cardapio = new clsAlimentacaoCardapio();
			$lst = $obj_cardapio->lista($this->idcar,null);
			
			if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;

					//** verificao de permissao para exclusao
					//$this->fexcluir = $obj_permissoes->permissao_excluir(572,$this->pessoa_logada,7);
					//**

					$retorno = "Editar";
				}else{
					header( "Location: alimentacao_cardapio_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_cardapio_det.php?idcar={$registro["idcar"]}" : "alimentacao_cardapio_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idcar", $this->idcar );

		// text
		$this->campoTexto( "descricao", "Descrição", $this->descricao, 30, 255, true );
		
		if($this->idcar > 0)
		{
			$this->campoRotulo("nm_arquivo","Arquivo","{$this->nm_arquivo}");
		}
		else
		{
			$this->campoArquivo("nm_arquivo", "Arquivo", $this->nm_arquivo, "50");
		}
		
		$opcoes = array();
		$obj_escola = new clsPmieducarEscola();
		$lista = $obj_escola->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["cod_escola"]] = $registro["nome"];
			}			
		}
		
		$this->campoLista( "ref_escola", "Escola", $opcoes, $this->ref_escola,"",false,"","","",true );
		
		//$this->campoMemo( "desc_funcao", "Descri&ccedil;&atilde;o Func&atilde;o", $this->desc_funcao, 60, 5, false );
		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$caminho  = "arquivos/cardapios/";

		if ( !empty($_FILES['nm_arquivo']['name']) )
		{
			$caminho .= date("Y-m-d")."-";
			list($usec, $sec) = explode(" ", microtime());
			$caminho .= substr(md5("{$usec}{$sec}"), 0, 8);
			while (file_exists("{$caminho}"))
			{
				$caminho = $caminho . "a";
			}
			$ext = end(explode(".",$_FILES['nm_arquivo']['name']));
			$caminho .= ".".$ext;
			move_uploaded_file($_FILES['nm_arquivo']['tmp_name'], "{$caminho}");
		}
		else
		{
			$this->mensagem = "Cadastro não realizado.<br>O arquivo é obrigatório.";
			echo "<!--\nErro ao cadastrar clsAlimentacaoEscolar\nArquivo obrigatório-->";
			return false;
		}

		$obj = new clsAlimentacaoCardapio();
		$obj->idcar = $this->idcar;
		$obj->dt_cadastro = "NOW()";
		$obj->ref_usuario_cad = $this->pessoa_logada;
		$obj->ref_escola = $this->ref_escola;
		$obj->descricao = $this->descricao;
		$obj->nm_arquivo = $_FILES['nm_arquivo']['name'];
		$obj->path_arquivo = $caminho;
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_cardapio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEscolar\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoCardapio();
		$obj->idcar = $this->idcar;
		$obj->ref_escola = $this->ref_escola;
		$obj->descricao = $this->descricao;
		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_cardapio_det.php?idcar={$this->idcar}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não editado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEscolar\n-->";
		return false;

		
	}

	function Excluir()
	{
		@session_start();
	    $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

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
