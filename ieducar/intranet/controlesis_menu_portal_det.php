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
require_once( "include/pmicontrolesis/clsPmicontrolesisMenuPortal.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Menu Portal" );
		$this->processoAp = "612";
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

	var $cod_menu_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $nm_menu;
	var $title;
	var $caminho;
	var $cor;
	var $posicao;
	var $ordem;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		$this->titulo = "Menu Portal - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_menu_portal=$_GET["cod_menu_portal"];

		$tmp_obj = new clsPmicontrolesisMenuPortal( $this->cod_menu_portal );
		$registro = $tmp_obj->detalhe();


		if( $registro["nm_menu"] )
		{
			$this->addDetalhe( array( "Nome Menu", "{$registro["nm_menu"]}") );
		}
		if( $registro["title"] )
		{
			$this->addDetalhe( array( "Title", "{$registro["title"]}") );
		}
		if( $registro["caminho"] )
		{
			$this->addDetalhe( array( "Caminho", "<img src='imagens/{$registro["caminho"]}' alt='{$registro["nm_menu"]}'>") );
		}
		if( $registro["cor"] )
		{
			$this->addDetalhe( array( "Cor", "{$registro["cor"]}") );
		}
		if( $registro["posicao"] )
		{
			$registro["posicao"] = $registro["posicao"]=='E' ? 'Esquerda' : 'Direita';
			$this->addDetalhe( array( "Posição", "{$registro["posicao"]}") );
		}
		if( $registro["ordem"] )
		{
			$this->addDetalhe( array( "Ordem", "{$registro["ordem"]}") );
		}
		$this->url_novo = "controlesis_menu_portal_cad.php";
		$this->url_editar = "controlesis_menu_portal_cad.php?cod_menu_portal={$registro["cod_menu_portal"]}";
		$this->url_cancelar = "controlesis_menu_portal_lst.php";
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