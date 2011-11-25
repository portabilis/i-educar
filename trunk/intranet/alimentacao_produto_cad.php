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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Produto " );
		$this->processoAp = "10002";
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

	var $idpro;
	var $nm_produto;
	var $fator_correcao;
	var $fator_coccao;
	var $ref_produto_grupo;
	var $ref_produto_unidade;
	var $calorias;
	var $proteinas;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->idpro=$_GET["idpro"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10002, $this->pessoa_logada,3, "alimentacao_produto_lst.php" );

		if( is_numeric( $this->idpro ) )
		{
			$obj_produto = new clsAlimentacaoProduto();
			$lst = $obj_produto->lista($this->idpro);
			
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
					header( "Location: alimentacao_produto_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_produto_det.php?idpro={$registro["idpro"]}" : "alimentacao_produto_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idpro", $this->idpro );

		// text
		$this->campoTexto( "nm_produto", "Produto", $this->nm_produto, 30, 255, true );
		
		$this->campoNumero( "fator_correcao", "Fator Correção", $this->fator_correcao, 30, 255, true );
		
		$this->campoNumero( "fator_coccao", "Fator Cocção", $this->fator_coccao, 30, 255, true );
		
		$opcoes = array();
		$obj_produto_grupo = new clsAlimentacaoProdutoGrupo();
		$lista = $obj_produto_grupo->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["idpg"]] = $registro["descricao"];
			}
			
		}
		$this->campoLista( "ref_produto_grupo", "Grupo", $opcoes, $this->ref_produto_grupo,"",false,"","","",true );
		
		$opcoes = array();
		$obj_produto_unidade = new clsAlimentacaoProdutoUnidade();
		$lista = $obj_produto_unidade->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["idpu"]] = $registro["descricao"];
			}
			
		}
		$this->campoLista( "ref_produto_unidade", "Unidade de medida", $opcoes, $this->ref_produto_unidade,"",false,"","","",true );
		
		
		$this->campoNumero( "calorias", "Calorias(Kcal)", $this->calorias, 30, 255, true, "Quantidade há cada 100 gramas ou ml" );
		
		$this->campoNumero( "proteinas", "Proteínas(gramas)", $this->proteinas, 30, 255, true, "Quantidade há cada 100 gramas ou ml" );		

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		

		$obj = new clsAlimentacaoProduto();
		$obj->ref_produto_grupo = $this->ref_produto_grupo;
		$obj->ref_produto_unidade = $this->ref_produto_unidade;
		$obj->nm_produto = $this->nm_produto;
		$obj->fator_correcao = $this->fator_correcao;
		$obj->fator_coccao = $this->fator_coccao;
		$obj->calorias = $this->calorias;
		$obj->proteinas = $this->proteinas;
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_produto_lst.php" );
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
		
		$obj = new clsAlimentacaoProduto();
		$obj->idpro = $this->idpro;
		$obj->ref_produto_grupo = $this->ref_produto_grupo;
		$obj->ref_produto_unidade = $this->ref_produto_unidade;
		$obj->nm_produto = $this->nm_produto;
		$obj->fator_correcao = $this->fator_correcao;
		$obj->fator_coccao = $this->fator_coccao;
		$obj->calorias = $this->calorias;
		$obj->proteinas = $this->proteinas;
		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_produto_det.php?idpro={$this->idpro}" );
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
