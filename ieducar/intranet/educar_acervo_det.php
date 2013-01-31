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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Obras" );
		$this->processoAp = "598";
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

	var $cod_acervo;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_acervo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_acervo_colecao;
	var $ref_cod_acervo_idioma;
	var $ref_cod_acervo_editora;
	var $sub_titulo;
	var $cdu;
	var $cutter;
	var $volume;
	var $num_edicao;
	var $ano;
	var $num_paginas;
	var $isbn;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Obras - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_acervo=$_GET["cod_acervo"];

		$tmp_obj = new clsPmieducarAcervo( $this->cod_acervo );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_acervo_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarBiblioteca" ) )
		{
			$obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
			$det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
			$registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
			}

			if( class_exists( "clsPmieducarEscola" ) )
			{
				$registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
				$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
				$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
				$idpes = $det_ref_cod_escola["ref_idpes"];
				if ($idpes)
				{
					$obj_escola = new clsPessoaJuridica( $idpes );
					$obj_escola_det = $obj_escola->detalhe();
					$registro["ref_cod_escola"] = $obj_escola_det["fantasia"];
				}
				else
				{
					$obj_escola = new clsPmieducarEscolaComplemento( $registro["ref_cod_escola"] );
					$obj_escola_det = $obj_escola->detalhe();
					$registro["ref_cod_escola"] = $obj_escola_det["nm_escola"];
				}
			}
		}
		else
		{
			$registro["ref_cod_biblioteca"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarBiblioteca\n-->";
		}

		if( class_exists( "clsPmieducarExemplarTipo" ) )
		{
			$obj_ref_cod_exemplar_tipo = new clsPmieducarExemplarTipo( $registro["ref_cod_exemplar_tipo"] );
			$det_ref_cod_exemplar_tipo = $obj_ref_cod_exemplar_tipo->detalhe();
			$registro["ref_cod_exemplar_tipo"] = $det_ref_cod_exemplar_tipo["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_exemplar_tipo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarExemplarTipo\n-->";
		}

		if( class_exists( "clsPmieducarAcervo" ) )
		{
			$obj_ref_cod_acervo = new clsPmieducarAcervo( $registro["ref_cod_acervo"] );
			$det_ref_cod_acervo = $obj_ref_cod_acervo->detalhe();
			$registro["ref_cod_acervo"] = $det_ref_cod_acervo["titulo"];
		}
		else
		{
			$registro["ref_cod_acervo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarAcervo\n-->";
		}

		if( class_exists( "clsPmieducarAcervoColecao" ) )
		{
			$obj_ref_cod_acervo_colecao = new clsPmieducarAcervoColecao( $registro["ref_cod_acervo_colecao"] );
			$det_ref_cod_acervo_colecao = $obj_ref_cod_acervo_colecao->detalhe();
			$registro["ref_cod_acervo_colecao"] = $det_ref_cod_acervo_colecao["nm_colecao"];
		}
		else
		{
			$registro["ref_cod_acervo_colecao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarAcervoColecao\n-->";
		}

		if( class_exists( "clsPmieducarAcervoIdioma" ) )
		{
			$obj_ref_cod_acervo_idioma = new clsPmieducarAcervoIdioma( $registro["ref_cod_acervo_idioma"] );
			$det_ref_cod_acervo_idioma = $obj_ref_cod_acervo_idioma->detalhe();
			$registro["ref_cod_acervo_idioma"] = $det_ref_cod_acervo_idioma["nm_idioma"];
		}
		else
		{
			$registro["ref_cod_acervo_idioma"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarAcervoIdioma\n-->";
		}

		if( class_exists( "clsPmieducarAcervoEditora" ) )
		{
			$obj_ref_cod_acervo_editora = new clsPmieducarAcervoEditora( $registro["ref_cod_acervo_editora"] );
			$det_ref_cod_acervo_editora = $obj_ref_cod_acervo_editora->detalhe();
			$registro["ref_cod_acervo_editora"] = $det_ref_cod_acervo_editora["nm_editora"];
		}
		else
		{
			$registro["ref_cod_acervo_editora"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarAcervoEditora\n-->";
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
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $registro["ref_cod_escola"] )
			{
				$this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
			}
		}
		if( $registro["ref_cod_biblioteca"] )
		{
				$this->addDetalhe( array( "Biblioteca", "{$registro["ref_cod_biblioteca"]}") );
		}
		if( $registro["titulo"] )
		{
			$this->addDetalhe( array( "T&iacute;tulo", "{$registro["titulo"]}") );
		}
		if( $registro["sub_titulo"] )
		{
			$this->addDetalhe( array( "Subt&iacute;tulo", "{$registro["sub_titulo"]}") );
		}
		if( $registro["ref_cod_exemplar_tipo"] )
		{
			$this->addDetalhe( array( "Tipo", "{$registro["ref_cod_exemplar_tipo"]}") );
		}
		if( $registro["ref_cod_acervo"] )
		{
			$this->addDetalhe( array( "Obra Refer&ecirc;ncia", "{$registro["ref_cod_acervo"]}") );
		}
		if( $registro["ref_cod_acervo_colecao"] )
		{
			$this->addDetalhe( array( "Cole&ccedil;&atilde;o", "{$registro["ref_cod_acervo_colecao"]}") );
		}
		if( $registro["ref_cod_acervo_idioma"] )
		{
			$this->addDetalhe( array( "Idioma", "{$registro["ref_cod_acervo_idioma"]}") );
		}
		if( $registro["ref_cod_acervo_editora"] )
		{
			$this->addDetalhe( array( "Editora", "{$registro["ref_cod_acervo_editora"]}") );
		}

		$obj = new clsPmieducarAcervoAcervoAutor();
		$obj->setOrderby("principal DESC");
		$lst = $obj->lista( null,$this->cod_acervo );
		if ($lst) {
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
					           <TD bgcolor=#A1B3BD><B>Principal</B></TD>
					       </TR>";
			$cont = 0;

			foreach ( $lst AS $valor ) {
				if ( ($cont % 2) == 0 ) {
					$color = " bgcolor=#E4E9ED ";
				}
				else {
					$color = " bgcolor=#FFFFFF ";
				}
				$obj = new clsPmieducarAcervoAutor( $valor["ref_cod_acervo_autor"] );
				$det = $obj->detalhe();
				$nm_autor = $det["nm_autor"];
				$principal = $valor["principal"];
				if ($principal == 1)
					$principal = "sim";
				else
					$principal = "n&atilde;o";

				$tabela .= "<TR>
							    <TD {$color} align=left>{$nm_autor}</TD>
							    <TD {$color} align=left>{$principal}</TD>
							</TR>";
				$cont++;
			}
			$tabela .= "</TABLE>";
		}
		if( $tabela )
		{
			$this->addDetalhe( array( "Autor", "{$tabela}") );
		}

		if($registro["estante"])
			$this->addDetalhe( array( "Estante", "{$registro["estante"]}") );

		if($registro["cdd"])
			$this->addDetalhe( array( "Cdd", "{$registro["cdd"]}") );

		if( $registro["cdu"] )
		{
			$this->addDetalhe( array( "Cdu", "{$registro["cdu"]}") );
		}
		if( $registro["cutter"] )
		{
			$this->addDetalhe( array( "Cutter", "{$registro["cutter"]}") );
		}
		if( $registro["volume"] )
		{
			$this->addDetalhe( array( "Volume", "{$registro["volume"]}") );
		}
		if( $registro["num_edicao"] )
		{
			$this->addDetalhe( array( "N&uacute;mero Edic&atilde;o", "{$registro["num_edicao"]}") );
		}
		if( $registro["ano"] )
		{
			$this->addDetalhe( array( "Ano", "{$registro["ano"]}") );
		}
		if( $registro["num_paginas"] )
		{
			$this->addDetalhe( array( "N&uacute;mero P&aacute;ginas", "{$registro["num_paginas"]}") );
		}
		if( $registro["isbn"] )
		{
			$this->addDetalhe( array( "ISBN", "{$registro["isbn"]}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11 ) )
		{
			$this->url_novo = "educar_acervo_cad.php";
			$this->url_editar = "educar_acervo_cad.php?cod_acervo={$registro["cod_acervo"]}";
		}

		$this->url_cancelar = "educar_acervo_lst.php";
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
