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
		$this->SetTitulo( "{$this->_instituicao} Fotos" );
		$this->processoAp = "669";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe de fotos";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$id_foto = @$_GET['id_foto'];

		$objPessoa = new clsPessoaFisica();
		$db = new clsBanco();
		$db->Consulta( "SELECT f.ref_ref_cod_pessoa_fj, f.nm_credito, f.data_foto, f.titulo, f.descricao, f.caminho, f.altura, f.largura, f.ref_cod_foto_secao FROM foto_portal f WHERE cod_foto_portal={$id_foto}" );
		if ($db->ProximoRegistro())
		{
			list ($cod_pessoa, $nm_credito, $data, $titulo, $descricao, $foto, $altura, $largura, $secao ) = $db->Tupla();
			list($nome) = $objPessoa->queryRapida($cod_pessoa, "nome");
			
			$data = date('d/m/Y', strtotime(substr($data,0,19) ));
			
			

			$this->addDetalhe( array("Data", $data) );
			$this->addDetalhe( array("T&iacute;tulo", $titulo) );
			$this->addDetalhe( array("Criador", $nome) );
			$this->addDetalhe( array("Credito", $nm_credito) );
			//echo $foto;
			$this->addDetalhe( array("Foto", "<a href='#' onclick='javascript:openfoto(\"$foto\", \"$altura\",  \"$largura\")'><img src='fotos/small/{$foto}' border='0'></a>") );
		}
		$this->url_novo = "fotos_galeria_cad.php";
		$this->url_editar = "fotos_galeria_cad.php?id_foto=$id_foto";
		$this->url_cancelar = "fotos_galeria_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>