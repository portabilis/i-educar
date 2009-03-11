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

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

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
		$this->titulo = "Submenu Portal - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_submenu_portal=$_GET["cod_submenu_portal"];

		$tmp_obj = new clsPmicontrolesisSubmenuPortal( $this->cod_submenu_portal );
		$registro = $tmp_obj->detalhe();
		if( class_exists( "clsPmicontrolesisMenuPortal" ) )
		{
			$obj_ref_cod_menu_portal = new clsPmicontrolesisMenuPortal( $registro["ref_cod_menu_portal"] );
			$det_ref_cod_menu_portal = $obj_ref_cod_menu_portal->detalhe();
			$registro["ref_cod_menu_portal"] = $det_ref_cod_menu_portal["nm_menu_portal"];
		}
		else
		{
			$registro["ref_cod_menu_portal"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisMenuPortal\n-->";
		}



	if( $registro["ref_cod_menu_portal"] )
		{
			$this->addDetalhe( array( "Menu Portal", "{$registro["ref_cod_menu_portal"]}") );
		}
		if( $registro["nm_submenu"] )
		{
			$this->addDetalhe( array( "Nome Submenu", "{$registro["nm_submenu"]}") );
		}
		if( $registro["arquivo"] )
		{
			$this->addDetalhe( array( "Arquivo", "{$registro["arquivo"]}") );
		}
		if( $registro["target"] )
		{
			$registro["target"] = $registro["target"]=='S' ? '_self' : '_blank';
			$this->addDetalhe( array( "Target", "{$registro["target"]}") );
		}
		if( $registro["title"] )
		{
			$this->addDetalhe( array( "Title", "{$registro["title"]}") );
		}
		if( $registro["ordem"] )
		{
			$this->addDetalhe( array( "Ordem", "{$registro["ordem"]}") );
		}

		$this->url_novo = "controlesis_submenu_portal_cad.php";
		$this->url_editar = "controlesis_submenu_portal_cad.php?cod_submenu_portal={$registro["cod_submenu_portal"]}";
		$this->url_cancelar = "controlesis_submenu_portal_lst.php";
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