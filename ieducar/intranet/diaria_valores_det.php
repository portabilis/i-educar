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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Diaria Valores" );
		$this->processoAp = "295";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe do valor";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_diaria_valores = @$_GET['cod_diaria_valores'];
		
		$db = new clsBanco();
		$db2 = new clsBanco();
		
		$db->Consulta( "SELECT cod_diaria_valores, ref_funcionario_cadastro, ref_cod_diaria_grupo, estadual, p100, p75, p50, p25, data_vigencia FROM pmidrh.diaria_valores WHERE cod_diaria_valores='{$cod_diaria_valores}'" );
		if( $db->ProximoRegistro() )
		{
			list( $cod_diaria_valores, $ref_funcionario_cadastro, $ref_cod_diaria_grupo, $estadual, $p100, $p75, $p50, $p25, $data_vigencia ) = $db->Tupla();
			
			$objPessoa = new clsPessoa_( $ref_funcionario_cadastro );
			$detalhePessoa = $objPessoa->detalhe();
			$this->addDetalhe( array( "Ultimo Editor", $detalhePessoa["nome"] ) );
			
			$nome_grupo = $db2->CampoUnico( "SELECT desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = '{$ref_cod_diaria_grupo}'" );
			$this->addDetalhe( array( "Grupo", $nome_grupo ) );
			
			$estadual = ( $estadual ) ? "Sim": "N&atilde;e";
			$this->addDetalhe( array( "Estadual", $estadual ) );
			
			$p100 = number_format( $p100, 2, ",", "." );
			$this->addDetalhe( array( "100%", $p100 ) );
			
			$p75 = number_format( $p75, 2, ",", "." );
			$this->addDetalhe( array( "75%", $p75 ) );
			
			$p50 = number_format( $p50, 2, ",", "." );
			$this->addDetalhe( array( "50%", $p50 ) );
			
			$p25 = number_format( $p25, 2, ",", "." );
			$this->addDetalhe( array( "25%", $p25 ) );
			
			$data_vigencia = date( "d/m/Y", strtotime( $data_vigencia ) );
			$this->addDetalhe( array( "Data de vig&ecirc;ncia", $data_vigencia ) );
			
			$this->url_editar = "diaria_valores_cad.php?cod_diaria_valores={$cod_diaria_valores}";
		}
		else 
		{
			$this->addDetalhe( array( "Erro", "Codigo de diaria-valor invalido" ) );
		}
		
		$this->url_novo = "diaria_valores_cad.php";
		$this->url_cancelar = "diaria_valores_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>