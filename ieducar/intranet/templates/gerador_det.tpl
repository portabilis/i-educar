require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
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

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	#inicia_variaveis#

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "#nome_pagina# - Detalhe";
		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

#get_pk_from_get#
		$tmp_obj = new #nome_classe#( #pk_obj_params# );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: #nome_schema_limpo#_#nome_tabela#_lst.php" );
			die();
		}

#foreign_detalhes_det#
#adiciona_detalhes#
#verificacao_especial_det_ini#
		$this->url_novo = "#nome_schema_limpo#_#nome_tabela#_cad.php";
		$this->url_editar = "#nome_schema_limpo#_#nome_tabela#_cad.php#get_pk_params#";
#verificacao_especial_det_end#
		$this->url_cancelar = "#nome_schema_limpo#_#nome_tabela#_lst.php";
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
