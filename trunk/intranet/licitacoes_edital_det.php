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
		$this->SetTitulo( "{$this->_instituicao} Edital" );
		$this->processoAp = "239";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$db = new clsBanco();
		$db2 = new clsBanco();
		$this->titulo = "Detalhe do Edital";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_edital = @$_GET['cod_edital'];
		
		$db->Consulta( "SELECT ref_cod_compras_licitacoes, versao, data_hora, ref_ref_cod_pessoa_fj, arquivo, motivo_alteracao, visivel FROM compras_editais_editais WHERE cod_compras_editais_editais = '{$cod_edital}'" );
		$db->ProximoRegistro();
		list( $cod_licitacao, $versao, $data_hora, $ref_pessoa, $arquivo, $motivo_alteracao, $visivel ) = $db->Tupla();

		$strVersoes = "";
		$db2->Consulta( "SELECT cod_compras_editais_editais, versao FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = $cod_licitacao ORDER BY versao ASC" );
		while ( $db2->ProximoRegistro() ) 
		{
			list( $cod_sub_edital, $sub_versao ) = $db2->Tupla();
			if( $sub_versao != $versao )
			{
				$strVersoes .= " <a href=\"licitacoes_edital_det.php?cod_edital={$cod_sub_edital}\">{$sub_versao}</a>";
			}
		}
		if( $strVersoes )
		{
			$this->addDetalhe( array( "Vers&otilde;es Anteriores", $strVersoes ) );
		}
		
		if( ! $visivel )
		{
			$this->addDetalhe( array( "Oculto" , "<b>Este edital esta oculto</b>" ) );
		}
		else 
		{
			$this->addDetalhe( array( "Visivel" , "Este edital esta visivel" ) );
		}
		
		$motivo_alteracao = str_replace( "\n", "<br>", $motivo_alteracao );
		$this->addDetalhe( array("Motivo da altea&ccedil;&atilde;o", $motivo_alteracao ) );
		
		$this->addDetalhe( array("Versão do Edital", $versao ) );
		$this->addDetalhe( array("Data da alteração", date( "d/m/Y H:i", strtotime(substr( $data_hora,0,19) ) ) ) );
		$objPessoa = new clsPessoaFisica();
		$resp_nome = $objPessoa->queryRapida( $ref_pessoa, "nome" );
		$this->addDetalhe( array( "Responsável", $resp_nome[0] ) );
		$extensao = substr( $arquivo, -3 );
		switch ( $extensao )
		{
			case "zip":
				$imagem = "imagens/nvp_icon_zip.gif";
				break;
			case "pdf":
				$imagem = "imagens/nvp_icon_pdf.gif";
				break;
			case "doc":
				$imagem = "imagens/nvp_icon_doc.gif";
				break;
			default:
				$imagem = "imagens/nvp_icon_download.gif";
				break;
		}
		$this->addDetalhe( array("Tipo de Arquivo", "<img src=\"$imagem\"> $extensao" ) );
		$this->addDetalhe( array("Arquivo", "<a href=\"{$arquivo}\">{$arquivo}</a>" ) );
		
		$db->Consulta( "SELECT m.nm_modalidade, l.numero, l.objeto, l.data_hora FROM compras_licitacoes l, compras_modalidade m WHERE m.cod_compras_modalidade=l.ref_cod_compras_modalidade AND cod_compras_licitacoes='{$cod_licitacao}'" );
		if ($db->ProximoRegistro())
		{
			list ($nm_modalidade, $numero, $objeto, $data ) = $db->Tupla();

			$this->addDetalhe( array("Numero da Licitação",$numero) );
			$this->addDetalhe( array("Modalidade",$nm_modalidade) );
			$this->addDetalhe( array("Data da Licitação", date( 'd/m/Y', strtotime(substr( $data,0,19) ) ) ) );
		}
		$this->url_novo = "licitacoes_edital_cad.php";
		$this->url_editar = "licitacoes_edital_cad.php?cod_edital=$cod_edital";
		$this->url_cancelar = "licitacoes_edital_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>