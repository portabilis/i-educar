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
require_once 'include/localizacaoSistema.php';

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Diária Grupo" );
		$this->processoAp = "297";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Diária Grupo";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
	
		$this->addCabecalhos( array( "Grupo" ) );
		
		
		$where = "";
		$gruda = "";

		$db = new clsBanco();
		$db2 = new clsBanco();
		$total = $db->UnicoCampo( "SELECT count(0) FROM pmidrh.diaria_grupo $where" );
		
		// Paginador
		$limite = 20;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$objPessoa = new clsPessoaFisica();
		
		$sql = "SELECT cod_diaria_grupo, desc_grupo FROM pmidrh.diaria_grupo $where ORDER BY desc_grupo ASC";
		$db->Consulta( $sql );
		while ( $db->ProximoRegistro() )
		{
			list ( $cod_diaria_grupo, $desc_grupo ) = $db->Tupla();
		
			$this->addLinhas( array( 
			"<a href='diaria_grupo_det.php?cod_diaria_grupo={$cod_diaria_grupo}'><img src='imagens/noticia.jpg' border=0>$desc_grupo</a>"));
		}
		
		// Paginador
		$this->addPaginador2( "diaria_grupo_lst.php", $total, $_GET, $this->nome, $limite );
		
		$this->acao = "go(\"diaria_grupo_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    ""                  => "Diárias Grupo"
                ));
                $this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();

$pagina->addForm( $miolo );

$pagina->MakeAll();

?>