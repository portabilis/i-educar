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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/portal/clsPortalAcesso.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Acesso" );
		$this->processoAp = "666";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_acesso;
	var $data_hora;
	var $ip_externo;
	var $ip_interno;
	var $cod_pessoa;
	var $obs;
	var $sucesso;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Acesso - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_acesso=$_GET["cod_acesso"];

		$tmp_obj = new clsPortalAcesso( $this->cod_acesso );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: portal_acesso_lst.php" );
			die();
		}


		if( $registro["cod_acesso"] )
		{
			$this->addDetalhe( array( "Acesso", "{$registro["cod_acesso"]}") );
		}
		if( $registro["data_hora"] )
		{
			$this->addDetalhe( array( "Data Hora", dataFromPgToBr( $registro["data_hora"], "d/m/Y H:i" ) ) );
		}
		if( $registro["ip_externo"] )
		{
			$this->addDetalhe( array( "Ip Externo", "{$registro["ip_externo"]}") );
		}
		if( $registro["ip_interno"] )
		{
			$this->addDetalhe( array( "Ip Interno", "{$registro["ip_interno"]}") );
		}
		if( $registro["cod_pessoa"] )
		{
			$this->addDetalhe( array( "Pessoa", "{$registro["cod_pessoa"]}") );
		}
		if( $registro["obs"] )
		{
			$this->addDetalhe( array( "Obs", "{$registro["obs"]}") );
		}
		if( ! is_null( $registro["sucesso"] ) )
		{
			$this->addDetalhe( array( "Sucesso", dbBool( $registro["sucesso"] ) ? "Sim": "Não" ) );
		}


		$this->url_novo = "portal_acesso_cad.php";
		$this->url_editar = "portal_acesso_cad.php?cod_acesso={$registro["cod_acesso"]}";

		$this->url_cancelar = "portal_acesso_lst.php";
		$this->largura = "100%";
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>