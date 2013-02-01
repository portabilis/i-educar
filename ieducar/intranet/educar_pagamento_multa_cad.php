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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Pagamento Multa" );
		$this->processoAp = "622";
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

	var $cod_pagamento_multa;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $nm_pessoa;
	var $ref_idpes;
	var $valor_pago_bib;
	var $valor_divida;
	var $valor_divida_bib;
	var $valor_pagamento;
	var $data_cadastro;
	var $ref_cod_biblioteca;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_cliente 	  = $_GET["cod_cliente"];
		$this->ref_cod_biblioteca = $_GET["cod_biblioteca"];

		if(!$this->ref_cod_cliente || !$this->ref_cod_biblioteca)
			header("Location: educar_pagamento_multa_lst.php");

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 622, $this->pessoa_logada, 11,  "educar_pagamento_multa_lst.php" );

		if ( is_numeric( $this->ref_cod_cliente ) ) {
			$obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
			$lst_exemplar_emprestimo = $obj_exemplar_emprestimo->listaDividaPagamentoCliente( $this->ref_cod_cliente, null, $this->ref_cod_cliente_tipo, $this->pessoa_logada, $this->ref_cod_biblioteca, $this->ref_cod_escola, $this->ref_cod_instituicao );
			if ( $lst_exemplar_emprestimo ) {
				foreach ( $lst_exemplar_emprestimo as $registro ) {
					if ( is_numeric( $registro["valor_multa"] ) )
						$this->valor_divida_bib = $registro["valor_multa"];
					else
						$this->valor_divida_bib = 0;
					if ( is_numeric( $registro["valor_pago"] ) )
						$this->valor_pago_bib = $registro["valor_pago"];
					else
						$this->valor_pago_bib = 0;
				}
			}
			$obj_cliente 	 = new clsPmieducarCliente( $this->ref_cod_cliente );
			$det_cliente 	 = $obj_cliente->detalhe();
			if ( $det_cliente ) {
				$this->ref_idpes = $det_cliente["ref_idpes"];
				$obj_pessoa 	 = new clsPessoa_( $this->ref_idpes );
				$det_pessoa 	 = $obj_pessoa->detalhe();
				if ( $det_pessoa )
					$this->nm_pessoa = $det_pessoa["nome"];
				$obj_divida = new clsPmieducarExemplarEmprestimo( null, null, null, $this->ref_cod_cliente );
				$det_divida = $obj_divida->clienteDividaTotal( $this->ref_idpes, $this->ref_cod_cliente );
				if ( $det_divida ) {
					foreach ( $det_divida as $divida )
						$this->valor_divida = $divida["valor_multa"];
				}
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_pagamento_multa_det.php?cod_cliente={$this->ref_cod_cliente}" : "educar_pagamento_multa_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->nome_url_sucesso  = "Pagar";
		$this->acao_enviar       = "validaValor()";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "ref_cod_cliente", $this->ref_cod_cliente );

		$this->campoOculto( "ref_cod_biblioteca", $this->ref_cod_biblioteca );

		$this->campoRotulo( "nm_cliente", "Cliente", $this->nm_pessoa );

		$this->campoMonetario( "valor_divida", "Total da Dívida", $this->valor_divida, 11, 11, false, "", "", "onChange", true );

		$this->campoMonetario( "valor_divida_bib", "Total da Dívida (Biblioteca)", $this->valor_divida_bib, 11, 11, false, "", "", "onChange", true );

		$this->campoMonetario( "valor_pago_bib", "Valor Pago (Biblioteca)", $this->valor_pago_bib, 11, 11, false, "", "", "onChange", true );

		$this->campoMonetario( "valor_pagamento", "Valor do Pagamento", $this->valor_pagamento, 11, 11, true );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 622, $this->pessoa_logada, 11,  "educar_pagamento_multa_lst.php" );

		$this->valor_pagamento = str_replace( ",", ".", $this->valor_pagamento );
		$obj = new clsPmieducarPagamentoMulta( null, $this->pessoa_logada, $this->ref_cod_cliente, $this->valor_pagamento, null, $this->ref_cod_biblioteca );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_pagamento_multa_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarPagamentoMulta\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_cliente ) && is_numeric( $this->valor_pago )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 622, $this->pessoa_logada, 11,  "educar_pagamento_multa_lst.php" );


		$obj = new clsPmieducarPagamentoMulta( $this->cod_pagamento_multa, null, $this->ref_cod_cliente, $this->valor_pago, null, $this->ref_cod_biblioteca );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_pagamento_multa_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarPagamentoMulta\nvalores obrigatorios\nif( is_numeric( $this->cod_pagamento_multa ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 622, $this->pessoa_logada, 11,  "educar_pagamento_multa_lst.php" );


		$obj = new clsPmieducarPagamentoMulta( $this->cod_pagamento_multa, null, $this->ref_cod_cliente, $this->valor_pago, null, $this->ref_cod_biblioteca );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_pagamento_multa_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarPagamentoMulta\nvalores obrigatorios\nif( is_numeric( $this->cod_pagamento_multa ) )\n-->";
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
<script>
	function validaValor()
	{
		var valor_divida_bib;
		var valor_pago_bib;
		var valor_pagamento;

		if ( document.getElementById('valor_divida_bib') )
			valor_divida_bib = document.getElementById('valor_divida_bib').value;
		if ( document.getElementById('valor_pago_bib') )
			valor_pago_bib   = document.getElementById('valor_pago_bib').value;
		if ( document.getElementById('valor_pagamento') )
			valor_pagamento  = document.getElementById('valor_pagamento').value;

		if ( ( valor_divida_bib.replace(",", ".") - valor_pago_bib.replace(",", ".") ) < valor_pagamento.replace(",", ".") ) {
			alert( "O valor de pagamento deve ser inferior ou igual ao valor devido na respectiva biblioteca." );
			valor_pagamento  = document.getElementById('valor_pagamento').value = "";
			return;
		}
		else
		{
			document.formcadastro.submit();
		}
	}
</script>