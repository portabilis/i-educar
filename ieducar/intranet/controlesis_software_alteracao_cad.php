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
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - Cadastro de Altera&ccedil;&atilde;o de Software" );
		$this->processoAp = "794";
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

	var $cod_software_alteracao;
	var $ref_funcionario_exc;
	var $ref_funcionario_cad;
	var $ref_cod_software;
	var $motivo;
	var $tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $script_banco;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_software_alteracao=$_GET["cod_software_alteracao"];


		if( is_numeric( $this->cod_software_alteracao ) )
		{

			$obj = new clsPmicontrolesisSoftwareAlteracao( $this->cod_software_alteracao );
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
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_software_alteracao_det.php?cod_software_alteracao={$registro["cod_software_alteracao"]}" : "controlesis_software_alteracao_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_software_alteracao", $this->cod_software_alteracao );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmicontrolesisSoftware" ) )
		{
			$objTemp = new clsPmicontrolesisSoftware();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_software']}"] = "{$registro['nm_software']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmicontrolesisSoftware nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_software", "Software", $opcoes, $this->ref_cod_software );

		$this->campoLista( "motivo", "Motivo", array('' => 'Selecione','i' => 'Inserção','a' => 'Alteração','e' => 'Exclusão'), $this->motivo );

		$this->campoLista( "tipo", "Tipo", array('' => 'Selecione','s' => 'Script','b' => 'Banco'), $this->tipo );

		$this->campoTexto("script_banco","Nome do Script/Banco",$this->script_banco,30,100,true);

		$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 10, true );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsPmicontrolesisSoftwareAlteracao( $this->cod_software_alteracao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_software, $this->motivo, $this->tipo, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->script_banco );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: controlesis_software_alteracao_cad.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisSoftwareAlteracao\nvalores obrigatorios\nis_numeric( $this->ref_funcionario_cad ) && is_numeric( $this->ref_cod_software ) && is_string( $this->motivo ) && is_string( $this->tipo ) && is_string( $this->descricao ) && is_string( $this->script_banco )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsPmicontrolesisSoftwareAlteracao($this->cod_software_alteracao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_software, $this->motivo, $this->tipo, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->script_banco);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_software_alteracao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisSoftwareAlteracao\nvalores obrigatorios\nif( is_numeric( $this->cod_software_alteracao ) && is_numeric( $this->ref_funcionario_exc ) && is_string( $this->script_banco ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsPmicontrolesisSoftwareAlteracao($this->cod_software_alteracao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_software, $this->motivo, $this->tipo, $this->descricao, $this->data_cadastro, $this->data_exclusao, 0, $this->script_banco);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_software_alteracao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisSoftwareAlteracao\nvalores obrigatorios\nif( is_numeric( $this->cod_software_alteracao ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
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