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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Produto Fornecedor" );
		$this->processoAp = "10004";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $idpf;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Produto Fornecedor - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->idpf=$_GET["idpf"];
		
		$obj_produto_fornecedor = new clsAlimentacaoProdutoFornecedor();
		$lista = $obj_produto_fornecedor->lista($this->idpf);
		$registro = $lista[0];

		if( ! $registro )
		{
			header( "location: alimentacao_produto_fornecedor_lst.php" );
			die();
		}
		
		$this->addDetalhe( array( "Produto", $registro["nm_produto"]) );

		$this->addDetalhe( array( "Fornecedor", $registro["fantasia"]) );
		
		$this->addDetalhe( array( "Ano", $registro["ano"]) );
		
		$this->addDetalhe( array( "Mês Início", $obj_produto_fornecedor->getMes($registro["mes_inicio"])) );
		
		$this->addDetalhe( array( "Mês Fim", $obj_produto_fornecedor->getMes($registro["mes_fim"])) );
		
		$this->addDetalhe( array( "Quantidade por Un.", $registro["pesoouvolume_un"]." ".$registro["unidade"]) );
		
		$this->addDetalhe( array( "Preço Un.", "R$".str_replace(".",",",$registro["preco_un"])) );
		
		$this->addDetalhe( array( "Agricultura Familiar", $obj_produto_fornecedor->getAgri($registro["agri_familiar"])) );



		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 10004, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "alimentacao_produto_fornecedor_cad.php";
            $this->url_editar = "alimentacao_produto_fornecedor_cad.php?idpf={$this->idpf}";

		}

		$this->url_cancelar = "alimentacao_produto_fornecedor_lst.php";
		$this->largura = "100%";
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