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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Disciplina" );
		$this->processoAp = "557";
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

	var $cod_disciplina;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $desc_disciplina;
	var $desc_resumida;
	var $abreviatura;
	var $carga_horaria;
	var $apura_falta;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_disciplina;
	var $ref_cod_curso;

	function Gerar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->titulo = "Disciplina - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_disciplina=$_GET["cod_disciplina"];

		$tmp_obj = new clsPmieducarDisciplina( $this->cod_disciplina );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_disciplina_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];

			if (class_exists("clsPmieducarInstituicao"))
			{
				$registro["ref_cod_instituicao"] = $det_ref_cod_curso["ref_cod_instituicao"];
				$obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
				$obj_instituicao_det = $obj_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $obj_instituicao_det["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
			}
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
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
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $registro["nm_disciplina"] )
		{
			$this->addDetalhe( array( "Disciplina", "{$registro["nm_disciplina"]}") );
		}
		if( $registro["desc_disciplina"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o Disciplina", "{$registro["desc_disciplina"]}") );
		}
		if( $registro["desc_resumida"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o Resumida", "{$registro["desc_resumida"]}") );
		}
		if( $registro["abreviatura"] )
		{
			$this->addDetalhe( array( "Abreviatura", "{$registro["abreviatura"]}") );
		}
		if( $registro["carga_horaria"] )
		{
			$this->addDetalhe( array( "Carga Hor&aacute;ria", "{$registro["carga_horaria"]}") );
		}
		if( $registro["apura_falta"] )
		{
			if ($registro["apura_falta"] == 0)
			{
				$registro["apura_falta"] = 'n&atilde;o';
			}
			else if ($registro["apura_falta"] == 1)
			{
				$registro["apura_falta"] = 'sim';
			}
			$this->addDetalhe( array( "Apura Falta", $registro["apura_falta"]) );
		}

		$objPermissao = new clsPermissoes();
		if( $objPermissao->permissao_cadastra( 557, $this->pessoa_logada,3 ) )
		{
			$this->url_novo = "educar_disciplina_cad.php";
			$this->url_editar = "educar_disciplina_cad.php?cod_disciplina={$registro["cod_disciplina"]}";
		}
		$this->url_cancelar = "educar_disciplina_lst.php";
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