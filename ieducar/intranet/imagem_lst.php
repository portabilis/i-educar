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
require_once ("include/imagem/clsPortalImagemTipo.inc.php");
require_once ("include/imagem/clsPortalImagem.inc.php");
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Banco de Imagens" );
		$this->processoAp = "473";
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
		$this->addCabecalhos( array( "Nome da Imagem","Imagem", "Tipo") );

		// Filtros de Busca
		$this->campoTexto("imagem","Nome Imagem ","",50,255);
		//$this->campoTexto("unidade","Unidade","",30,255);		
		// Paginador
		  
		$limite = 20;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;		
		$obj_menu = new clsPortalImagem();
		$obj_menu->setLimite($limite, $iniciolimit );
		$obj_menu->setOrderby("cod_imagem");
		$lista_menu = $obj_menu->lista(false,false,false,false, false, false, false, $_GET['imagem']);
		if($lista_menu)
		{
			foreach ($lista_menu as $menu) 
			{
				$obj_tipo = new clsPortalImagemTipo();
				$lista_tipo = $obj_tipo->lista($menu['ref_cod_imagem_tipo'] );				
				if($lista_tipo)
				{
					foreach ($lista_tipo as $tipo) 
					{		
						$menu['nm_imagem'] = ($menu['nm_imagem'] == "") ? "S/N":$menu['nm_imagem'] ;
						$this->addLinhas(array("<a href='imagem_det.php?cod_imagem={$menu['cod_imagem']}'  width=16 height=16><img src='imagens/noticia.jpg' border=0> {$menu['nm_imagem']}</a>","<img src='imagens/banco_imagens/{$menu['caminho']}' alt='{$menu['nm_imagem']}' title='{$menu['nm_imagem']}'  width=16 height=16>" ,$tipo['nm_tipo']));
						$total = $menu['_total'];
					}
				}
			}
		}		
		
		// Paginador
		$this->addPaginador2( "imagem_lst.php", $total, $_GET, $this->nome, $limite );		
		$this->acao = "go(\"imagem_cad.php\")";
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