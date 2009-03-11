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
require_once ("include/clsBanco.inc.php");
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

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe da Imagem";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_imagem = @$_GET['cod_imagem'];

		$objimagem = new clsPortalImagem($cod_imagem);
		$detalheImagem = $objimagem->detalhe();
		$objimagemTipo = new clsPortalImagemTipo($detalheImagem['ref_cod_imagem_tipo']);
		$detalheImagemTipo = $objimagemTipo->detalhe();
		
		$this->addDetalhe( array("Tipo da Imagem", $detalheImagemTipo['nm_tipo']));
		$this->addDetalhe( array("Nome", $detalheImagem['nm_imagem']));
		$this->addDetalhe( array("Imagem", "<img src='banco_imagens/{$detalheImagem['caminho']}' alt='{$detalheImagem['nm_imagem']}' title='{$detalheImagem['nm_imagem']}'>"));
		$this->addDetalhe( array("Extensão", "{$detalheImagem['extensao']}"));
		$this->addDetalhe( array("Largura", "{$detalheImagem['largura']}"));
		$this->addDetalhe( array("Altura", "{$detalheImagem['altura']}"));
		$this->addDetalhe( array("Data de Cadastro", date("d/m/Y", strtotime(substr($detalheImagem['altura'],0,19)) )));		
		$this->url_novo = "imagem_cad.php";
		$this->url_editar = "imagem_cad.php?cod_imagem={$cod_imagem}";
		$this->url_cancelar = "imagem_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>