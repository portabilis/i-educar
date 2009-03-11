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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Exemplar" );
		$this->processoAp = "597";
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

	var $cod_exemplar_tipo;
	var $ref_cod_biblioteca;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Tipo Exemplar - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_exemplar_tipo = $_GET["cod_exemplar_tipo"];

		$tmp_obj = new clsPmieducarExemplarTipo( $this->cod_exemplar_tipo );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_exemplar_tipo_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarBiblioteca" ) )
		{
			$obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
			$det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
			$registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
			$registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
			$registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
			if( $registro["ref_cod_instituicao"] )
			{
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
			if( $registro["ref_cod_escola"] )
			{
				$obj_ref_cod_escola = new clsPmieducarEscola();
				$det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["ref_cod_escola"]));
				$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
			}
		}
		else
		{
			$registro["ref_cod_biblioteca"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarBiblioteca\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if( $registro["ref_cod_instituicao"] && $nivel_usuario == 1)
		{
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $registro["ref_cod_escola"] && ($nivel_usuario == 1 || $nivel_usuario == 2) )
		{
			$this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
		}
		if( $registro["ref_cod_biblioteca"] && ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4))
		{
			$this->addDetalhe( array( "Biblioteca", "{$registro["ref_cod_biblioteca"]}") );
		}
		if( $registro["nm_tipo"] )
		{
			$this->addDetalhe( array( "Tipo Exemplar", "{$registro["nm_tipo"]}") );
		}
		if( $registro["descricao"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
		}
		$obj_cliente_tp_exemplar_tp = new clsPmieducarClienteTipoExemplarTipo();
		$lst_cliente_tp_exemplar_tp = $obj_cliente_tp_exemplar_tp->lista( null, $this->cod_exemplar_tipo );
		if ($lst_cliente_tp_exemplar_tp)
		{
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Tipo Cliente</B></TD>
					           <TD bgcolor=#A1B3BD><B>Dias Empr&eacute;stimo</B></TD>
					       </TR>";
			$cont = 0;

			foreach ( $lst_cliente_tp_exemplar_tp AS $valor )
			{
				if ( ($cont % 2) == 0 )
				{
					$color = " bgcolor=#E4E9ED ";
				}
				else
				{
					$color = " bgcolor=#FFFFFF ";
				}
				$obj_cliente_tipo = new clsPmieducarClienteTipo( $valor["ref_cod_cliente_tipo"] );
				$det_cliente_tipo = $obj_cliente_tipo->detalhe();
				$nm_tipo = $det_cliente_tipo["nm_tipo"];

				$tabela .= "<TR>
							    <TD {$color} align=left>{$nm_tipo}</TD>
							    <TD {$color} align=left>{$valor["dias_emprestimo"]}</TD>
							</TR>";
				$cont++;
			}
			$tabela .= "</TABLE>";
		}
		if( $tabela )
		{
			$this->addDetalhe( array( "Tipo Cliente", "{$tabela}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11 ) )
		{
			$this->url_novo = "educar_exemplar_tipo_cad.php";
			$this->url_editar = "educar_exemplar_tipo_cad.php?cod_exemplar_tipo={$registro["cod_exemplar_tipo"]}";
		}

		$this->url_cancelar = "educar_exemplar_tipo_lst.php";
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