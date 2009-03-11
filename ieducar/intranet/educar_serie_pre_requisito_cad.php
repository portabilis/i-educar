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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Serie Pre Requisito" );
		$this->processoAp = "599";
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

	var $ref_cod_pre_requisito;
	var $ref_cod_operador;
	var $ref_cod_serie;
	var $valor;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_serie 		 = $_GET["ref_cod_serie"];
		$this->ref_cod_operador 	 = $_GET["ref_cod_operador"];
		$this->ref_cod_pre_requisito = $_GET["ref_cod_pre_requisito"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 599, $this->pessoa_logada, 3,  "educar_serie_pre_requisito_lst.php" );

		if( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )
		{

			$obj = new clsPmieducarSeriePreRequisito( $this->ref_cod_pre_requisito, $this->ref_cod_operador, $this->ref_cod_serie );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 599, $this->pessoa_logada, 3 ) )
			{
				$this->fexcluir = true;
			}

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_serie_pre_requisito_det.php?ref_cod_pre_requisito={$registro["ref_cod_pre_requisito"]}&ref_cod_operador={$registro["ref_cod_operador"]}&ref_cod_serie={$registro["ref_cod_serie"]}" : "educar_serie_pre_requisito_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "ref_cod_pre_requisito", $this->ref_cod_pre_requisito );
		$this->campoOculto( "ref_cod_operador", $this->ref_cod_operador );
		$this->campoOculto( "ref_cod_serie", $this->ref_cod_serie );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$objTemp = new clsPmieducarSerie();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_serie']}"] = "{$registro['nm_serie']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarSerie nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$script = "javascript:showExpansivelIframe(520, 550, 'educar_serie_cad_pop.php?precisa_lista=sim');";
		$script = "<img id='img_colecao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
		$this->campoLista( "ref_cod_serie", "Serie", $opcoes, $this->ref_cod_serie, "", "", "", $script );

		$fim_sentenca = array();
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarOperador" ) )
		{
			$objTemp = new clsPmieducarOperador();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_operador']}"] = "{$registro['nome']}";
					if( $registro["fim_sentenca"] )
					{
						$fim_sentenca[$registro['cod_operador']] = $registro['cod_operador'];
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarOperador nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$javascript = "";
		if( count( $fim_sentenca ) )
		{
			$javascript = "if( this.options[this.selectedIndex].value == " . implode( " || this.options[this.selectedIndex].value == ", $fim_sentenca ) . "){ document.getElementById( 'valor' ).disabled = true; } else { document.getElementById( 'valor' ).disabled = false; }";
		}
		
		$script = "javascript:showExpansivelIframe(520, 400, 'educar_operador_cad_pop.php');";
		$script = "<img id='img_colecao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
		$this->campoLista( "ref_cod_operador", "Operador", $opcoes, $this->ref_cod_operador, $javascript, "", "", $script );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarPreRequisito" ) )
		{
			$objTemp = new clsPmieducarPreRequisito();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_pre_requisito']}"] = "{$registro['nome']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarPreRequisito nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		
		$script = "javascript:showExpansivelIframe(520, 400, 'educar_pre_requisito_cad_pop.php');";
		$script = "<img id='img_colecao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
		$this->campoLista( "ref_cod_pre_requisito", "Pre Requisito", $opcoes, $this->ref_cod_pre_requisito, "", "","", $script );


		// text
		$this->campoTexto( "valor", "Valor", $this->valor, 30, 255, false );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 599, $this->pessoa_logada, 3,  "educar_serie_pre_requisito_lst.php" );


		$obj = new clsPmieducarSeriePreRequisito( $this->ref_cod_pre_requisito, $this->ref_cod_operador, $this->ref_cod_serie, $this->valor );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_serie_pre_requisito_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarSeriePreRequisito\nvalores obrigatorios\nis_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 599, $this->pessoa_logada, 3,  "educar_serie_pre_requisito_lst.php" );


		$obj = new clsPmieducarSeriePreRequisito($this->ref_cod_pre_requisito, $this->ref_cod_operador, $this->ref_cod_serie, $this->valor);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_serie_pre_requisito_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarSeriePreRequisito\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 599, $this->pessoa_logada, 3,  "educar_serie_pre_requisito_lst.php" );


		$obj = new clsPmieducarSeriePreRequisito($this->ref_cod_pre_requisito, $this->ref_cod_operador, $this->ref_cod_serie, $this->valor);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_serie_pre_requisito_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarSeriePreRequisito\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )\n-->";
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