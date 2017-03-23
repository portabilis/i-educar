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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Itinerario" );
		$this->processoAp = "614";
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

	var $cod_itinerario;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $numero;
	var $itinerario;
	var $retorno;
	var $horarios;
	var $descricao_horario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nome;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_itinerario=$_GET["cod_itinerario"];


		if( is_numeric( $this->cod_itinerario ) )
		{

			$obj = new clsPmicontrolesisItinerario( $this->cod_itinerario );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_itinerario_det.php?cod_itinerario={$registro["cod_itinerario"]}" : "controlesis_itinerario_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_itinerario", $this->cod_itinerario );

		// foreign keys

		// text
		$this->campoTexto( "nome", "Nome", $this->nome, 30, 255, true );
		$this->campoTexto( "numero", "Numero", $this->numero, 30, 255, false );
		$this->campoMemo( "itinerario", "Itinerario", $this->itinerario, 60, 10, false );
		$this->campoMemo( "retorno", "Retorno", $this->retorno, 60, 10, false );
		$this->campoMemo( "horarios", "Horarios", $this->horarios, 60, 10, false );
		$this->campoMemo( "descricao_horario", "Descric&atilde;o Horario", $this->descricao_horario, 60, 10, false );


		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisItinerario( $this->cod_itinerario, $this->pessoa_logada, $this->pessoa_logada, $this->numero, $this->itinerario, $this->retorno, $this->horarios, $this->descricao_horario, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->nome );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: controlesis_itinerario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisItinerario\nvalores obrigatorios\nis_numeric( $this->ref_funcionario_cad ) && is_string( $this->nome )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisItinerario($this->cod_itinerario, $this->pessoa_logada, $this->pessoa_logada, $this->numero, $this->itinerario, $this->retorno, $this->horarios, $this->descricao_horario, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->nome);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_itinerario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisItinerario\nvalores obrigatorios\nif( is_numeric( $this->cod_itinerario ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisItinerario($this->cod_itinerario, $this->pessoa_logada, $this->pessoa_logada, $this->numero, $this->itinerario, $this->retorno, $this->horarios, $this->descricao_horario, $this->data_cadastro, $this->data_exclusao, 0, $this->nome);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_itinerario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisItinerario\nvalores obrigatorios\nif( is_numeric( $this->cod_itinerario ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
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