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
		$this->SetTitulo( "{$this->_instituicao} Grupos de Email!" );
		$this->processoAp = "85";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe do Grupo";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		$id_grupo = @$_GET['id_grupo'];
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_mailling_grupo, nm_grupo FROM mailling_grupo WHERE cod_mailling_grupo={$id_grupo}" );
		if ($db->ProximoRegistro())
		{
			list ($cod_grupo, $nome) = $db->Tupla();
			$this->addDetalhe( array("Nome", $nome) );
		}
		$db->Consulta("SELECT nm_pessoa, email FROM mailling_grupo_email mge, mailling_email me WHERE ref_cod_mailling_grupo={$id_grupo} AND cod_mailling_email=ref_cod_mailling_email");
		while ($db->ProximoRegistro()) {
			list($nome, $email) = $db->Tupla();
			$this->addDetalhe(array("Emails Vinculados", "{$nome} - {$email}"));
		}
		
		$this->url_novo = "mailling_grupos_cad.php";
		$this->url_editar = "mailling_grupos_cad.php?id_grupo={$id_grupo}";
		$this->url_cancelar = "mailling_grupos_lst.php";
		$this->largura = "100%";
	}
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>