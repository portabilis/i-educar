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
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Banner!" );
		$this->processoAp = "89";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Banners";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		
		$this->addCabecalhos( array( "Banner", "Prioridade") );
		
		$db = new clsBanco();
		
		$soma_lateral = $db->UnicoCampo( "SELECT SUM(prioridade) FROM portal_banner WHERE lateral=1" );
		$soma_centro = $db->UnicoCampo( "SELECT SUM(prioridade) FROM portal_banner WHERE lateral=0" );
		
		$db->Consulta( "SELECT cod_portal_banner, caminho, title, prioridade, link, lateral FROM portal_banner ORDER BY prioridade, title" );
		while ($db->ProximoRegistro())
		{
			list ($cod_portal_banner, $caminho, $title, $prioridade, $link, $lateral) = $db->Tupla();

			if ($lateral)
			{
				$porcentagem = number_format((100*$prioridade)/$soma_lateral, 2)."%";
			}
			else
			{
				$porcentagem = number_format((100*$prioridade)/$soma_centro, 2)."%";
			}
			
			$prioridade *= 15;
			$prioridade = $prioridade > 600 ? 600 : $prioridade;

			$this->addLinhas( array("<a href='banner_det.php?cod_portal_banner=$cod_portal_banner'><img src='fotos/imgs/{$caminho}' border=\"0\" width=\"149\"></a>", "<img src='imagens/grafico_hp.png' border=0 height='8' width='{$prioridade}'><br>{$link}<br>{$porcentagem}"));
		}
		$this->acao = "go(\"banner_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>