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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Produto Fornecedor " );
		$this->processoAp = "10004";
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

	var $idpf;
	var $ref_produto;
	var $ref_fornecedor;
	var $ano;
	var $mes_inicio;
	var $mes_fim;
	var $agri_familiar;
	var $pesoouvolume_un;
	var $preco_un;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->idpf=$_GET["idpf"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10004, $this->pessoa_logada,3, "alimentacao_produto_fornecedor_lst.php" );

		if( is_numeric( $this->idpf ) )
		{
			$obj_produto_fornecedor = new clsAlimentacaoProdutoFornecedor();
			$lst = $obj_produto_fornecedor->lista($this->idpf);
			
			if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;

					//** verificao de permissao para exclusao
					$this->fexcluir = $obj_permissoes->permissao_excluir(10004,$this->pessoa_logada,3);
					//**

					$retorno = "Editar";
				}else{
					header( "Location: alimentacao_produto_fornecedor_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_produto_fornecedor_det.php?idpf={$registro["idpf"]}" : "alimentacao_produto_fornecedor_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idpf", $this->idpf );
		
		$opcoes = array();
		$obj_produto = new clsAlimentacaoProduto();
		$lista = $obj_produto->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["idpro"]] = $registro["nm_produto"];
			}
			
		}				
		$this->campoLista( "ref_produto", "Produto", $opcoes, $this->ref_produto,"",false,"","","",true );
		
		$opcoes = array();
		$obj_fornecedor = new clsAlimentacaoFornecedor();
		$lista = $obj_fornecedor->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["idpes"]] = $registro["fantasia"];
			}
			
		}				
		$this->campoLista( "ref_fornecedor", "Fornecedor", $opcoes, $this->ref_fornecedor,"",false,"","","",true );
		
		$opcoes = array();
		for ($i = 2008; $i <= date("Y");$i++)
		{
			$opcoes[$i] = $i;
		}
		$this->campoLista( "ano", "Ano", $opcoes, $this->ano,"",false,"","","",true );
		
		$obj_produto_fornecedor = new clsAlimentacaoProdutoFornecedor();
		
		$this->campoLista( "mes_inicio", "Mês - Início", $obj_produto_fornecedor->getArrayMes(), $this->mes_inicio,"",false,"","","",true );
		
		$this->campoLista( "mes_fim", "Mês - Fim", $obj_produto_fornecedor->getArrayMes(), $this->mes_fim,"",false,"","","",true );
		
		$this->campoMonetario( "pesoouvolume_un", "Peso(Kg) ou Volume(L) por Un.", number_format($this->pesoouvolume_un,2,",",""), 30, 255, true );
		
		$this->campoMonetario( "preco_un", "Preço Un.",number_format($this->preco_un,2,",",""),10,16,true);
		
		$this->campoLista( "agri_familiar", "Agricultura Familiar", array("Não","Sim"), $this->agri_familiar,"",false,"","","",true );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		

		$obj = new clsAlimentacaoProdutoFornecedor();
		$obj->ref_produto = $this->ref_produto;
		$obj->ref_fornecedor = $this->ref_fornecedor;
		$obj->ano = $this->ano;
		$obj->mes_inicio = $this->mes_inicio;
		$obj->mes_fim = $this->mes_fim;
		$obj->pesoouvolume_un = str_replace(",",".",$this->pesoouvolume_un);
		$obj->preco_un = str_replace(",",".",$this->preco_un);
		$obj->agri_familiar = $this->agri_familiar;
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_produto_fornecedor_lst.php" );
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
		
		$obj = new clsAlimentacaoProdutoFornecedor();
		$obj->idpf = $this->idpf;
		$obj->ref_produto = $this->ref_produto;
		$obj->ref_fornecedor = $this->ref_fornecedor;
		$obj->ano = $this->ano;
		$obj->mes_inicio = $this->mes_inicio;
		$obj->mes_fim = $this->mes_fim;
		$obj->pesoouvolume_un = str_replace(",",".",$this->pesoouvolume_un);
		$obj->preco_un = str_replace(",",".",$this->preco_un);
		$obj->agri_familiar = $this->agri_familiar;		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_produto_fornecedor_det.php?idpf={$this->idpf}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não editado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoProdutoFornecedor\n-->";
		return false;

		
	}

	function Excluir()
	{
		@session_start();
	    $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsAlimentacaoProdutoFornecedor();
		$obj->idpf = $this->idpf;
		$excluiu = $obj->exclui();
		if( $excluiu )
		{
			$this->mensagem .= "Cadastro excluído com sucesso.<br>";
			header( "Location: alimentacao_produto_fornecedor_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não excluído.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoProdutoFornecedor\n-->";
		return false;

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
