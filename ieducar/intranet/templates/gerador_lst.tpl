require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/#nome_schema#/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - #nome_pagina#" );
		$this->processoAp = "0";
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $__pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $__titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $__limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $__offset;

	#inicia_variaveis#

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "#nome_pagina# - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			#lst_cabecalho#
		) );

		// Filtros de Foreign Keys
#campos_fk#

		// outros Filtros
#pesquisa_lst#

		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj_#nome_tabela# = new #nome_classe#();
		$obj_#nome_tabela#->setOrderby( "#campo_orderby# ASC" );
		$obj_#nome_tabela#->setLimite( $this->__limite, $this->__offset );

		$lista = $obj_#nome_tabela#->lista(
			#metodo_lista_lst#
		);

		$total = $obj_#nome_tabela#->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
#muda_campos_data#
				// pega detalhes de foreign_keys
#foreign_detalhes#
				$this->addLinhas( array(
					#linha_lista#
				) );
			}
		}
		$this->addPaginador2( "#nome_schema_limpo#_#nome_tabela#_lst.php", $total, $_GET, $this->nome, $this->__limite );
#verificacao_especial_det_ini#
		$this->acao = "go(\"#nome_schema_limpo#_#nome_tabela#_cad.php\")";
		$this->nome_acao = "Novo";
#verificacao_especial_det_end#
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
