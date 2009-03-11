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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Avalia&ccedil;&atilde;o" );
		$this->processoAp = "560";
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
	var $incluir;
	var $excluir_;
	var $retorno;

	var $cod_tipo_avaliacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $conceitual;

	var $ref_cod_instituicao;

	var $valores_avaliacao;
	var $valores_adicionados;
	var $valores_removidos;

	var $nome;
	var $valor;
	var $valor_min;
	var $valor_max;

	function Inicializar()
	{
		$this->retorno = "Novo";
		@session_start();
		$this->pessoa_logada 	  = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 560, $this->pessoa_logada, 3, "educar_instituicao_lst.php" );

		$this->cod_tipo_avaliacao = $_GET["cod_tipo_avaliacao"];

		if( is_numeric( $this->cod_tipo_avaliacao ) )
		{
			$obj = new clsPmieducarTipoAvaliacao( $this->cod_tipo_avaliacao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir = $obj_permissoes->permissao_excluir( 560, $this->pessoa_logada, 3 );
				$this->retorno = "Editar";
			}
		}
		$this->url_cancelar = ($this->retorno == "Editar") ? "educar_tipo_avaliacao_det.php?cod_tipo_avaliacao={$registro["cod_tipo_avaliacao"]}" : "educar_tipo_avaliacao_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $this->retorno;
	}

	function Gerar()
	{
		$this->nm_tipo    = ( $this->nm_tipo ) ? $this->nm_tipo : $_POST["nm_tipo"];
		$this->conceitual = ( $this->conceitual ) ? $this->conceitual : $_POST["conceitual"];
		$this->excluir_	  = ( $this->excluir_ ) ? $this->excluir_ : $_POST["excluir_"];
		if ( $_POST["valores_avaliacao"] )
			$this->valores_avaliacao = unserialize( urldecode( $_POST["valores_avaliacao"] ) );
		if ( $_POST["ref_cod_instituicao"] )
			$this->ref_cod_instituicao = $_POST["ref_cod_instituicao"];
		$qtd_valores = ( count( $this->valores_avaliacao ) == 0 ) ? 1 : ( count( $this->valores_avaliacao ) + 1);

		if( is_numeric( $this->cod_tipo_avaliacao ) && $_POST["incluir"] != 'S' && empty( $_POST["excluir_"] ) )
		{
			$obj = new clsPmieducarTipoAvaliacaoValores( $this->cod_tipo_avaliacao );
			$obj->setOrderby( "valor" );
			$registros = $obj->lista( $this->cod_tipo_avaliacao );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					/*$this->valores_avaliacao[$campo[$qtd_valores]]["sequencial_"] = $campo["sequencial"];
					$this->valores_avaliacao[$campo[$qtd_valores]]["nome_"] 	  = $campo["nome"];
					$this->valores_avaliacao[$campo[$qtd_valores]]["valor_"] 	  = $campo["valor"];
					$this->valores_avaliacao[$campo[$qtd_valores]]["valor_min_"]  = $campo["valor_min"];
					$this->valores_avaliacao[$campo[$qtd_valores]]["valor_max_"]  = $campo["valor_max"];
					$qtd_valores++;*/
					$this->valores_avaliacao[$campo["sequencial"]]["sequencial_"] = $campo["sequencial"];
					$this->valores_avaliacao[$campo["sequencial"]]["nome_"] 	  = $campo["nome"];
					$this->valores_avaliacao[$campo["sequencial"]]["valor_"] 	  = $campo["valor"];
					$this->valores_avaliacao[$campo["sequencial"]]["valor_min_"]  = $campo["valor_min"];
					$this->valores_avaliacao[$campo["sequencial"]]["valor_max_"]  = $campo["valor_max"];
				}
			}
		}

		if ( is_string($_POST["nome"]) && $_POST["valor"] && $_POST["valor_min"] && $_POST["valor_max"] )
		{
			$this->valores_avaliacao[$qtd_valores]["sequencial_"] = $qtd_valores;
			$this->valores_avaliacao[$qtd_valores]["nome_"] 	  = $_POST["nome"];
			$this->valores_avaliacao[$qtd_valores]["valor_"] 	  = $_POST["valor"];
			$this->valores_avaliacao[$qtd_valores]["valor_min_"]  = $_POST["valor_min"];
			$this->valores_avaliacao[$qtd_valores]["valor_max_"]  = $_POST["valor_max"];
			$qtd_valores++;
			unset( $this->nome );
			unset( $this->valor );
			unset( $this->valor_min );
			unset( $this->valor_max );
		}

		// primary keys
		$this->campoOculto( "cod_tipo_avaliacao", $this->cod_tipo_avaliacao );
		$get_escola = false;
		$obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");
		// foreign keys

		// text
		$this->campoTexto( "nm_tipo", "Tipo da Avaliação", $this->nm_tipo, 30, 255, true );
		$this->campoCheck( "conceitual", "Conceitual", $this->conceitual );
		$this->campoOculto( "excluir_", "" );
		$qtd_valores = 1;
		$aux;

		$this->campoQuebra();

		if ( $this->valores_avaliacao )
		{
			foreach ( $this->valores_avaliacao AS $campo )
			{
				if ( $this->excluir_ == $campo["sequencial_"] )
				{
					$this->valores_avaliacao[$campo["sequencial"]] = null;
					$this->excluir_								   = null;
				}
				else
				{
					$this->campoTextoInv( "nome_{$campo["sequencial_"]}", "Avaliação {$campo["sequencial_"]}", $campo["nome_"], 30, 255, false, false, true );
					$this->campoTextoInv( "valor_{$campo["sequencial_"]}", "", $campo["valor_"], 5, 5, false, false, true );
					$this->campoTextoInv( "valor_min_{$campo["sequencial_"]}", "", $campo["valor_min_"], 5, 5, false, false, true );
					$this->campoTextoInv( "valores_max_{$campo["sequencial_"]}", "", $campo["valor_max_"], 5, 5, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_').value = '{$campo["sequencial_"]}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
					$aux[$qtd_valores]["sequencial_"] = $qtd_valores;
					$aux[$qtd_valores]["nome_"] 	  = $campo["nome_"];
					$aux[$qtd_valores]["valor_"] 	  = $campo["valor_"];
					$aux[$qtd_valores]["valor_min_"]  = $campo["valor_min_"];
					$aux[$qtd_valores]["valor_max_"]  = $campo["valor_max_"];
					$qtd_valores++;
				}

			}
			unset($this->valores_avaliacao);
			$this->valores_avaliacao = $aux;
		}
		$this->campoOculto( "valores_avaliacao", serialize( $this->valores_avaliacao ) );

		if ( $qtd_valores > 1 ) {
			$this->campotexto( "nome", "Nome da Avaliação", $this->nome, 30, 255 );
			$this->campoMonetario( "valor", "Nota", $this->valor, 5, 5 );
			$this->campoMonetario( "valor_min", "Nota Mínima", $this->valor_min, 5, 5 );
			$this->campoMonetario( "valor_max", "Nota Máxima", $this->valor_max, 5, 5 );
		}
		else {
			$this->campotexto( "nome", "Nome da Avaliação", $this->nome, 30, 255, true );
			$this->campoMonetario( "valor", "Nota", $this->valor, 5, 5, true );
			$this->campoMonetario( "valor_min", "Nota Mínima", $this->valor_min, 5, 5, true );
			$this->campoMonetario( "valor_max", "Nota Máxima", $this->valor_max, 5, 5, true );
		}
		$this->campoOculto( "incluir", "" );
		$this->campoRotulo( "bt_incluir", "Incluir Valores", "<a href='#' onclick=\"getElementById('incluir').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>" );

		$this->campoQuebra();
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		if ( $this->nm_tipo && $this->valores_avaliacao && $this->incluir != 'S' && empty( $this->excluir_ ) ) {
			$this->conceitual = ( $this->conceitual == "on" ) ? 1 : 0;
			$obj 	   = new clsPmieducarTipoAvaliacao( $this->cod_tipo_avaliacao, $this->ref_usuario_exc, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 1, $this->conceitual, $this->ref_cod_instituicao );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				$this->valores_avaliacao = unserialize( urldecode( $this->valores_avaliacao ) );
				foreach ( $this->valores_avaliacao AS $campo ) {
					$campo["valor_"] 	 = str_replace( ',', '.', $campo["valor_"] );
					$campo["valor_min_"] = str_replace( ',', '.', $campo["valor_min_"] );
					$campo["valor_max_"] = str_replace( ',', '.', $campo["valor_max_"] );
					$obj 	   			 = new clsPmieducarTipoAvaliacaoValores( $cadastrou, $campo["sequencial_"], $campo["nome_"], $campo["valor_"], $campo["valor_min_"], $campo["valor_max_"] );
					$cadastrou2 		 = $obj->cadastra();
					if ( !$cadastrou2 ) {
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarTipoAvaliacaoValores\nvalores obrigatorios\nis_numeric( $cadastrou ) && is_numeric( {$campo["sequencial_"]} ) && is_string( {$campo["nome_"]} ) && is_numeric( {$campo["valor_"]} ) && is_numeric( {$campo["valor_min_"]} ) && is_numeric( {$campo["valor_max_"]} )\n-->";
						return false;
					}
				}
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_tipo_avaliacao_lst.php" );
				die();
				return true;
			}

			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarTipoAvaliacao\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo ) && is_numeric( 1 )\n-->";
			return false;
		}
		return true;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		if ( $this->nm_tipo && $this->valores_avaliacao && $this->incluir != 'S' && empty( $this->excluir_ ) ) {
			$this->conceitual = ( $this->conceitual == "on" ) ? 1 : 0;
			$obj    = new clsPmieducarTipoAvaliacao( $this->cod_tipo_avaliacao, $this->pessoa_logada, $this->ref_usuario_cad, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 1, $this->conceitual, $this->ref_cod_instituicao );
			$editou = $obj->edita();

			if( $editou ) {
				$this->valores_avaliacao = unserialize( urldecode( $this->valores_avaliacao ) );
				$obj 	 = new clsPmieducarTipoAvaliacaoValores( $this->cod_tipo_avaliacao );
				$excluiu = $obj->excluirTodos();
				if ( $excluiu ) {

					foreach ( $this->valores_avaliacao AS $campo ) {
						$campo["valor_"] 	 = str_replace( ',', '.', $campo["valor_"] );
						$campo["valor_min_"] = str_replace( ',', '.', $campo["valor_min_"] );
						$campo["valor_max_"] = str_replace( ',', '.', $campo["valor_max_"] );
						$obj 	   			 = new clsPmieducarTipoAvaliacaoValores( $this->cod_tipo_avaliacao, $campo["sequencial_"], $campo["nome_"], $campo["valor_"], $campo["valor_min_"], $campo["valor_max_"],true );
						//$cadastrou = true;
						if(!$obj->existe())
							$cadastrou 			 = $obj->cadastra();
						else
							$cadastrou 			 = $obj->edita();
						if ( !$cadastrou ) {
							$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
							echo "<!--\nErro ao editar clsPmieducarTipoAvaliacaoValores\nvalores obrigatorios\nis_numeric( $this->cod_tipo_avaliacao ) && is_numeric( {$campo["sequencial_"]} ) && is_string( {$campo["nome_"]} ) && is_numeric( {$campo["valor_"]} ) && is_numeric( {$campo["valor_min_"]} ) && is_numeric( {$campo["valor_max_"]} )\n-->";
							return false;
						}
					}
					$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_tipo_avaliacao_det.php?cod_tipo_avaliacao={$this->cod_tipo_avaliacao}" );
					die();
					return true;
				}
			}

			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarTipoAvaliacao\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_avaliacao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
			return false;
		}
		return true;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTipoAvaliacao($this->cod_tipo_avaliacao, $this->pessoa_logada, $this->ref_usuario_cad, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 0,null,$this->ref_cod_instituicao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_tipo_avaliacao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarTipoAvaliacao\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_avaliacao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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