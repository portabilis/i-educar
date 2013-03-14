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
require_once( "include/public/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Uf" );
		$this->processoAp = "754";
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

	var $sigla_uf;
	var $nome;
	var $geom;
	var $idpais;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Uf - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->sigla_uf=$_GET["sigla_uf"];

		$tmp_obj = new clsPublicUf( $this->sigla_uf );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: public_uf_lst.php" );
			die();
		}

		if( class_exists( "clsPais" ) )
		{
			$obj_idpais = new clsPais( $registro["idpais"] );
			$det_idpais = $obj_idpais->detalhe();
			$registro["idpais"] = $det_idpais["nome"];
		}
		else
		{
			$registro["idpais"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPais\n-->";
		}


		if( $registro["sigla_uf"] )
		{
			$this->addDetalhe( array( "Sigla Uf", "{$registro["sigla_uf"]}") );
		}
		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
		}
		if( $registro["geom"] )
		{
			$this->addDetalhe( array( "Geom", "{$registro["geom"]}") );
		}
		if( $registro["idpais"] )
		{
			$this->addDetalhe( array( "Pais", "{$registro["idpais"]}") );
		}


		$this->url_novo = "public_uf_cad.php";
		$this->url_editar = "public_uf_cad.php?sigla_uf={$registro["sigla_uf"]}";

		$this->url_cancelar = "public_uf_lst.php";
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