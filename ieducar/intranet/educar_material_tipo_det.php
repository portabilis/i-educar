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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Material Did&aacute;tico" );
		$this->processoAp = "563";
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

	var $cod_material_tipo;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $nm_tipo;
	var $desc_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;


	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Tipo Material Did&aacute;tico - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_material_tipo=$_GET["cod_material_tipo"];

		$tmp_obj = new clsPmieducarMaterialTipo( $this->cod_material_tipo );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_material_tipo_lst.php" );
			die();
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
		if( $registro["cod_material_tipo"] )
		{
			$this->addDetalhe( array( "C&oacute;digo Tipo Material Did&aacute;tico", "{$registro["cod_material_tipo"]}") );
		}
		if( $registro["nm_tipo"] )
		{
			$this->addDetalhe( array( "Material Did&aacute;tico", "{$registro["nm_tipo"]}") );
		}
		if( $registro["desc_tipo"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["desc_tipo"]}") );
		}

		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();

		if($obj_permissao->permissao_cadastra(563, $this->pessoa_logada,3))
		{
			if( $registro["ativo"] == 1)
			{
				$this->url_novo = "educar_material_tipo_cad.php";
				$this->url_editar = "educar_material_tipo_cad.php?cod_material_tipo={$registro["cod_material_tipo"]}";
				$this->url_cancelar = "educar_material_tipo_lst.php";
			}
			else
			{
			/*	if($obj_permissao->permissao_excluir(563,$this->pessoa_logada,7))
				{

					$this->array_botao_url_script[] = "if(confirm(\"Deseja ativar o material \\\"{$registro["nm_tipo"]}\\\"?\")){go(\"educar_material_tipo_cad.php?cod_material_tipo={$registro["cod_material_tipo"]}&ativar=true\");}";
					$this->array_botao[] = "Ativar";

					$this->array_botao_url_script[] = "go(\"educar_material_tipo_lst.php?ativo=excluido\")";
					$this->array_botao[] = "Cancelar";
				}
				else
				{
					header( "location: educar_material_tipo_lst.php" );
					die();
				}*/
			}

		}
		else
		{
			$this->array_botao_url_script[] = "go(\"educar_material_tipo_lst.php?ativo=excluido\")";
			$this->array_botao[] = "Cancelar";
		}
		//**


		//$this->url_cancelar = "educar_material_tipo_lst.php";
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