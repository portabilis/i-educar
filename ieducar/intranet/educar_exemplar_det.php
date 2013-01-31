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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar" );
		$this->processoAp = "606";
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

	var $cod_exemplar;
	var $ref_cod_fonte;
	var $ref_cod_motivo_baixa;
	var $ref_cod_acervo;
	var $ref_cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $permite_emprestimo;
	var $preco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $data_aquisicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Exemplar - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_exemplar=$_GET["cod_exemplar"];

		$tmp_obj = new clsPmieducarExemplar( $this->cod_exemplar );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_exemplar_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarFonte" ) )
		{
			$obj_ref_cod_fonte = new clsPmieducarFonte( $registro["ref_cod_fonte"] );
			$det_ref_cod_fonte = $obj_ref_cod_fonte->detalhe();
			$registro["ref_cod_fonte"] = $det_ref_cod_fonte["nm_fonte"];
		}
		else
		{
			$registro["ref_cod_fonte"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarFonte\n-->";
		}

		if( class_exists( "clsPmieducarMotivoBaixa" ) )
		{
			$obj_ref_cod_motivo_baixa = new clsPmieducarMotivoBaixa( $registro["ref_cod_motivo_baixa"] );
			$det_ref_cod_motivo_baixa = $obj_ref_cod_motivo_baixa->detalhe();
			$registro["ref_cod_motivo_baixa"] = $det_ref_cod_motivo_baixa["nm_motivo_baixa"];
		}
		else
		{
			$registro["ref_cod_motivo_baixa"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarMotivoBaixa\n-->";
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

		if( class_exists( "clsPmieducarSituacao" ) )
		{
			$obj_ref_cod_situacao = new clsPmieducarSituacao( $registro["ref_cod_situacao"] );
			$det_ref_cod_situacao = $obj_ref_cod_situacao->detalhe();
			$registro["ref_cod_situacao"] = $det_ref_cod_situacao["nm_situacao"];
		}
		else
		{
			$registro["ref_cod_situacao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarSituacao\n-->";
		}

		$this->addDetalhe(array("Código", "{$registro["cod_exemplar"]}"));
		$this->addDetalhe(array("Tombo",  "{$registro["tombo"]}"));

		if( $registro["ref_cod_acervo"] )
		{
			$this->addDetalhe( array( "Obra Refer&eacute;ncia", "{$registro["ref_cod_acervo"]}") );
		}
		if( $registro["ref_cod_fonte"] )
		{
			$this->addDetalhe( array( "Fonte", "{$registro["ref_cod_fonte"]}") );
		}
		if( $registro["ref_cod_motivo_baixa"] )
		{
			$this->addDetalhe( array( "Motivo Baixa", "{$registro["ref_cod_motivo_baixa"]}") );
		}

		if( $registro["ref_cod_situacao"] )
		{
			$this->addDetalhe( array( "Situac&atilde;o", "{$registro["ref_cod_situacao"]}") );
		}
		if( $registro["permite_emprestimo"] )
		{
			$registro["permite_emprestimo"] = $registro["permite_emprestimo"] == 2 ? "Sim" :"N&atilde;o";

			$this->addDetalhe( array( "Permite Empr&eacute;stimo", "{$registro["permite_emprestimo"]}") );
		}
		if( $registro["preco"] )
		{
			$registro['preco'] = number_format($registro['preco'], 2, ",", ".");
			$this->addDetalhe( array( "Pre&ccedil;o", "{$registro["preco"]}") );
		}
		if( $registro["data_aquisicao"] )
		{
			$this->addDetalhe( array( "Data Aquisic&atilde;o", dataFromPgToBr( $registro["data_aquisicao"], "d/m/Y" ) ) );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11 ) )
		{
			$this->url_novo = "educar_exemplar_cad.php";
			$this->url_editar = "educar_exemplar_cad.php?cod_exemplar={$registro["cod_exemplar"]}";

			if(!$registro["ref_cod_motivo_baixa"])
			{
				$this->array_botao = array('Baixa');
				$this->array_botao_url = array("educar_exemplar_baixa.php?cod_exemplar={$registro["cod_exemplar"]}");
			}
		}

		$this->url_cancelar = "educar_exemplar_lst.php";
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
