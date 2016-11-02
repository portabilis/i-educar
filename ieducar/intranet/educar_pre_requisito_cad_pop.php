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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Pre Requisito" );
		$this->processoAp = "601";
		$this->renderBanner = false;
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
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

	var $cod_pre_requisito;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $schema_;
	var $tabela;
	var $nome;
	var $sql;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_pre_requisito=$_GET["cod_pre_requisito"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3,  "educar_pre_requisito_lst.php", true );

		if( is_numeric( $this->cod_pre_requisito ) )
		{

			$obj = new clsPmieducarPreRequisito( $this->cod_pre_requisito );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 601, $this->pessoa_logada, 3, null, true ) )
			{
				$this->fexcluir = true;
			}

				$retorno = "Editar";
			}
		}
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_pre_requisito_det.php?cod_pre_requisito={$registro["cod_pre_requisito"]}" : "educar_pre_requisito_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
		return $retorno;
	}

	function Gerar()
	{
		$db = new clsBanco();

		// primary keys
		$this->campoOculto( "cod_pre_requisito", $this->cod_pre_requisito );

		// foreign keys
		$opcoes = array( "Selecione o Schema" );
		$db->Consulta( "SELECT DISTINCT schemaname FROM pg_catalog.pg_tables WHERE schemaname NOT IN ('pg_catalog', 'information_schema', 'pg_toast') ORDER BY schemaname" );
		while ( $db->ProximoRegistro() )
		{
			list( $schema ) = $db->Tupla();
			$opcoes[$schema] = $schema;
		}
		$this->campoLista( "schema_", "Schema", $opcoes, $this->schema_, "buscaTabela( 'tabela' )" );

		$opcoes = array( "Selecione a Tabela" );
		$this->campoLista( "tabela", "Tabela", $opcoes, $this->tabela, "", false, "", "", true );

		// text
//		$this->campoTexto( "schema_", "Schema ", $this->schema_, 30, 255, true );
//		$this->campoTexto( "tabela", "Tabela", $this->tabela, 30, 255, true );
		$this->campoTexto( "nome", "Nome", $this->nome, 30, 255, true );
		$this->campoMemo( "sql", "Sql", $this->sql, 60, 10, false );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3,  "educar_pre_requisito_lst.php", true );


		$obj = new clsPmieducarPreRequisito( $this->cod_pre_requisito, $this->pessoa_logada, $this->pessoa_logada, $this->schema_, $this->tabela, $this->nome, $this->sql, $this->data_cadastro, $this->data_exclusao, $this->ativo );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			echo "<script>
						parent.document.getElementById('ref_cod_pre_requisito').options[parent.document.getElementById('ref_cod_pre_requisito').options.length] = new Option('$this->nome', '$cadastrou', false, false);
						parent.document.getElementById('ref_cod_pre_requisito').value = '$cadastrou';
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarPreRequisito\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->schema_ ) && is_string( $this->tabela ) && is_string( $this->nome ) && is_string( $this->sql )\n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3,  "educar_pre_requisito_lst.php", true );


		$obj = new clsPmieducarPreRequisito($this->cod_pre_requisito, $this->pessoa_logada, $this->pessoa_logada, $this->schema_, $this->tabela, $this->nome, $this->sql, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_pre_requisito_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarPreRequisito\nvalores obrigatorios\nif( is_numeric( $this->cod_pre_requisito ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 601, $this->pessoa_logada, 3,  "educar_pre_requisito_lst.php", true );


		$obj = new clsPmieducarPreRequisito($this->cod_pre_requisito, $this->pessoa_logada, $this->pessoa_logada, $this->schema_, $this->tabela, $this->nome, $this->sql, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_pre_requisito_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarPreRequisito\nvalores obrigatorios\nif( is_numeric( $this->cod_pre_requisito ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;*/
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