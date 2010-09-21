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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Material Did&aacute;tico" );
		$this->processoAp = "569";
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

	var $cod_material_didatico;
	var $ref_cod_escola;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_material_tipo;
	var $nm_material;
	var $desc_material;
	var $custo_unitario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Material Did&aacute;tico - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_material_didatico=$_GET["cod_material_didatico"];

		$tmp_obj = new clsPmieducarMaterialDidatico( $this->cod_material_didatico );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_material_didatico_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarMaterialTipo" ) )
		{
			$obj_ref_cod_material_tipo = new clsPmieducarMaterialTipo( $registro["ref_cod_material_tipo"] );
			$det_ref_cod_material_tipo = $obj_ref_cod_material_tipo->detalhe();
			$registro["ref_cod_material_tipo"] = $det_ref_cod_material_tipo["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_material_tipo"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarMaterialTipo\n-->";
		}
		if (class_exists("clsPmieducarInstituicao"))
		{
			$obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
			$obj_instituicao_det = $obj_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $obj_instituicao_det['nm_instituicao'];
		}
		else
		{
			$cod_instituicao = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if ($registro["ref_cod_instituicao"])
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}" ) );
			}
		}
		if( $registro["ref_cod_material_tipo"] )
		{
			$this->addDetalhe( array( "Tipo de Material", "{$registro["ref_cod_material_tipo"]}") );
		}
		if( $registro["nm_material"] )
		{
			$this->addDetalhe( array( "Material", "{$registro["nm_material"]}") );
		}
		if( $registro["desc_material"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["desc_material"]}") );
		}
		if( $registro["custo_unitario"] )
		{
			$valor = number_format($registro["custo_unitario"],2,",",".");
			$this->addDetalhe( array( "Custo Unit&aacute;rio", "{$valor}") );
		}

		if( $obj_permissoes->permissao_cadastra( 569, $this->pessoa_logada,3 ) ) {
			$this->url_novo = "educar_material_didatico_cad.php";
			$this->url_editar = "educar_material_didatico_cad.php?cod_material_didatico={$registro["cod_material_didatico"]}";
		}
		$this->url_cancelar = "educar_material_didatico_lst.php";
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