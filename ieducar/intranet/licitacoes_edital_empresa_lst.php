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
		$this->SetTitulo( "{$this->_instituicao} Empresas" );
		$this->processoAp = "239";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$db = new clsBanco();
		$db2 = new clsBanco();
		$this->titulo = "Editais - Empresas";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$this->campoTexto( "busca_nome", "Empresa", $_GET["busca_nome"], 50, 255 );
		$where = "";
		if( isset( $_GET["busca_nome"] ) )
		{
			$where = " WHERE nm_empresa LIKE '%{$_GET["busca_nome"]}%'";
		}
		
		
		$this->addCabecalhos( array( "Empresa", "CNPJ ou CPF", "e-mail" ) );
		
		$total = $db->CampoUnico( "SELECT count(0) FROM compras_editais_empresa $where" );
		$limite = 20;
		$inicio_limite = @$_GET["pos_atual"] * $limite;
		$limit = " LIMIT $inicio_limite, $limite";
		
		$db->Consulta( "SELECT cod_compras_editais_empresa, cnpj, nm_empresa, email, data_hora FROM compras_editais_empresa $where ORDER BY nm_empresa ASC $limit" );
		while ( $db->ProximoRegistro() )
		{
			list ( $cod_compras_editais_empresa, $cnpj, $nm_empresa, $email, $data_hora ) = $db->Tupla();
			$this->addLinhas( array( "<a href='licitacoes_edital_empresa_det.php?cod_empresa=$cod_compras_editais_empresa'><img src='imagens/noticia.jpg' border=0>$nm_empresa</a>", "<a href='licitacoes_edital_empresa_det.php?cod_empresa=$cod_compras_editais_empresa'>$cnpj</a>", $email ) );
		}
		$this->paginador( "licitacoes_edital_empresa_lst.php?", $total, $limite, @$_GET['pos_atual'] );

		$this->acao = "go(\"licitacoes_edital_empresa_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>