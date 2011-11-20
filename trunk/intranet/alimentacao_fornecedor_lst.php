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
require_once ("include/clsListagem.inc.php");
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

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;
	
	var $cnpj;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Fornecedor - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$obj_permissao = new clsPermissoes();
		
		$lista_busca = array(
					"Fornecedor",
					"CNPJ",
					"Descrição"
		);

		$this->addCabecalhos($lista_busca);

		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 1 );
		$parametros->setPessoa( 'J' );
		//$parametros->setPessoaCampo('sem_cnpj');
		$parametros->setPessoaNovo( "S" );
		$parametros->setPessoaCPF("N");
		$parametros->setPessoaTela('window');
		//$this->campoOculto( "sem_cnpj", "" );
		$parametros->setCodSistema(13);
		$parametros->adicionaCampoTexto( "cnpj", "cnpj" );
		$this->campoCnpjPesq( "cnpj", "CNPJ", $this->cnpj, "pesquisa_pessoa_lst.php", $parametros->serializaCampos(), true );
			
			
		$obj_fornecedor = new clsAlimentacaoFornecedor();
		$filtro_cnpj = null;
		if($this->cnpj != "")
		{
			$filtro_cnpj = str_replace("/","",str_replace("-","",str_replace(".","",$this->cnpj)));
			if ( ! is_numeric(filtro_cnpj))
			{
				$filtro_cnpj = null;
			}
		}
		$lista = $obj_fornecedor->lista(null,$filtro_cnpj);
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$lista_busca = array();
				$lista_busca[] = "<a href=\"alimentacao_fornecedor_det.php?idpes={$registro["idpes"]}\">{$registro["fantasia"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_fornecedor_det.php?idpes={$registro["idpes"]}\">{$registro["cnpj"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_fornecedor_det.php?idpes={$registro["idpes"]}\">{$registro["descricao"]}</a>";
				$this->addLinhas($lista_busca);
			}
			
		}

		if( $obj_permissao->permissao_cadastra( 10003, $this->pessoa_logada, 3 ) )
		{		
			$this->acao = "go(\"alimentacao_fornecedor_cad.php\")";
			$this->nome_acao = "Novo";
		}
		
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