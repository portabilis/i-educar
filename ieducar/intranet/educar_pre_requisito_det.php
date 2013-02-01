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
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Pre Requisito" );
		$this->processoAp = "601";
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

	var $cod_pre_requisito;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $schema_;
	var $tabela;
	var $nome;
	var $sql;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Pre Requisito - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_pre_requisito=$_GET["cod_pre_requisito"];

		$tmp_obj = new clsPmieducarPreRequisito( $this->cod_pre_requisito );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_pre_requisito_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarUsuario" ) )
		{
			$obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
			$det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
			$registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];
		}
		else
		{
			$registro["ref_usuario_exc"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
		}

		if( class_exists( "clsPmieducarUsuario" ) )
		{
			$obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
			$det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
			$registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];
		}
		else
		{
			$registro["ref_usuario_cad"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
		}


		if( $registro["cod_pre_requisito"] )
		{
			$this->addDetalhe( array( "Pre Requisito", "{$registro["cod_pre_requisito"]}") );
		}
		if( $registro["schema_"] )
		{
			$this->addDetalhe( array( "Schema ", "{$registro["schema_"]}") );
		}
		if( $registro["tabela"] )
		{
			$this->addDetalhe( array( "Tabela", "{$registro["tabela"]}") );
		}
		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
		}
		if( $registro["sql"] )
		{
			$this->addDetalhe( array( "Sql", "{$registro["sql"]}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3, null, true ) )
		{
		$this->url_novo = "educar_pre_requisito_cad.php";
		$this->url_editar = "educar_pre_requisito_cad.php?cod_pre_requisito={$registro["cod_pre_requisito"]}";
		}

		$this->url_cancelar = "educar_pre_requisito_lst.php";
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