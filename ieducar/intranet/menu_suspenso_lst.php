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
require_once ("include/clsListagem.inc.php");


class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Menu Suspenso" );
		$this->processoAp = "445";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		@session_start();
			$id_pessoa = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet",false );
		$this->addCabecalhos( array( "Nome Menu") );

		// Filtros de Busca
		$this->campoTexto("nome_menu","Nome Menu","",50,255);
		//$this->campoTexto("unidade","Unidade","",30,255);
		
		
		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$obj_menu = new clsTutormenu();
		$lista_menu = $obj_menu->lista(false,false,false,"nm_tutormenu ASC");
		if($lista_menu)
		{
			foreach ($lista_menu as $menu) 
			{
				$this->addLinhas(array("<img src='imagens/noticia.jpg' border=0> <a href='menu_suspenso_det.php?cod_menu={$menu['cod_tutormenu']}'>{$menu['nm_tutormenu']}</a>"));
			}
		}
		
		// Paginador
		$this->addPaginador2( "menu_suspenso_lst.php", $total, $_GET, $this->nome, $limite );
		
		
		$this->acao = "go(\"tutormenu_cad.php\")";
		$this->nome_acao = "Novo";			
		
		// Define Largura da Página
		$this->largura = "100%";
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();

?>