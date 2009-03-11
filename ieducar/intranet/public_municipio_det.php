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
		$this->SetTitulo( "{$this->_instituicao} Munic&iacute;pio" );
		$this->processoAp = "755";
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
	
	var $idmun;
	var $nome;
	var $sigla_uf;
	var $area_km2;
	var $idmreg;
	var $idasmun;
	var $cod_ibge;
	var $geom;
	var $tipo;
	var $idmun_pai;
	var $idpes_rev;
	var $idpes_cad;
	var $data_rev;
	var $data_cad;
	var $origem_gravacao;
	var $operacao;
	var $idsis_rev;
	var $idsis_cad;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Munic&iacute;pio - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->idmun=$_GET["idmun"];

		$tmp_obj = new clsPublicMunicipio( $this->idmun );
		$registro = $tmp_obj->detalhe();
		
		if( ! $registro )
		{
			header( "location: public_municipio_lst.php" );
			die();
		}
		
		if( class_exists( "clsPais" ) )
		{
			$obj_uf = new clsUf( $registro["sigla_uf"] );
			$det_uf = $obj_uf->detalhe();
			
			$obj_pais = new clsPais( $det_uf['idpais']->idpais );
			$det_pais = $obj_pais->detalhe();
			$registro["idpais"] = $det_pais["nome"];
		}
		else
		{
			$registro["idpais"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPais\n-->";
		}

		if( class_exists( "clsUf" ) )
		{
			$obj_sigla_uf = new clsUf( $registro["sigla_uf"] );
			$det_sigla_uf = $obj_sigla_uf->detalhe();
			$registro["sigla_uf"] = $det_sigla_uf["nome"];
		}
		else
		{
			$registro["sigla_uf"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsUf\n-->";
		}

		if( class_exists( "clsMunicipio" ) )
		{
			$obj_idmun_pai = new clsMunicipio( $registro["idmun_pai"] );
			$det_idmun_pai = $obj_idmun_pai->detalhe();
			$registro["idmun_pai"] = $det_idmun_pai["nome"];
		}
		else
		{
			$registro["idmun_pai"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsMunicipio\n-->";
		}

		
		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
		}
		if( $registro["sigla_uf"] )
		{
			$this->addDetalhe( array( "Estado", "{$registro["sigla_uf"]}") );
		}
		if( $registro["idpais"] )
		{
			$this->addDetalhe( array( "Pais", "{$registro["idpais"]}") );
		}
		if( $registro["area_km2"] )
		{
			$this->addDetalhe( array( "Area Km2", "{$registro["area_km2"]}") );
		}
		if( $registro["tipo"] )
		{
			$this->addDetalhe( array( "Tipo", "{$registro["tipo"]}") );
		}
		if( $registro["idmun_pai"] )
		{
			$this->addDetalhe( array( "Idmun Pai", "{$registro["idmun_pai"]}") );
		}		
		if( $registro["origem_gravacao"] )
		{
			$this->addDetalhe( array( "Origem Grava&ccedil;&atilde;o", "{$registro["origem_gravacao"]}") );
		}
		if( $registro["operacao"] )
		{
			$this->addDetalhe( array( "Operac&atilde;o", "{$registro["operacao"]}") );
		}
/*		if( $registro["idsis_cad"] )
		{
			$this->addDetalhe( array( "Idsis Cad", "{$registro["idsis_cad"]}") );
		}*/


		$this->url_novo = "public_municipio_cad.php";
		$this->url_editar = "public_municipio_cad.php?idmun={$registro["idmun"]}";

		$this->url_cancelar = "public_municipio_lst.php";
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