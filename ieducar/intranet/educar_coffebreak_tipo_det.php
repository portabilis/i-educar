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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Coffebreak Tipo" );
		$this->processoAp = "564";
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
	
	var $cod_coffebreak_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $desc_tipo;
	var $custo_unitario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Coffebreak Tipo - Detalhe";
		

		$this->cod_coffebreak_tipo=$_GET["cod_coffebreak_tipo"];

		$tmp_obj = new clsPmieducarCoffebreakTipo( $this->cod_coffebreak_tipo );
		$registro = $tmp_obj->detalhe();
		
		if( ! $registro || !$registro["ativo"] )
		{
			header( "location: educar_coffebreak_tipo_lst.php" );
			die();
		}


		if( $registro["cod_coffebreak_tipo"] )
		{
			$this->addDetalhe( array( "Coffebreak Tipo", "{$registro["cod_coffebreak_tipo"]}") );
		}
		if( $registro["nm_tipo"] )
		{
			$this->addDetalhe( array( "Nome Tipo", "{$registro["nm_tipo"]}") );
		}
		if( $registro["desc_tipo"] )
		{
			$this->addDetalhe( array( "Desc Tipo", "{$registro["desc_tipo"]}") );
		}
		if( $registro["custo_unitario"] )
		{
			$this->addDetalhe( array( "Custo Unitario", str_replace(".",",",$registro["custo_unitario"])) );
		}

		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();
		
		if($obj_permissao->permissao_cadastra(554, $this->pessoa_logada,7))
		{	
			$this->url_novo = "educar_coffebreak_tipo_cad.php";
			$this->url_editar = "educar_coffebreak_tipo_cad.php?cod_coffebreak_tipo={$registro["cod_coffebreak_tipo"]}";
		}
		//**		

		$this->url_cancelar = "educar_coffebreak_tipo_lst.php";
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