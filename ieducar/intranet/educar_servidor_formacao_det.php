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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Formacao" );
		$this->processoAp = "635";
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

	var $cod_formacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_servidor;
	var $nm_formacao;
	var $tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Servidor Formacao - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_formacao=$_GET["cod_formacao"];

		$tmp_obj = new clsPmieducarServidorFormacao( $this->cod_formacao );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_servidor_formacao_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarServidor" ) )
		{
			$obj_ref_cod_servidor = new clsPmieducarServidor( $registro["ref_cod_servidor"], null, null, null, null, null, 1, $registro["ref_ref_cod_instituicao"] );
			$det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();
			$registro["ref_cod_servidor"] = $det_ref_cod_servidor["cod_servidor"];
		}
		else
		{
			$registro["ref_cod_servidor"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarServidor\n-->";
		}
		if( $registro["nm_formacao"] )
		{
			$this->addDetalhe( array( "Nome Forma&ccedil;&atilde;o", "{$registro["nm_formacao"]}") );
		}
		if ( $registro["tipo"] == "C" ) {
			$obj_curso = new clsPmieducarServidorCurso( null, $this->cod_formacao );
			$det_curso = $obj_curso->detalhe();
		}
		elseif ( $registro["tipo"] == "T" || $registro["tipo"] == "O" ) {
			$obj_titulo = new clsPmieducarServidorTituloConcurso( null, $this->cod_formacao );
			$det_titulo = $obj_titulo->detalhe();
		}
		if( $registro["tipo"] )
		{
			if ( $registro["tipo"] == "C" ) {
				$registro["tipo"] = "Curso";
			}
			elseif ( $registro["tipo"] == "T" ) {
				$registro["tipo"] = "T&iacute;tulo";
			}
			else {
				$registro["tipo"] = "Concurso";
			}
			$this->addDetalhe( array( "Tipo", "{$registro["tipo"]}") );
		}
		if( $registro["descricao"] )
		{
			$this->addDetalhe( array( "Descric&atilde;o", "{$registro["descricao"]}") );
		}
		if ( $det_curso["data_conclusao"] ) {
			$this->addDetalhe( array( "Data de Conclus&atilde;o", "".dataFromPgToBr( $det_curso["data_conclusao"] )."" ) );
		}
		if ( $det_curso["data_registro"] ) {
			$this->addDetalhe( array( "Data de Registro", "".dataFromPgToBr( $det_curso["data_registro"] )."" ) );
		}
		if ( $det_curso["diplomas_registros"] ) {
			$this->addDetalhe( array( "Diplomas e Registros", "{$det_curso["diplomas_registros"]}" ) );
		}
		if ( $det_titulo["data_vigencia_homolog"] && $registro["tipo"] == "T&iacute;tulo") {
			$this->addDetalhe( array( "Data de Vigência", "".dataFromPgToBr( $det_titulo["data_vigencia_homolog"] )."" ) );
		}
		elseif ( $det_titulo["data_vigencia_homolog"] && $registro["tipo"] == "Concurso") {
			$this->addDetalhe( array( "Data de Homologação", "".dataFromPgToBr( $det_titulo["data_vigencia_homolog"] )."" ) );
		}
		if ( $det_titulo["data_publicacao"] ) {
			$this->addDetalhe( array( "Data de Publicação", "".dataFromPgToBr( $det_titulo["data_publicacao"] )."" ) );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "educar_servidor_formacao_cad.php";
			$this->url_editar = "educar_servidor_formacao_cad.php?cod_formacao={$registro["cod_formacao"]}&ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}&ref_cod_servidor={$registro["ref_cod_servidor"]}";
		}

		$this->url_cancelar = "educar_servidor_formacao_lst.php?ref_cod_servidor={$registro["ref_cod_servidor"]}&ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}";
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