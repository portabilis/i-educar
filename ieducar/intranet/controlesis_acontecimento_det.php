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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Acontecimento" );
		$this->processoAp = "605";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */

	var $cod_acontecimento;
	var $ref_cod_tipo_acontecimento;
	var $ref_cod_funcionario_cad;
	var $ref_cod_funcionario_exc;
	var $titulo;
	var $descricao;
	var $dt_inicio;
	var $dt_fim;
	var $hr_inicio;
	var $hr_fim;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Acontecimento - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_acontecimento=$_GET["cod_acontecimento"];

		$tmp_obj = new clsPmicontrolesisAcontecimento( $this->cod_acontecimento );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: controlesis_acontecimento_lst.php" );
			die();
		}

		if( class_exists( "clsPmicontrolesisTipoAcontecimento" ) )
		{
			$obj_ref_cod_tipo_acontecimento = new clsPmicontrolesisTipoAcontecimento( $registro["ref_cod_tipo_acontecimento"] );
			$det_ref_cod_tipo_acontecimento = $obj_ref_cod_tipo_acontecimento->detalhe();
			$registro["ref_cod_tipo_acontecimento"] = $det_ref_cod_tipo_acontecimento["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_tipo_acontecimento"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisTipoAcontecimento\n-->";
		}

		if( $registro["ref_cod_tipo_acontecimento"] )
		{
			$this->addDetalhe( array( "Tipo Acontecimento", "{$registro["ref_cod_tipo_acontecimento"]}") );
		}

		if( $registro["titulo"] )
		{
			$this->addDetalhe( array( "Titulo", "{$registro["titulo"]}") );
		}
		if( $registro["descricao"] )
		{
			$this->addDetalhe( array( "Descric&atilde;o", "<div style='text-align:justify'>{$registro["descricao"]}</div>") );
		}
		if( $registro["local"] )
		{
			$this->addDetalhe( array( "Local", "{$registro["local"]}") );
		}
		if( $registro["contato"] )
		{
			$this->addDetalhe( array( "Contato", "{$registro["contato"]}") );
		}
		if( $registro["link"] )
		{
			$this->addDetalhe( array( "Link", "{$registro["link"]}") );
		}
		if( $registro["dt_inicio"] )
		{
			$this->addDetalhe( array( "Dt Inicio", substr(dataFromPgToBr( $registro["dt_inicio"], "d/m/Y H:i" ), 0, 10) ) );
		}
		if( $registro["dt_fim"] )
		{
			$this->addDetalhe( array( "Dt Fim", substr(dataFromPgToBr( $registro["dt_fim"], "d/m/Y H:i" ), 0, 10) ) );
		}
		if( $registro["hr_inicio"] )
		{
			$this->addDetalhe( array( "Hr Inicio", substr($registro["hr_inicio"], 0, 5) ) );
		}
		if( $registro["hr_fim"] )
		{
			$this->addDetalhe( array( "Hr Fim", substr($registro["hr_fim"], 0, 5)) );
		}
		
		$db = new clsBanco();
		$db->Consulta( "SELECT ref_cod_foto_evento FROM pmicontrolesis.foto_vinc n WHERE ref_cod_acontecimento={$this->cod_acontecimento}" );

		while($db->ProximoRegistro())
		{
			list($cod) = $db->Tupla();
			$dba = new clsBanco();
			$dba->Consulta( "SELECT titulo, caminho, altura, largura FROM pmicontrolesis.foto_evento WHERE cod_foto_evento={$cod}" );
			$dba->ProximoRegistro();
			list ($titulo,$caminho,$altura,$largura) = $dba->Tupla();
			$this->addDetalhe( array("Fotos Vinculadas", "<a href='#' onclick='javascript:openfoto(\"$titulo\",\"$caminho\",$altura,$largura)'><img src='fotos/small/{$caminho}' border='0'></a>") );
		}

		$this->url_novo = "controlesis_acontecimento_cad.php";
		$this->url_editar = "controlesis_acontecimento_cad.php?cod_acontecimento={$registro["cod_acontecimento"]}";
		$this->url_cancelar = "controlesis_acontecimento_lst.php";
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