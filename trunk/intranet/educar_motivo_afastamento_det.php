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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Afastamento" );
		$this->processoAp = "633";
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

	var $cod_motivo_afastamento;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_motivo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	//var $ref_cod_escola;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Motivo Afastamento - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_motivo_afastamento=$_GET["cod_motivo_afastamento"];

		$tmp_obj = new clsPmieducarMotivoAfastamento( $this->cod_motivo_afastamento );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_motivo_afastamento_lst.php" );
			die();
		}
	/*	if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$nm_escola = $det_ref_cod_escola["nome"];
			$ref_cod_instituicao = $det_ref_cod_escola["ref_cod_instituicao"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}*/
		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro['ref_cod_instituicao'] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}

		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $det_ref_cod_instituicao["nm_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$det_ref_cod_instituicao["nm_instituicao"]}") );
			}
		}
	/*	if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $nm_escola )
			{
				$this->addDetalhe( array( "Escola", "{$nm_escola}") );
			}
		}*/
		if( $registro["nm_motivo"] )
		{
			$this->addDetalhe( array( "Motivo de Afastamento", "{$registro["nm_motivo"]}") );
		}
		if( $registro["descricao"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 633, $this->pessoa_logada, 7 ) )
		{
			$this->url_novo = "educar_motivo_afastamento_cad.php";
			$this->url_editar = "educar_motivo_afastamento_cad.php?cod_motivo_afastamento={$registro["cod_motivo_afastamento"]}";
		}

		$this->url_cancelar = "educar_motivo_afastamento_lst.php";
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