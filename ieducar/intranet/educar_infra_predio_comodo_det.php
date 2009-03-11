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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - C&ocirc;modo Pr&eacute;dio" );
		$this->processoAp = "574";
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

	var $cod_infra_predio_comodo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_infra_comodo_funcao;
	var $ref_cod_infra_predio;
	var $nm_comodo;
	var $desc_comodo;
	var $area;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Infra Predio Comodo - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_infra_predio_comodo=$_GET["cod_infra_predio_comodo"];

		$tmp_obj = new clsPmieducarInfraPredioComodo( $this->cod_infra_predio_comodo );
		$lst = $tmp_obj->lista($this->cod_infra_predio_comodo);
		if (is_array($lst))
		{
			$registro = array_shift($lst);
		}


		if( ! $registro )
		{
			header( "location: educar_infra_predio_comodo_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarInfraComodoFuncao" ) )
		{
			$obj_ref_cod_infra_comodo_funcao = new clsPmieducarInfraComodoFuncao( $registro["ref_cod_infra_comodo_funcao"] );
			$det_ref_cod_infra_comodo_funcao = $obj_ref_cod_infra_comodo_funcao->detalhe();
			$registro["ref_cod_infra_comodo_funcao"] = $det_ref_cod_infra_comodo_funcao["nm_funcao"];
		}
		else
		{
			$registro["ref_cod_infra_comodo_funcao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInfraComodoFuncao\n-->";
		}
		if( class_exists( "clsPmieducarInfraPredio" ) )
		{
			$obj_ref_cod_infra_predio = new clsPmieducarInfraPredio( $registro["ref_cod_infra_predio"] );
			$det_ref_cod_infra_predio = $obj_ref_cod_infra_predio->detalhe();
			$registro["ref_cod_infra_predio"] = $det_ref_cod_infra_predio["nm_predio"];
		}
		else
		{
			$registro["ref_cod_infra_predio"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInfraPredio\n-->";
		}
		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}
		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$nm_escola = $det_ref_cod_escola["nome"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}

		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $registro["ref_cod_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
			}
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $nm_escola )
			{
				$this->addDetalhe( array( "Escola", "{$nm_escola}") );
			}
		}
		if( $registro["ref_cod_infra_predio"] )
		{
			$this->addDetalhe( array( "Pr&eacute;dio", "{$registro["ref_cod_infra_predio"]}") );
		}
		if( $registro["nm_comodo"] )
		{
			$this->addDetalhe( array( "C&ocirc;modo", "{$registro["nm_comodo"]}") );
		}
		if( $registro["ref_cod_infra_comodo_funcao"] )
		{
			$this->addDetalhe( array( "Func&atilde;o C&ocirc;modo", "{$registro["ref_cod_infra_comodo_funcao"]}") );
		}
		if( $registro["area"] )
		{
			$this->addDetalhe( array( "&Aacute;rea", "{$registro["area"]}") );
		}
		if( $registro["desc_comodo"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o C&ocirc;modo", "{$registro["desc_comodo"]}") );
		}


		$this->url_novo = "educar_infra_predio_comodo_cad.php";
		$this->url_editar = "educar_infra_predio_comodo_cad.php?cod_infra_predio_comodo={$registro["cod_infra_predio_comodo"]}";
		$this->url_cancelar = "educar_infra_predio_comodo_lst.php";
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