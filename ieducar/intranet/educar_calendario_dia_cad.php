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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Calendario Dia" );
		$this->processoAp = "620";
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

	var $ref_cod_calendario_ano_letivo;
	var $mes;
	var $dia;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_calendario_dia_motivo;
	//var $ref_cod_calendario_atividade;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ano;
	var $ref_cod_escola;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->dia=$_GET["dia"];
		$this->mes=$_GET["mes"];
		$this->ref_cod_calendario_ano_letivo = $_GET["ref_cod_calendario_ano_letivo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7,  "educar_calendario_dia_lst.php" );

		if( is_numeric( $this->ref_cod_calendario_ano_letivo ) && is_numeric( $this->mes ) && is_numeric( $this->dia ) )
		{

			$obj = new clsPmieducarCalendarioDia( $this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;


			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 620, $this->pessoa_logada, 7 ) )
			{
				if($this->descricao)
					$this->fexcluir = true;
			}

				$retorno = "Editar";
			}

			if( class_exists( "clsPmieducarCalendarioAnoLetivo" ) )
			{
				$objTemp = new clsPmieducarCalendarioAnoLetivo($this->ref_cod_calendario_ano_letivo);

				$det = $objTemp->detalhe();
				$this->ano = $det["ano"];

			}
			else
			{
				header("location:educar_calendario_dia_lst.php?ref_cod_calendario_ano_letivo={$registro["ref_cod_calendario_ano_letivo"]}&mes={$registro["mes"]}&dia={$registro["dia"]}");
			}

		}
		$this->url_cancelar = "educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo={$registro["ref_cod_calendario_ano_letivo"]}&ano={$this->ano}&mes={$registro["mes"]}&dia={$registro["dia"]}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{

		// primary keys
		$this->campoRotulo( "dia_","Dia", "<b>{$this->dia}/{$this->mes}/{$this->ano}</b> ");
		$this->campoOculto( "ref_cod_calendario_ano_letivo", $this->ref_cod_calendario_ano_letivo );

		$obj_calendario_ano_letivo = new clsPmieducarCalendarioAnoLetivo( $this->ref_cod_calendario_ano_letivo );
		$det_calendario_ano_letivo = $obj_calendario_ano_letivo->detalhe();
		$ref_cod_escola = $det_calendario_ano_letivo["ref_cod_escola"];

		//$opcoes = array( "" => "Selecione" );

		//$this->campoLista( "ref_cod_calendario_ano_letivo", "Calendario Ano Letivo", $opcoes, $this->ref_cod_calendario_ano_letivo,"","","","",true );


		$this->campoRotulo( "ano", "Ano Letivo",$this->ano );

		$this->campoOculto( "mes", $this->mes );
		$this->campoOculto( "dia", $this->dia );
		//$this->campoOculto( "ano", $this->ano );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarCalendarioDiaMotivo" ) )
		{
			$objTemp = new clsPmieducarCalendarioDiaMotivo();
			$lista = $objTemp->lista( null,$ref_cod_escola,null,null,null,null,null,null,null,null,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_calendario_dia_motivo']}"] = "{$registro['nm_motivo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarCalendarioDiaMotivo nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_calendario_dia_motivo", "Calendario Dia Motivo", $opcoes, $this->ref_cod_calendario_dia_motivo,"",false,"","",false,false );

	/*	$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarCalendarioAtividade" ) )
		{
			$objTemp = new clsPmieducarCalendarioAtividade();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_calendario_atividade']}"] = "{$registro['nm_atividade']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarCalendarioAtividade nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_calendario_atividade", "Calendario Atividade", $opcoes, $this->ref_cod_calendario_atividade,"",false,"","",false,false );
*/
		$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 30,10,true );

//		$this->array_botao = array('Adicionar Anota&ccedil;&atilde;o');
	//	$this->array_botao_url = array("educar_calendario_anotacao_cad.php?ref_ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}&dia={$this->dia}&mes={$this->mes}");

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7,  "educar_calendario_dia_lst.php" );


		$obj = new clsPmieducarCalendarioDia( $this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_calendario_dia_motivo, /*$this->ref_cod_calendario_atividade, */$this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			//header( "Location: educar_calendario_dia_lst.php" );
//			header( "Location: educar_calendario_ano_letivo_lst.php" );
			header( "Location: educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarCalendarioDia\nvalores obrigatorios\nis_numeric( $this->ref_cod_calendario_ano_letivo ) && is_numeric( $this->mes ) && is_numeric( $this->dia ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_calendario_dia_motivo ) &&  is_string( $this->descricao )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7,  "educar_calendario_dia_lst.php" );


		$obj = new clsPmieducarCalendarioDia($this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_calendario_dia_motivo/*, $this->ref_cod_calendario_atividade*/, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
//			header( "Location: educar_calendario_dia_lst.php" );
			//header( "Location: educar_calendario_ano_letivo_lst.php" );
			header( "Location: educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarCalendarioDia\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_calendario_ano_letivo ) && is_numeric( $this->mes ) && is_numeric( $this->dia ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 620, $this->pessoa_logada, 7,  "educar_calendario_dia_lst.php" );


		$obj = new clsPmieducarCalendarioDia($this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia, $this->pessoa_logada, $this->pessoa_logada, "NULL", /*$this->ref_cod_calendario_atividade,*/ "NULL", $this->data_cadastro, $this->data_exclusao, 1);
		$excluiu = $obj->edita();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarCalendarioDia\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_calendario_ano_letivo ) && is_numeric( $this->mes ) && is_numeric( $this->dia ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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