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
require_once( "include/pmicontrolesis/clsPmicontrolesisSubmenuPortal.inc.php" );
require_once( "include/pmicontrolesis/clsPmicontrolesisMenuPortal.inc.php" );

class clsIndexBase extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Submenu Portal" );
		$this->processoAp = "613";
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

	var $cod_submenu_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_menu_portal;
	var $nm_submenu;
	var $arquivo;
	var $target;
	var $title;
	var $ordem;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Submenu Portal - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(

			"Nome Submenu",

		) );

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmicontrolesisMenuPortal" ) )
		{
			$objTemp = new clsPmicontrolesisMenuPortal();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_menu_portal']}"] = "{$registro['nm_menu']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmicontrolesisMenuPortal nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}


		// outros Filtros
		$this->campoTexto( "nm_submenu", "Nome Submenu", $this->nm_submenu, 30, 255, false );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_submenu_portal = new clsPmicontrolesisSubmenuPortal();
		$obj_submenu_portal->setOrderby( "nm_submenu ASC" );
		$obj_submenu_portal->setLimite( $this->limite, $this->offset );

		$lista = $obj_submenu_portal->lista(
			null,
			null,
			null,
			null,
			$this->nm_submenu,
			null,
			null,
			null,
			null,
			null,
			null,1
		);

		$total = $obj_submenu_portal->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data

				$this->addLinhas( array(
					"<a href=\"controlesis_submenu_portal_det.php?cod_submenu_portal={$registro["cod_submenu_portal"]}\">{$registro["nm_submenu"]}</a>",

				) );
			}
		}
		$this->addPaginador2( "controlesis_submenu_portal_lst.php", $total, $_GET, $this->nome, $this->limite );
		$this->acao = "go(\"controlesis_submenu_portal_cad.php\")";
		$this->nome_acao = "Novo";
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