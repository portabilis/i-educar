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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Usu&aacute;rio" );
		$this->processoAp = "555";
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

	var $cod_usuario;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_tipo_usuario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->titulo = "Usu&aacute;rio - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_usuario=$_GET["cod_usuario"];

		$tmp_obj = new clsPmieducarUsuario( $this->cod_usuario );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_usuario_lst.php" );
			die();
		}

		if( class_exists( "clsPessoa_" ) )
		{
			$obj_cod_usuario = new clsPessoa_($registro["cod_usuario"] );
			$obj_usuario_det = $obj_cod_usuario->detalhe();
			$nm_usuario = $obj_usuario_det['nome'];
		}
		else
		{
			$registro["cod_usuario"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsFuncionario\n-->";
		}
		if( class_exists( "clsPmieducarTipoUsuario" ) )
		{
			$obj_ref_cod_tipo_usuario = new clsPmieducarTipoUsuario( $registro["ref_cod_tipo_usuario"] );
			$det_ref_cod_tipo_usuario = $obj_ref_cod_tipo_usuario->detalhe();
			$registro["ref_cod_tipo_usuario"] = $det_ref_cod_tipo_usuario["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_tipo_usuario"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarTipoUsuario\n-->";
		}
		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}
		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
			$obj_cod_escola_det = $obj_cod_escola->detalhe();
			$id_pessoa = $obj_cod_escola_det["nome"];
			//$obj_cod_escola = new clsJuridica($id_pessoa);
		 	//$registro["ref_cod_escola"] = $obj_cod_escola_det["fantasia"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}

		if( $nm_usuario )
		{
			$this->addDetalhe( array( "Usu&aacute;rio", "{$nm_usuario}") );
		}
		if( $registro["ref_cod_tipo_usuario"] )
		{
			$this->addDetalhe( array( "Tipo Usu&aacute;rio", "{$registro["ref_cod_tipo_usuario"]}") );
		}
		if( $registro["ref_cod_instituicao"] )
		{
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $registro["ref_cod_escola"] )
		{
			$this->addDetalhe( array( "Escola", "$id_pessoa") );
		}

		$objPermissao = new clsPermissoes();
		if( $objPermissao->permissao_cadastra( 555, $this->pessoa_logada,7,"educar_usuario_lst.php",true ) )
		{
			$this->url_novo = "educar_usuario_cad.php";
			$this->url_editar = "educar_usuario_cad.php?cod_usuario={$registro["cod_usuario"]}";
		}
		$this->url_cancelar = "educar_usuario_lst.php";
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