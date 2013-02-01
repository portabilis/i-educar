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
require_once ("include/Geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Institui&ccedil;&atilde;o" );
		$this->processoAp = "559";
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

	var $cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $complemento;
	var $nm_responsavel;
	var $ddd_telefone;
	var $telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_instituicao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 559, $this->pessoa_logada, 1, "educar_instituicao_lst.php" );

		$this->cod_instituicao=$_GET["cod_instituicao"];

		if( is_numeric( $this->cod_instituicao ) )
		{

			$obj = new clsPmieducarInstituicao( $this->cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

				$this->fexcluir = $obj_permissoes->permissao_excluir( 559, $this->pessoa_logada, 1 );
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_instituicao_det.php?cod_instituicao={$registro["cod_instituicao"]}" : "educar_instituicao_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_instituicao", $this->cod_instituicao );

		// text
		$this->campoTexto( "nm_instituicao", "Nome da Instituição", $this->nm_instituicao, 30, 255, true );
		$this->campoCep( "cep", "CEP", int2CEP( $this->cep ), true, "-", false, false );
		$this->campoTexto( "logradouro", "Logradouro", $this->logradouro, 30, 255, true );
		$this->campoTexto( "bairro", "Bairro", $this->bairro, 30, 40, true );
		$this->campoTexto( "cidade", "Cidade", $this->cidade, 30, 60, true );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsTipoLogradouro" ) )
		{
			$objTemp = new clsTipoLogradouro();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUrbanoTipoLogradouro nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_idtlog", "Tipo do Logradouro", $opcoes, $this->ref_idtlog, "", false, "", "", false, true );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUf" ) )
		{
			$objTemp = new clsUf();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				asort($lista);
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['sigla_uf']}"] = "{$registro['sigla_uf']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_sigla_uf", "UF", $opcoes, $this->ref_sigla_uf, "", false, "", "", false, true );

		$this->campoNumero( "numero", "Número", $this->numero, 6, 6 );
		$this->campoTexto( "complemento", "Complemento", $this->complemento, 30, 50, false );
		$this->campoTexto( "nm_responsavel", "Nome do Responsável", $this->nm_responsavel, 30, 255, true );
		$this->campoNumero( "ddd_telefone", "DDD Telefone", $this->ddd_telefone, 2, 2 );
		$this->campoNumero( "telefone", "Telefone", $this->telefone, 11, 11 );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInstituicao( null, $this->ref_usuario_exc, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, str_replace( "-", "", $this->cep ), $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, 1, $this->nm_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarInstituicao\nvalores obrigatorios\nis_numeric( $ref_usuario_cad ) && is_string( $ref_idtlog ) && is_string( $ref_sigla_uf ) && is_numeric( $cep ) && is_string( $cidade ) && is_string( $bairro ) && is_string( $logradouro ) && is_string( $nm_responsavel ) && is_string( $data_cadastro ) && is_numeric( $ativo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPmieducarInstituicao( $this->cod_instituicao, $this->ref_usuario_exc, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, str_replace( "-", "", $this->cep ), $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, 1, $this->nm_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarInstituicao\nvalores obrigatorios\nif( is_numeric( $this->cod_instituicao ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInstituicao($this->cod_instituicao, $this->pessoa_logada, $this->ref_usuario_cad, $this->ref_idtlog, $this->ref_sigla_uf, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarInstituicao\nvalores obrigatorios\nif( is_numeric( $this->cod_instituicao ) )\n-->";
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