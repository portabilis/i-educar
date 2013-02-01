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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/imagem/clsPortalImagem.inc.php");
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Menu Suspenso" );
		$this->processoAp = "445";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		@session_start();
			$id_pessoa = $_SESSION['id_pessoa'];
	 	@session_write_close();

		$this->titulo = "Detalhe do Menu";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_menu = @$_GET['cod_menu'];

		$obj_menu_suspenso = new clsMenuSuspenso();
		$lista = $obj_menu_suspenso->lista(false,false,false,false,false,false,false,false,$cod_menu,false,false,"cod_menu ASC, ord_menu ASC");
		if($lista)
		{
			$tabela = "<style> .tds{ padding-left:5px; padding-right:5px; }</style>";
			$tabela .= "<table border='0' cellpadding='1' cellspacing='3' width='100%'>";
			$tabela .= "<tr  bgcolor='#B3BABF'><td class='tds'>Ordem</td><td class='tds'>Menu Pai</td><td class='tds'nowrap>Submenu</td><td class='tds'>Título</td><td class='tds'>Ícone</td><td class='tds'>Caminho</td><td class='tds'>Supre</td></tr>";
			foreach ($lista as $menu)
			{

				$ref_cod_menu_pai = $menu['ref_cod_menu_pai'];

				$obj_menu_suspenso2 = new clsMenuSuspenso($ref_cod_menu_pai);
				$detalhe = $obj_menu_suspenso2->detalhe();
				$ds_menu_pai = $detalhe['tt_menu'];

				$ref_cod_menu_submenu = $menu['ref_cod_menu_submenu'];
				if($ref_cod_menu_submenu)
				{
					$db = new clsBanco();
					$ds_menu_submenu = $db->CampoUnico("SELECT nm_submenu FROM menu_submenu WHERE cod_sistema = 2 AND cod_menu_submenu = {$ref_cod_menu_submenu}");
				}
				$suprime_menu = $menu['suprime_menu'];
				if ($suprime_menu == 1)
				{
					$ds_suprime_menu = 'Sim';
				}
				else
				{
					$ds_suprime_menu = 'Não';
				}
				$ObjImagem = new clsPortalImagem($menu[4]);
				$detalheImagem = $ObjImagem->detalhe();
				if($detalheImagem)
					$ico_menu 			  = "<img src='imagens/banco_imagens/{$detalheImagem['caminho']}' alt='' title='' width='12' height='12'>";
				else
					$ico_menu = "S/";
				$titulo 			  = $menu['tt_menu'];
				$ordem 				  = $menu['ord_menu'];
				$caminho 			  = $menu['caminho'];
				$alvo 				  = $menu['alvo'];

				$tabela .= "<tr><td class='tds' align='right'>{$ordem}</td><td class='tds' align='right'>{$ds_menu_pai}</td><td class='tds'>{$ds_menu_submenu}</td><td class='tds'>{$titulo}</td><td class='tds'align='center'>{$ico_menu}</td><td class='tds'>{$caminho}<td class='tds'align='center'>{$ds_suprime_menu}</td></tr>";
			}
			$tabela .= "</table>";

		}
		$this->addDetalhe( array("Menu", $tabela) );


		//$this->url_novo = "menu_suspenso_cad.php";
		$this->url_editar = "menu_suspenso_cad.php?cod_menu={$cod_menu}";
		$this->url_cancelar = "menu_suspenso_lst.php";
		$this->largura = "100%";
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
