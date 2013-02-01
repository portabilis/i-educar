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
		$this->SetTitulo( "{$this->_instituicao} Not&iacute;cias!" );
		$this->processoAp = "26";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Not&iacute;cias";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->campoTexto("titulo","titulo","",50,255);
		if($_GET['titulo'])
		{
			$where = "WHERE n.titulo ilike '%{$_GET['titulo']}%' ";
		}


		$this->addCabecalhos( array( "Data", "T&iacute;tulo") );
		$db = new clsBanco();
		$db->Consulta( "SELECT count(*) FROM not_portal n $where"  );
		$db->ProximoRegistro();
		list ($total) = $db->Tupla();
		$total_tmp = $total;
		$iniciolimit = (@$_GET['iniciolimit']) ? $_GET['iniciolimit'] : "0";
		$limite = 10;
		if ($total > $limite)
		{
			$iniciolimit_ = $iniciolimit * $limite;
			$limit = " LIMIT {$iniciolimit_}, $limite";
		}


		$db->Consulta( "SELECT n.data_noticia, n.titulo, n.cod_not_portal FROM not_portal  n $where ORDER BY n.data_noticia DESC {$limit}" );
		while ($db->ProximoRegistro())
		{
			list ($data, $titulo, $id_noticia) = $db->Tupla();
			$data= date("d/m/Y", strtotime(substr($data,0,19)) );

			$this->addLinhas( array( "<img src='imagens/noticia.jpg' border=0>{$data}","<a href='noticias_det.php?id_noticia=$id_noticia'>$titulo</a>") );
		}

		$this->paginador("noticias_lst.php?", $total_tmp,$limite,@$_GET['pos_atual']);
		$this->acao = "go(\"noticias_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";

	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>