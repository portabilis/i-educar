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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Fornecedor " );
		$this->processoAp = "10003";
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

	var $idpes;
	var $descricao;
	var $cnpj;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->idpes=$_GET["idpes"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10003, $this->pessoa_logada,3, "alimentacao_fornecedor_lst.php" );

		if( is_numeric( $this->idpes ) )
		{
			$obj_fornecedor = new clsAlimentacaoFornecedor();
			$lst = $obj_fornecedor->lista($this->idpes);
			
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
					header( "Location: alimentacao_fornecedor_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_fornecedor_det.php?idpes={$registro["idpes"]}" : "alimentacao_produto_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idpes", $this->idpes );
		
		if($this->idpes > 0)
		{
			$this->campoRotulo("cnpj","Fornecedor","{$this->cnpj}");
		}
		else
		{
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 0 );
			$parametros->setPessoa( 'J' );
			$parametros->setPessoaNovo( "S" );
			$parametros->setPessoaCPF("N");
			$parametros->setPessoaTela('window');
			$parametros->setCodSistema(13);
			$parametros->adicionaCampoTexto( "cnpj", "cnpj" );
			$this->campoCnpjPesq( "cnpj", "CNPJ", $this->cnpj, "pesquisa_pessoa_lst.php", $parametros->serializaCampos(), true );
		}

		// text
		$this->campoTexto( "descricao", "Descrição", $this->descricao, 30, 255, true );
		
				

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		

		$obj = new clsAlimentacaoFornecedor();
		
		$pCnpj = null;
		$pCnpj = $this->cnpj;
		$pCnpj = str_replace("-","",str_replace("/","",str_replace(".","",$pCnpj)));
		if(!is_numeric($pCnpj))
		{
			$pCnpj = null;
		}		
		if (!is_numeric($pCnpj))
		{
			$this->mensagem = "Cadastro não realizado.<br>CNJP inválido.";
			return false;
		}
		
		$lst = $obj->lista(null,$pCnpj);
		if (is_array($lst))
		{
			$registro = array_shift($lst);
			if( $registro )
			{
				$this->mensagem = "Cadastro não realizado.<br>Fornecedor já cadastrado.";
				return false;
			}
		}
		
		
		$obj_juridica = new clsJuridica(null,$pCnpj);
		$det = $obj_juridica->detalhe();
		if(!is_array($det))
		{
			$this->mensagem = "Cadastro não realizado.<br>CNPJ não está cadastrado como empresa.";
			return false;
		}		
		
		$obj->idpes = $det["idpes"];
		$obj->descricao = $this->descricao;
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_fornecedor_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoProduto\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoFornecedor();
		$obj->idpes = $this->idpes;
		$obj->descricao = $this->descricao;
		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_fornecedor_det.php?idpes={$this->idpes}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não editado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoProduto\n-->";
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
