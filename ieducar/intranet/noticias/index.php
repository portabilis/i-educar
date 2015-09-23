<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itajaí								 *
*	@version 1.0.0b														 *
*	@updated 26/03/2006													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
*						prefeitura@itajai.sc.gov.br						 *
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


header("Content-type: text/xml");

require_once ("../include/clsXmlTag.inc.php");
require_once ("../include/clsXML.inc.php");
require_once ("../include/clsBancoPgSql.inc.php");
require_once ("../include/clsBanco.inc.php");

$db = new clsBanco();
$objConfig = new clsConfig();

/*$ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
$ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];

$db->Consulta("INSERT INTO acesso (data_hora, ip_externo, ip_interno, cod_pessoa, obs ) VALUES (now(), '{$ip}', '{$ip_de_rede}', 0, 'RSS NOTICIAS')");*/

$xml = new clsXML();
$xml->criaTag( "rss", "version=\"2.0\"", "", true );
	$xml->criaTag( "channel", "", "", true );
		$xml->criaTag( "title", "", "Prefeitura de Itajaí - Noticias", false );
		$xml->criaTag( "link", "", "{$objConfig->arrayConfig["strSiteUrl"]}", false );
		$xml->criaTag( "description", "", "Notícias", false );
		$xml->criaTag( "language", "", "pt-br", false );
		$xml->criaTag( "copyright", "", "GPL", false );
		$xml->criaTag( "image", "", "", true );
			$xml->criaTag( "title", "", "Prefeitura de Itajaí", false );
			$xml->criaTag( "width", "", "203", false );
			$xml->criaTag( "height", "", "95", false );
			$xml->criaTag( "link", "", "{$this->arrayConfig["strSiteUrl"]}", false );
			$xml->criaTag( "url", "", "http://forum.itajai.sc.gov.br/templates/subSilver/images/logo_phpBB.gif", false );
		$xml->voltaPai();
		$xml->criaTag( "webMaster", "", $objConfig->arrayConfig['strAdminIntraEmail'], false );
		$xml->criaTag( "pubDate", "", date('D, d M Y G:i:s T'), false );
		
		$db->Consulta("SELECT cod_not_portal, titulo, data_noticia, descricao FROM not_portal ORDER BY data_noticia DESC LIMIT 0, 15");
		while ($db->ProximoRegistro())
		{
			list ($cod_not_portal, $titulo, $data_noticia, $descricao) = $db->Tupla();
			
			$tdia =  date('d', strtotime(substr($data_noticia,0,19)));
			$tmes =  date('m', strtotime(substr($data_noticia,0,19)));
			$tano =  date('Y', strtotime(substr($data_noticia,0,19)));
			$thora =  date('H', strtotime(substr($data_noticia,0,19)));
			$tminuto =  date('i', strtotime(substr($data_noticia,0,19)));
			$data_noticia = date('d/m/Y - H:i', strtotime(substr($data_noticia,0,19)));
			$descricao = substr($descricao, 0, 300)."...";

		$xml->criaTag( "item", "", "", true );
			$xml->criaTag( "title", "", "{$titulo}", false );
			$xml->criaTag( "link", "",  "{$this->arrayConfig["strSiteUrl"]}noticias_det.php?id_noticia={$cod_not_portal}", false );
			$xml->criaTag( "description", "", "{$descricao}", false );
			$xml->criaTag( "pubDate", "", date('D, d M Y G:i:s T', mktime($thora, $tminuto, 0, $tmes, $tdia, $tano)), false );
			$xml->voltaPai();
		}

$xml->codificacaoXML = "ISO-8859-1";
echo $xml->geraXml();
?>