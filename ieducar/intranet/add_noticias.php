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
		$this->SetTitulo( "{$this->_instituicao} Fotos!" );
		$this->processoAp = "26";
		$this->renderMenu = false;
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		@session_start();
		$_SESSION["campo"] = $_GET["campo"] ? $_GET["campo"] : $_SESSION["campo"];
		$this->nome = "form1";
		
		$this->titulo = "Not&iacute;cias";

		
		$this->addCabecalhos( array("Selecionar", "Data", "T&iacute;tulo", "Criador") );
		
		$objPessoa = new clsPessoaFisica();
		$db = new clsBanco();
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		$total = $db->CampoUnico("SELECT count(*) FROM not_portal");
		$db->Consulta( "SELECT n.ref_ref_cod_pessoa_fj, cod_not_portal, n.data_noticia, n.titulo, n.descricao FROM not_portal n ORDER BY n.data_noticia DESC LIMIT $iniciolimit,$limite" );
		while ($db->ProximoRegistro())
		{
			list ($cod_pessoa, $id_noticia, $data, $titulo, $descricao) = $db->Tupla();
			list($nome) = $objPessoa->queryRapida($cod_pessoa, "nome");
			$data = date('d/m/Y', strtotime(substr($data,0,19)));
			$campo = @$_GET['campo'];

			$this->addLinhas( array("<center><a href='javascript:void(0);' onclick='javascript:retorna(\"{$this->nome}\", \"{$campo}\", \"{$id_noticia}\")'><img  width='20' height='20' src='imagens/noticia.jpg' border=0>", $data, $titulo, $nome." ".$sobrenome) );
		}
		$this->addPaginador2( "add_noticias.php", $total, $_GET, $this->nome, $limite );
		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>