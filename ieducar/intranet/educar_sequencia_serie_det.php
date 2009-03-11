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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Sequ&ecirc;ncia Enturma&ccedil;&atilde;o" );
		$this->processoAp = "587";
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

	var $ref_serie_origem;
	var $ref_serie_destino;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Sequ&ecirc;ncia Enturma&ccedil;&atilde;o - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->ref_serie_origem = $_GET["ref_serie_origem"];
		$this->ref_serie_destino = $_GET["ref_serie_destino"];

		$tmp_obj = new clsPmieducarSequenciaSerie( $this->ref_serie_origem, $this->ref_serie_destino );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_sequencia_serie_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_ref_serie_origem = new clsPmieducarSerie( $registro["ref_serie_origem"] );
			$det_ref_serie_origem = $obj_ref_serie_origem->detalhe();
			$nm_serie_origem = $det_ref_serie_origem["nm_serie"];
			$registro["ref_curso_origem"] = $det_ref_serie_origem["ref_cod_curso"];
			if( class_exists( "clsPmieducarCurso" ) )
			{
				$obj_ref_curso_origem = new clsPmieducarCurso( $registro["ref_curso_origem"] );
				$det_ref_curso_origem = $obj_ref_curso_origem->detalhe();
				$nm_curso_origem = $det_ref_curso_origem["nm_curso"];
				$registro["ref_cod_instituicao"] = $det_ref_curso_origem["ref_cod_instituicao"];
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$det_instituicao = $obj_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $det_instituicao["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}
			}
			else
			{
				$registro["ref_cod_origem"] = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarCurso\n-->";
			}
		}
		else
		{
			$registro["ref_serie_origem"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
		}
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_ref_serie_destino = new clsPmieducarSerie( $registro["ref_serie_destino"] );
			$det_ref_serie_destino = $obj_ref_serie_destino->detalhe();
			$nm_serie_destino = $det_ref_serie_destino["nm_serie"];
			$registro["ref_curso_destino"] = $det_ref_serie_destino["ref_cod_curso"];
			if( class_exists( "clsPmieducarCurso" ) )
			{
				$obj_ref_curso_destino = new clsPmieducarCurso( $registro["ref_curso_destino"] );
				$det_ref_curso_destino = $obj_ref_curso_destino->detalhe();
				$nm_curso_destino = $det_ref_curso_destino["nm_curso"];
			}
			else
			{
				$registro["ref_cod_destino"] = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarCurso\n-->";
			}
		}
		else
		{
			$registro["ref_serie_destino"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
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
		if( $nm_curso_origem )
		{
			$this->addDetalhe( array( "Curso Origem", "{$nm_curso_origem}") );
		}
		if( $nm_serie_origem )
		{
			$this->addDetalhe( array( "S&eacute;rie Origem", "{$nm_serie_origem}") );
		}
		if( $nm_curso_destino )
		{
			$this->addDetalhe( array( "Curso Destino", "{$nm_curso_destino}") );
		}
		if( $nm_serie_destino )
		{
			$this->addDetalhe( array( "S&eacute;rie Destino", "{$nm_serie_destino}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 587, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "educar_sequencia_serie_cad.php";
			$this->url_editar = "educar_sequencia_serie_cad.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}";
		}

		$this->url_cancelar = "educar_sequencia_serie_lst.php";
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