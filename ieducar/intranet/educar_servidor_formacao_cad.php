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
require_once ("include/clsCadastro.inc.php");
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

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

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
	var $passo;
	var $data_conclusao;
	var $data_registro;
	var $diplomas_registros;
	var $ref_cod_instituicao;
	var $data_vigencia_homolog;
	var $data_publicacao;
	var $cod_servidor_curso;
	var $cod_servidor_titulo;

	function Inicializar()
	{
		$retorno = "";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_formacao 	   = $_GET["cod_formacao"];
		$this->ref_cod_servidor    = $_GET["ref_cod_servidor"];
		$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];
		$this->passo			   = $_POST["passo"];
		$this->tipo				   = $_POST["tipo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		if( is_string( $this->passo ) && $this->passo == 1 )
			$retorno = "Novo";

		if( is_numeric( $this->cod_formacao ) )
		{

			$obj = new clsPmieducarServidorFormacao( $this->cod_formacao, null, null, $this->ref_cod_servidor, null, null, null, null, null, 1, $this->ref_cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				$this->nm_formacao = $registro["nm_formacao"];
				$this->tipo		   = $registro["tipo"];
				$this->descricao   = $registro["descricao"];

				if ( $this->tipo == "C" ) {
					$obj_curso 				  = new clsPmieducarServidorCurso( null, $this->cod_formacao );
					$det_curso 				  = $obj_curso->detalhe();
					$this->data_conclusao 	  = dataFromPgToBr( $det_curso["data_conclusao"] );
					$this->data_registro  	  = dataFromPgToBr( $det_curso["data_registro"] );
					$this->diplomas_registros =	$det_curso["diplomas_registros"];
					$this->cod_servidor_curso = $det_curso["cod_servidor_curso"];
				}
				else {
					$obj_outros = new clsPmieducarServidorTituloConcurso( null, $this->cod_formacao );
					$det_outros = $obj_outros->detalhe();
					$this->data_vigencia_homolog = dataFromPgToBr( $det_outros["data_vigencia_homolog"] );
					$this->data_publicacao		 = dataFromPgToBr( $det_outros["data_publicacao"] );
					$this->cod_servidor_titulo   = $det_outros["cod_servidor_titulo"];
				}

				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
				$this->passo = 1;
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_servidor_formacao_det.php?cod_formacao={$registro["cod_formacao"]}" : "educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		//$this->ref_cod_servidor = $_GET["ref_cod_servidor"];// ($this->ref_cod_servidor == "") ?  : $this->ref_cod_servidore
		//echo "what --> ".$this->ref_cod_servidor;
		if ( !is_numeric( $this->passo ) ) {
			$this->passo = 1;
			$this->campoOculto( "passo", $this->passo );
			$opcoes = array( "C" => "Cursos", "T" => "Títulos", "O" => "Concursos" );
			$this->campoLista( "tipo", "Tipo de Formação", $opcoes, $this->tipo );
			$this->acao_enviar = false;
			$this->array_botao[] = 'Continuar';
			$this->array_botao_url_script[] = 'acao();';
			$this->url_cancelar = false;
			$this->array_botao[] = 'Cancelar';
			$this->array_botao_url_script[] = "go('educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}')";
		}
		elseif ( is_numeric( $this->passo ) && $this->passo == 1 ) {

			if ( $this->tipo == "C" ) {
				// primary keys
				$this->campoOculto( "cod_formacao", $this->cod_formacao );
				$this->campoOculto( "tipo", $this->tipo );
				$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );
				$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
				$this->campoOculto( "cod_servidor_curso", $this->cod_servidor_curso );
				$obrigatorio 	 = true;
				$get_instituicao = true;
				include("include/pmieducar/educar_campo_lista.php");

				$this->campoRotulo( "nm_tipo", "Tipo de Formação", ( $this->tipo == "C" ) ? "Curso" : "Error" );
				$this->campoTexto( "nm_formacao", "Nome do Curso", $this->nm_formacao, 30, 255, true );

				// foreign keys
				$nm_servidor = "";
				$objTemp = new clsFuncionario( $this->ref_cod_servidor );
				$detalhe = $objTemp->detalhe();
				if ( $detalhe ) {
					$objTmp = new clsPessoa_( $detalhe["ref_cod_pessoa_fj"] );
					$det    = $objTmp->detalhe();
					if ( $det ) {
						$nm_servidor = $det["nome"];
					}
				}
				$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 5, false );
				$this->campoRotulo( "nm_servidor", "Nome do Servidor", $nm_servidor );
				$this->campoData( "data_conclusao", "Data de Conclus&atilde;o", $this->data_conclusao, true );
				$this->campoData( "data_registro", "Data de Registro", $this->data_registro );
				$this->campoMemo( "diplomas_registros", "Diplomas e Registros", $this->diplomas_registros, 60, 5, false );
			}
			elseif ( $this->tipo == "T" ) {
				// primary keys
				$this->campoOculto( "cod_formacao", $this->cod_formacao );
				$this->campoOculto( "tipo", $this->tipo );
				$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );
				$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
				$this->campoOculto( "cod_servidor_titulo", $this->cod_servidor_titulo );
				$obrigatorio     = true;
				$get_instituicao = true;
				include("include/pmieducar/educar_campo_lista.php");

				$this->campoRotulo( "nm_tipo", "Tipo de Formação", ( $this->tipo == "T" ) ? "T&icirc;tulo" : "Error" );
				$this->campoTexto( "nm_formacao", "Nome do Título", $this->nm_formacao, 30, 255, true );

				// foreign keys
				$nm_servidor = "";
				$objTemp = new clsFuncionario( $this->ref_cod_servidor );
				$detalhe = $objTemp->detalhe();
				if ( $detalhe ) {
					$objTmp = new clsPessoa_( $detalhe["ref_cod_pessoa_fj"] );
					$det    = $objTmp->detalhe();
					if ( $det ) {
						$nm_servidor = $det["nome"];
					}
				}
				$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 5, false );
				$this->campoRotulo( "nm_servidor", "Nome do Servidor", $nm_servidor );
				$this->campoData( "data_vigencia_homolog", "Data de Vig&ecirc;ncia", $this->data_vigencia_homolog, true );
				$this->campoData( "data_publicacao", "Data de Publica&ccedil;&atilde;o", $this->data_publicacao, true );
			}
			elseif ( $this->tipo == "O" ) {
				// primary keys
				$this->campoOculto( "cod_formacao", $this->cod_formacao );
				$this->campoOculto( "tipo", $this->tipo );
				$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );
				$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
				$this->campoOculto( "cod_servidor_titulo", $this->cod_servidor_titulo );
				$obrigatorio = true;
				$get_instituicao = true;
				include("include/pmieducar/educar_campo_lista.php");

				$this->campoRotulo( "nm_tipo", "Tipo de Formação", ( $this->tipo == "O" ) ? "Forma&ccedil;&atilde;o" : "Error" );
				$this->campoTexto( "nm_formacao", "Nome do Concurso", $this->nm_formacao, 30, 255, true );

				// foreign keys
				$nm_servidor = "";
				$objTemp = new clsFuncionario( $this->ref_cod_servidor );
				$detalhe = $objTemp->detalhe();
				if ( $detalhe ) {
					$objTmp = new clsPessoa_( $detalhe["ref_cod_pessoa_fj"] );
					$det    = $objTmp->detalhe();
					if ( $det ) {
						$nm_servidor = $det["nome"];
					}
				}
				$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 5, false );
				$this->campoRotulo( "nm_servidor", "Nome do Servidor", $nm_servidor );
				$this->campoData( "data_vigencia_homolog", "Data de Homologa&ccedil;&atilde;o", $this->data_vigencia_homolog, true );
				$this->campoData( "data_publicacao", "Data de Publica&ccedil;&atilde;o", $this->data_publicacao, true );
			}
		}
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		$obj = new clsPmieducarServidorFormacao( null, null, $this->pessoa_logada, $this->ref_cod_servidor, $this->nm_formacao, $this->tipo, $this->descricao, null, null, $this->ativo, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			if ( $this->tipo == "C" ) {
				$obj = new clsPmieducarServidorCurso( null, $cadastrou, dataToBanco( $this->data_conclusao ), dataToBanco( $this->data_registro ), $this->diplomas_registros );
				if ( $obj->cadastra() ) {
					$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
					header( "Location: educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
					die();
					return true;
				}
			}
			elseif ( $this->tipo == "T" || $this->tipo == "O" ) {
				$obj = new clsPmieducarServidorTituloConcurso( null, $cadastrou, dataToBanco( $this->data_vigencia_homolog ), dataToBanco( $this->data_publicacao ) );
				if ( $obj->cadastra() ) {
					$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
					header( "Location: educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
					die();
					return true;
				}
			}
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarServidorFormacao\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_servidor ) && is_string( $this->nm_formacao ) && is_string( $this->tipo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


		$obj = new clsPmieducarServidorFormacao( $this->cod_formacao, $this->pessoa_logada, null, $this->ref_cod_servidor, $this->nm_formacao, $this->tipo, $this->descricao, null, null, 1 );
		$editou = $obj->edita();
		if( $editou )
		{
			if ( $this->tipo == "C" ) {
				$obj_curso  = new clsPmieducarServidorCurso( $this->cod_servidor_curso, $this->cod_formacao, dataToBanco( $this->data_conclusao ), dataToBanco( $this->data_registro ), $this->diplomas_registros );
				$editou_cur = $obj_curso->edita();
				if ( $editou_cur ) {
					$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
					die();
					return true;
				}
			}
			else {
				$obj_titulo = new clsPmieducarServidorTituloConcurso( $this->cod_servidor_titulo, $this->cod_formacao, dataToBanco( $this->data_vigencia_homolog ), dataToBanco( $this->data_publicacao ) );
				$editou_tit = $obj_titulo->edita();
				if ( $editou_tit ) {
					$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
					die();
					return true;
				}
			}
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarServidorFormacao\nvalores obrigatorios\nif( is_numeric( $this->cod_formacao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3,  "educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


		$obj = new clsPmieducarServidorFormacao( $this->cod_formacao, $this->pessoa_logada, null, $this->ref_cod_servidor, $this->nm_formacao, $this->tipo, $this->descricao, null, null, 0, $this->ref_cod_instituicao );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_servidor_formacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarServidorFormacao\nvalores obrigatorios\nif( is_numeric( $this->cod_formacao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
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