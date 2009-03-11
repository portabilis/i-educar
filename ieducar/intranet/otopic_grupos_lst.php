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
require_once ("include/otopic/otopicGeral.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Grupos!" );
		$this->processoAp = "296";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Grupos";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array( "Grupo", "Data de crica&ccedil;&atilde;o", "N&uacute;mero de Membros") );

		$this->campoTexto( "nm_grupo", "Grupo",  $_GET['nm_grupo'], "50", "255", true );

		$nm_grupo = ($_GET['nm_grupo']) ? $_GET['nm_grupo'] : false;
		//$nm_grupo = ($_GET['nm_grupo']) ? $_GET['nm_grupo'] : false;

		// Paginador
		$limite = 20;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		$objGrupos = new clsGrupos();
		$listaGrupos = $objGrupos->lista($nm_grupo);

		if($listaGrupos)
		{
			foreach ($listaGrupos as $grupo)
			{
				$total = $grupo['total'];
				$totalPessoas = 0;

				$data_cadastro = $grupo['data_cadastro'];
				$data_cadastro = date( "d/m/Y", strtotime( substr( $data_cadastro, 0, 16 ) ) );

				$cod_grupo = $grupo['cod_grupos'];
				$nm_grupo = $grupo['nm_grupo'];

				$objGrupoPessoas = new clsGrupoPessoa();
				$listaGrupoPessoas = $objGrupoPessoas->lista(false, $cod_grupo);

				$objGrupoModerador = new clsGrupoModerador();
				$listaGrupoModerador = $objGrupoModerador->lista(false, $cod_grupo);

				$totalPessoas += (!empty($listaGrupoPessoas)) ? count($listaGrupoPessoas) : $totalPessoas;
				$totalPessoas += (!empty($listaGrupoModerador)) ? count($listaGrupoModerador) : $totalPessoas;

				$this->addLinhas( array("<img src='imagens/noticia.jpg' border=0><a href='otopic_grupos_det.php?cod_grupos={$cod_grupo}'>$nm_grupo</a>", $data_cadastro, $totalPessoas) );
			}
		}
		$this->acao = "go(\"otopic_grupos_cad.php\")";
		$this->nome_acao = "Novo";
		$this->largura = "100%";
		$this->addPaginador2( "otopic_grupos_lst.php", $total, $_GET, $this->nome, $limite );
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>