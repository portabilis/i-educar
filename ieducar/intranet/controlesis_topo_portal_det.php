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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Topo Portal" );
		$this->processoAp = "694";
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
	
	var $cod_topo_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_menu_portal;
	var $caminho1;
	var $caminho2;
	var $caminho3;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Topo Portal - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_topo_portal=$_GET["cod_topo_portal"];

		$tmp_obj = new clsPmicontrolesisTopoPortal( $this->cod_topo_portal );
		$registro = $tmp_obj->detalhe();
		
		if( ! $registro )
		{
			header( "location: controlesis_topo_portal_lst.php" );
			die();
		}
		
		if( class_exists( "clsFuncionario" ) )
		{
			$obj_ref_funcionario_exc = new clsFuncionario( $registro["ref_funcionario_exc"] );
			$det_ref_funcionario_exc = $obj_ref_funcionario_exc->detalhe();
			if( is_object( $det_ref_funcionario_exc["idpes"] ) )
			{
			$det_ref_funcionario_exc = $det_ref_funcionario_exc["idpes"]->detalhe();
			$registro["ref_funcionario_exc"] = $det_ref_funcionario_exc["nome"];
			}
			else
			{
			$pessoa = new clsPessoa_( $det_ref_funcionario_exc["idpes"] );
			$det_ref_funcionario_exc = $pessoa->detalhe();
			$registro["ref_funcionario_exc"] = $det_ref_funcionario_exc["nome"];
			}
		}
		else
		{
			$registro["ref_funcionario_exc"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
		}

		if( class_exists( "clsFuncionario" ) )
		{
			$obj_ref_funcionario_cad = new clsFuncionario( $registro["ref_funcionario_cad"] );
			$det_ref_funcionario_cad = $obj_ref_funcionario_cad->detalhe();
			if( is_object( $det_ref_funcionario_cad["idpes"] ) )
			{
			$det_ref_funcionario_cad = $det_ref_funcionario_cad["idpes"]->detalhe();
			$registro["ref_funcionario_cad"] = $det_ref_funcionario_cad["nome"];
			}
			else
			{
			$pessoa = new clsPessoa_( $det_ref_funcionario_cad["idpes"] );
			$det_ref_funcionario_cad = $pessoa->detalhe();
			$registro["ref_funcionario_cad"] = $det_ref_funcionario_cad["nome"];
			}
		}
		else
		{
			$registro["ref_funcionario_cad"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
		}

		if( class_exists( "clsPmicontrolesisMenuPortal" ) )
		{
			$obj_ref_cod_menu_portal = new clsPmicontrolesisMenuPortal( $registro["ref_cod_menu_portal"] );
			$det_ref_cod_menu_portal = $obj_ref_cod_menu_portal->detalhe();
			$registro["ref_cod_menu_portal"] = $det_ref_cod_menu_portal["nm_menu"];
		}
		else
		{
			$registro["ref_cod_menu_portal"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisMenuPortal\n-->";
		}


		if( $registro["cod_topo_portal"] )
		{
			$this->addDetalhe( array( "Topo Portal", "{$registro["cod_topo_portal"]}") );
		}
		if( $registro["ref_cod_menu_portal"] )
		{
			$this->addDetalhe( array( "Menu Portal", "{$registro["ref_cod_menu_portal"]}") );
		}
		$this->addDetalhe( array( "Caminho1", "<img src='imagens/topos/{$registro["caminho1"]}' height='40'><img height='40' src='imagens/topos/{$registro["caminho2"]}'><img src='imagens/topos/{$registro["caminho3"]}' height='40'>") );
		

		$this->url_novo = "controlesis_topo_portal_cad.php";
		$this->url_editar = "controlesis_topo_portal_cad.php?cod_topo_portal={$registro["cod_topo_portal"]}";

		$this->url_cancelar = "controlesis_topo_portal_lst.php";
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