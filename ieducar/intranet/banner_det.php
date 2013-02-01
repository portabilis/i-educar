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

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Banner" );
		$this->processoAp = "89";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe do Banner";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_portal_banner = @$_GET['cod_portal_banner'];

		$objPessoa = new clsPessoaFisica();
		
		$db = new clsBanco();
		$db->Consulta( "SELECT b.ref_ref_cod_pessoa_fj, b.cod_portal_banner, b.caminho, b.title, b.prioridade, b.link, b.lateral FROM portal_banner b WHERE b.cod_portal_banner={$cod_portal_banner}" );
		if ($db->ProximoRegistro())
		{
			list ($cod_pessoa, $cod_portal_banner, $caminho, $title, $prioridade, $link, $lateral ) = $db->Tupla();
			list ($nm_pessoa) = $objPessoa->queryRapida($cod_pessoa, "nome");
			$this->addDetalhe( array("Responsável", $nm_pessoa) );
			$this->addDetalhe( array("Title", $title) );
			$this->addDetalhe( array("Prioridade", $prioridade) );
			$this->addDetalhe( array("Link", $link) );
			$lateral = ( $lateral ) ? "Sim": "Não";
			$this->addDetalhe( array("Lateral", $lateral ) );
			
			$this->addDetalhe( array("Banner", "<img src='fotos/imgs/{$caminho}' title='{$title}' width=\"149\">") );
		}
		$this->url_novo = "banner_cad.php";
		$this->url_editar = "banner_cad.php?cod_portal_banner=$cod_portal_banner";
		$this->url_cancelar = "banner_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>