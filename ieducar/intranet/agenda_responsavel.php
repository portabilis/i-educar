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
		$this->SetTitulo( "{$this->_instituicao} Agenda" );
		$this->processoAp = "341";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	var $cd_agenda;
	var $nm_agenda;

	function Gerar()
	{

		@session_start();
		$this->pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Agendas que eu posso editar";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
	
		$this->addCabecalhos( array( "Agenda" ) );

		$this->campoTexto('pesquisa', 'Agenda', '', 50, 255);
				
		$db = new clsBanco();
		
		$and = "";
		
		if (!empty($_GET['pesquisa']))
		{
			$pesquisa = str_replace(' ', '%', $_GET['pesquisa']);
			$and = "AND nm_agenda ilike ('%{$pesquisa}%')";
			$pesquisa = str_replace('%', ' ', $_GET['pesquisa']);
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) + (SELECT COUNT(*) FROM portal.agenda_responsavel WHERE ref_ref_cod_pessoa_fj = {$this->pessoa} ) FROM portal.agenda WHERE ref_ref_cod_pessoa_own = {$this->pessoa} {$and} " );
		
		// Paginador
		$limite = 15;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$sql = "SELECT cod_agenda, 1 AS minha FROM agenda WHERE ref_ref_cod_pessoa_own = {$this->pessoa} {$and} UNION SELECT ref_cod_agenda, 0 AS minha FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = {$this->pessoa} ORDER BY minha DESC";
		
		$db1 = new clsBanco();
		$db1->Consulta( $sql );
		while ( $db1->ProximoRegistro() )
		{
			list ( $cd_agenda, $propriedade ) = $db1->Tupla();
			
			$db2 = new clsBanco();
			$db2->Consulta( "SELECT nm_agenda, ref_ref_cod_pessoa_own FROM agenda WHERE cod_agenda = {$cd_agenda} {$and}" );
			while ( $db2->ProximoRegistro() )
			{
				list ( $nm_agenda, $cod_pessoa_own ) = $db2->Tupla();
				$this->addLinhas( array( 
				"<a href='agenda.php?cod_agenda={$cd_agenda}'><img src='imagens/noticia.jpg' border=0>$nm_agenda</a>"));
			}
		}
		
		// Paginador
		$this->addPaginador2( "agenda_responsavel.php", $total, $_GET, $this->nome, $limite );
		
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    ""                  => "Agendas"
                ));
                $this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>