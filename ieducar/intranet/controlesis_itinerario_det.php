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
		$this->SetTitulo( "{$this->_instituicao} Itinerario" );
		$this->processoAp = "614";
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

	var $cod_itinerario;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $numero;
	var $itinerario;
	var $retorno;
	var $horarios;
	var $descricao_horario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nome;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Itinerario - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_itinerario=$_GET["cod_itinerario"];

		$tmp_obj = new clsPmicontrolesisItinerario( $this->cod_itinerario );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: controlesis_itinerario_lst.php" );
			die();
		}




		if( $registro["cod_itinerario"] )
		{
			$this->addDetalhe( array( "Itinerario", "{$registro["cod_itinerario"]}") );
		}
		if( $registro["numero"] )
		{
			$this->addDetalhe( array( "Numero", "{$registro["numero"]}") );
		}
		if( $registro["itinerario"] )
		{
			$this->addDetalhe( array( "Itinerario", "{$registro["itinerario"]}") );
		}
		if( $registro["retorno"] )
		{
			$this->addDetalhe( array( "Retorno", "{$registro["retorno"]}") );
		}
		if( $registro["horarios"] )
		{
			$this->addDetalhe( array( "Horarios", "{$registro["horarios"]}") );
		}
		if( $registro["descricao_horario"] )
		{
			$this->addDetalhe( array( "Descric&atilde;o Horario", "{$registro["descricao_horario"]}") );
		}
		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
		}

		$this->url_novo = "controlesis_itinerario_cad.php";
		$this->url_editar = "controlesis_itinerario_cad.php?cod_itinerario={$registro["cod_itinerario"]}";
		$this->url_cancelar = "controlesis_itinerario_lst.php";
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