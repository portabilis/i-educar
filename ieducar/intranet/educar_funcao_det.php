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
		$this->SetTitulo( "{$this->_instituicao} i-Educar -  Fun&ccedil;&atilde;o Servidor" );
		$this->processoAp = "634";
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

	var $cod_funcao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_funcao;
	var $abreviatura;
	var $professor;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Funcao - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_funcao=$_GET["cod_funcao"];
		$this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];

		$tmp_obj = new clsPmieducarFuncao( $this->cod_funcao,null,null,null,null,null,null,null,null,$this->ref_cod_instituicao );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_funcao_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $registro["ref_cod_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
			}
		}
		if( $registro["cod_funcao"] )
		{
			$this->addDetalhe( array( "Func&atilde;o", "{$registro["cod_funcao"]}") );
		}
		if( $registro["nm_funcao"] )
		{
			$this->addDetalhe( array( "Nome Func&atilde;o", "{$registro["nm_funcao"]}") );
		}
		if( $registro["abreviatura"] )
		{
			$this->addDetalhe( array( "Abreviatura", "{$registro["abreviatura"]}") );
		}

		$opcoes = array('1' => 'Sim',
						'0' => 'N&atilde;o'
						);

		if( is_numeric($registro["professor"]) )
		{
			$this->addDetalhe( array( "Professor", "{$opcoes[$registro["professor"]]}") );
		}

		if( $obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "educar_funcao_cad.php";
			$this->url_editar = "educar_funcao_cad.php?cod_funcao={$registro["cod_funcao"]}";
		}

		$this->url_cancelar = "educar_funcao_lst.php";
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