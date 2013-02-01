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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Ensino" );
		$this->processoAp = "558";
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

	var $cod_tipo_ensino;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Tipo Ensino - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_tipo_ensino=$_GET["cod_tipo_ensino"];

		$tmp_obj = new clsPmieducarTipoEnsino( $this->cod_tipo_ensino,null,null,null,null,null,1);
		if( !$registro = $tmp_obj->detalhe())
			header("Location: educar_tipo_ensino_lst.php");

		if(!$registro["ativo"] )
			header("Location: educar_tipo_ensino_lst.php");

		if( $registro["cod_tipo_ensino"] )
		{
			$this->addDetalhe( array( "Tipo Ensino", "{$registro["cod_tipo_ensino"]}") );
		}

		if( $registro["ref_cod_instituicao"] )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
				$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
			}
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $registro["nm_tipo"] )
		{
			$this->addDetalhe( array( "Nome Tipo", "{$registro["nm_tipo"]}") );
		}

		//** Verificacao de permissao para cadastro ou edicao
		$obj_permissao = new clsPermissoes();

		if($obj_permissao->permissao_cadastra(558, $this->pessoa_logada,7))
		{
			$this->url_novo = "educar_tipo_ensino_cad.php";
			$this->url_editar = "educar_tipo_ensino_cad.php?cod_tipo_ensino={$registro["cod_tipo_ensino"]}";
		}
		//**


		$this->url_cancelar = "educar_tipo_ensino_lst.php";
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