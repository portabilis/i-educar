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
		$this->SetTitulo( "{$this->_instituicao} Not&iacute;cias" );
		$this->processoAp = "26";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe de not&iacute;cias";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$id_noticia = @$_GET['id_noticia'];

		$db = new clsBanco();
		$db->Consulta( "SELECT n.titulo, n.data_noticia, n.descricao, n.ref_ref_cod_pessoa_fj FROM not_portal n WHERE cod_not_portal={$id_noticia}" );
		if ($db->ProximoRegistro())
		{
			list ($titulo, $data, $descricao, $cod_responsavel) = $db->Tupla();
			$objPessoa = new clsPessoaFj();
			list($responsavel) = $objPessoa->queryRapida($cod_responsavel,"nome");
			$data = explode(".",$data);
			$data= date("d/m/Y", strtotime(substr($data[0],0,19) ));

			$this->addDetalhe( array("Responsável", $responsavel) );
			$this->addDetalhe( array("Data", $data) );
			
			$this->addDetalhe( array("T&iacute;tulo", $titulo) );

			$descricao = str_replace("\n\r", "<br>", $descricao);
			$descricao = str_replace("\n", "<br>", $descricao);

			$this->addDetalhe( array("Descri&ccedil;&atilde;o", $descricao) );
			
			$db->Consulta( "SELECT tipo,cod_vinc,caminho,nome_arquivo FROM not_vinc_portal n WHERE ref_cod_not_portal={$id_noticia}" );

			while($db->ProximoRegistro())
			{
				list($tipo,$cod,$caminho,$nome_arquivo) = $db->Tupla();
				if($tipo =="F")
				{
					$dba = new clsBanco();
					$dba->Consulta( "SELECT titulo, caminho, altura, largura FROM foto_portal WHERE cod_foto_portal={$cod}" );
					$dba->ProximoRegistro();
					list ($titulo,$caminho,$altura,$largura) = $dba->Tupla();
					$this->addDetalhe( array("Fotos Vinculadas", "<a href='#' onclick='javascript:openfoto(\"$titulo\",\"$caminho\",$altura,$largura)'><img src='fotos/small/{$caminho}' border='0'></a>") );
					
				}
				if($tipo =="N")
				{
					$dba = new clsBanco();
					$dba->Consulta( "SELECT titulo FROM not_portal WHERE cod_not_portal={$cod}" );
					$dba->ProximoRegistro();
					list ($titulo) = $dba->Tupla();
					$this->addDetalhe( array("Noticias Vinculadas", "<img src='imagens/noticia.jpg' border=0>&nbsp;<a href='noticias_det.php?id_noticia=$cod'><strong>$titulo</strong></a>") );
					$dba->Consulta( "SELECT v.cod_vinc, n.titulo FROM not_vinc_portal v, not_portal n WHERE v.ref_cod_not_portal={$cod} AND v.tipo='N' AND v.cod_vinc = n.cod_not_portal " );
					while($dba->ProximoRegistro())
					{
						list($cod, $titulo) = $dba->Tupla();
						$this->addDetalhe( array("Noticias Vinculadas", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='imagens/noticia.jpg' border=0>&nbsp;<a href='noticias_det.php?id_noticia=$cod'><strong>$titulo</strong></a>") );
					} 
				}
				if($tipo =="A")
				{
					$this->addDetalhe( array("Arquivos Vinculados", "<strong>$nome_arquivo</strong> &nbsp; <a href='$caminho'><img  width='20' height='20' src='imagens/noticia.jpg' border=0></a>") );
				}
			
			}
		}
		$this->url_novo = "noticias_cad.php";
		$this->url_editar = "noticias_cad.php?id_noticia=$id_noticia";
		$this->url_cancelar = "noticias_lst.php";
		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>