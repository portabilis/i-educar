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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Falta Atraso Compensado" );
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

	var $cod_compensado;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	var $ref_cod_servidor;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_inicio;
	var $data_fim;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_compensado 	   = $_GET["cod_compensado"];
		$this->ref_cod_servidor    = $_GET["ref_cod_servidor"];
		$this->ref_cod_escola	   = $_GET["ref_cod_escola"];
		$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		if( is_numeric( $this->cod_compensado ) )
		{

			$obj = new clsPmieducarFaltaAtrasoCompensado( $this->cod_compensado );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_inicio = dataFromPgToBr( $this->data_inicio );
				$this->data_fim = dataFromPgToBr( $this->data_fim );
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7 ) )
			{
				$this->fexcluir = true;
			}

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" : "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_compensado", $this->cod_compensado );
		$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );

		// foreign keys
		$obrigatorio 	 = true;
		$get_instituicao = true;
		$get_escola		 = true;
		include("include/pmieducar/educar_campo_lista.php");

		// data
		$this->campoData( "data_inicio", "Data Inicio", $this->data_inicio, true );
		$this->campoData( "data_fim", "Data Fim", $this->data_fim, true );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		$obj = new clsPmieducarFaltaAtrasoCompensado( null, $this->ref_cod_escola, $this->ref_cod_instituicao, $this->ref_cod_servidor, $this->pessoa_logada, $this->pessoa_logada, $this->data_inicio, $this->data_fim, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarFaltaAtrasoCompensado\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->data_inicio ) && is_string( $this->data_fim )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


		$obj = new clsPmieducarFaltaAtrasoCompensado($this->cod_compensado, $this->ref_cod_escola, $this->ref_cod_instituicao, $this->ref_cod_servidor, $this->pessoa_logada, $this->pessoa_logada, $this->data_inicio, $this->data_fim, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarFaltaAtrasoCompensado\nvalores obrigatorios\nif( is_numeric( $this->cod_compensado ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7,  "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


		$obj = new clsPmieducarFaltaAtrasoCompensado($this->cod_compensado, $this->ref_cod_escola, $this->ref_cod_instituicao, $this->ref_cod_servidor, $this->pessoa_logada, $this->pessoa_logada, $this->data_inicio, $this->data_fim, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarFaltaAtrasoCompensado\nvalores obrigatorios\nif( is_numeric( $this->cod_compensado ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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