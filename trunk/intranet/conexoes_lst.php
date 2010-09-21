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
		$this->SetTitulo( "{$this->_instituicao} Conexões!" );
		$this->processoAp = "157";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Conexões";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$this->addCabecalhos( array( "Data Hora", "Local do Acesso") );
		
		// Paginador
		$limite = 20;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
				
		@session_start();
		$id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		
		$sql = "SELECT b.data_hora, b.ip_externo FROM acesso b WHERE cod_pessoa={$id_pessoa}";
		if (!empty($_GET['status']))
		{
			if ($_GET['status'] == 'P')
				$where .= " AND ip_externo = '200.215.80.163'";
			else if ($_GET['status'] == 'X')
				$where .= " AND ip_externo <> '200.215.80.163'";
		}
		if(!empty($_GET['data_inicial']))
		{
			$data = explode("/", $_GET['data_inicial']);
			$where .= " AND data_hora >= '{$data[2]}-{$data[1]}-{$data[0]}'";
		}
		
		if(!empty($_GET['data_final']))
		{
			$data = explode("/", $_GET['data_final']);
			$where .= " AND data_hora <= '{$data[2]}-{$data[1]}-{$data[0]}'";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo("SELECT count(*) FROM acesso WHERE cod_pessoa={$id_pessoa} $where");
				
		$sql .= " $where ORDER BY b.data_hora DESC LIMIT $iniciolimit, $limite";	
		
		$db->Consulta( $sql );
		while ( $db->ProximoRegistro() )
		{
			list ($data_hora, $ip_externo) = $db->Tupla();
			
			$local = $ip_externo == '200.215.80.163' ? 'Prefeitura' : 'Externo';

			$this->addLinhas( array("<img src='imagens/noticia.jpg' border=0>$data_hora", $local ) );
		}

		/*$this->acao = "go(\"bairros_cad.php\")";
		$this->nome_acao = "Novo";*/
		
		$opcoes[""] = "Escolha uma opção...";
		$opcoes["P"] = "Prefeitura";
		$opcoes["X"] = "Externo";
	
		$this->campoLista( "status", "Status", $opcoes, $_GET['status'] );
		
		$this->campoData("data_inicial","Data Inicial",$_GET['data_inicial']);
		$this->campoData("data_final","Data Final",$_GET['data_final']);
		
		$this->addPaginador2( "conexoes_lst.php", $total, $_GET, $this->nome, $limite );

		$this->largura = "100%";

	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>