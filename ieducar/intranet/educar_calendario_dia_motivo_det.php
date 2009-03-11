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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Calend&aacute;rio Dia Motivo" );
		$this->processoAp = "576";
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

	var $cod_calendario_dia_motivo;
	var $ref_cod_escola;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $sigla;
	var $descricao;
	var $tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Calend&aacute;rio Dia Motivo - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_calendario_dia_motivo=$_GET["cod_calendario_dia_motivo"];

		$tmp_obj = new clsPmieducarCalendarioDiaMotivo( $this->cod_calendario_dia_motivo );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_calendario_dia_motivo_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
			$obj_cod_escola_det = $obj_cod_escola->detalhe();
			$registro["ref_cod_escola"] = $obj_cod_escola_det["nome"];

			if (class_exists("clsPmieducarInstituicao"))
			{
				$cod_instituicao = $obj_cod_escola_det['ref_cod_instituicao'];
				$obj_instituicao = new clsPmieducarInstituicao($cod_instituicao);
				$obj_instituicao_det = $obj_instituicao->detalhe();
				$nm_instituicao = $obj_instituicao_det['nm_instituicao'];
			}
			else
			{
				$cod_instituicao = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
			}
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}

		if ($nm_instituicao)
		{
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$nm_instituicao}" ) );
		}
		if( $registro["ref_cod_escola"] )
		{
			$this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
		}
		if( $registro["nm_motivo"] )
		{
			$this->addDetalhe( array( "Motivo", "{$registro["nm_motivo"]}") );
		}
		if( $registro["sigla"] )
		{
			$this->addDetalhe( array( "Sigla", "{$registro["sigla"]}") );
		}
		if( $registro["descricao"] )
		{
			$this->addDetalhe( array( "Descric&atilde;o", "{$registro["descricao"]}") );
		}
		if( $registro["tipo"] )
		{
			if ($registro["tipo"] == 'e')
			{
				$registro["tipo"] = 'extra';
			}
			else if ($registro["tipo"] == 'n')
			{
				$registro["tipo"] = 'n&atilde;o-letivo';
			}
			$this->addDetalhe( array( "Tipo", "{$registro["tipo"]}") );
		}

		$obj_permissao = new clsPermissoes();
		if( $obj_permissao->permissao_cadastra( 576, $this->pessoa_logada,7 ) )
		{
			$this->url_novo = "educar_calendario_dia_motivo_cad.php";
			$this->url_editar = "educar_calendario_dia_motivo_cad.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}";
		}
		$this->url_cancelar = "educar_calendario_dia_motivo_lst.php";
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