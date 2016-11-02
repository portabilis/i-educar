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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Editora" );
		$this->processoAp = "595";
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

	var $cod_acervo_editora;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $nm_editora;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $telefone;
	var $ddd_telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11,  "educar_acervo_editora_lst.php" );

		return $retorno;
	}

	function Gerar()
	{
		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir')}</script>";
		$this->campoOculto("ref_cod_biblioteca", $this->ref_cod_biblioteca);
		$this->campoTexto( "nm_editora", "Editora", $this->nm_editora, 30, 255, true );

		// foreign keys
		if ($this->cod_acervo_editora)
		{
			$this->cep = int2CEP($this->cep);
		}

		$this->campoCep( "cep", "CEP", $this->cep, false );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUf" ) )
		{
			$objTemp = new clsUf();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_sigla_uf", "Estado", $opcoes, $this->ref_sigla_uf, '', false, '', '', false, false );

		$this->campoTexto( "cidade", "Cidade", $this->cidade, 30, 60, false );
		$this->campoTexto( "bairro", "Bairro", $this->bairro, 30, 60, false );

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
		$this->campoLista( "ref_idtlog", "Tipo Logradouro", $opcoes, $this->ref_idtlog, '', false, '', '', false, false );

		$this->campoTexto( "logradouro", "Logradouro", $this->logradouro, 30, 255, false );

		$this->campoNumero( "numero", "N&uacute;mero", $this->numero, 6, 6 );

		$this->campoNumero( "ddd_telefone", "DDD Telefone", $this->ddd_telefone, 2, 2, false );
		$this->campoNumero( "telefone", "Telefone", $this->telefone, 10, 15, false );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11,  "educar_acervo_editora_lst.php" );

		$this->cep = idFederal2int($this->cep);

		$obj = new clsPmieducarAcervoEditora( null, $this->pessoa_logada, null, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1 , $this->ref_cod_biblioteca);
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			//header( "Location: educar_acervo_editora_lst.php" );
			echo "<script>
					parent.document.getElementById('editora').value = '$cadastrou';
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('ref_cod_acervo_editora').disabled = false;
					parent.document.getElementById('formcadastro').submit();
			     </script>";
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarAcervoEditora\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_string( $this->ref_idtlog ) && is_string( $this->ref_sigla_uf ) && is_string( $this->nm_editora ) && is_numeric( $this->cep ) && is_string( $this->cidade ) && is_string( $this->bairro ) && is_string( $this->logradouro ) && is_numeric( $this->numero )\n-->";
		return false;
	}

	function Editar()
	{
	}

	function Excluir()
	{
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
	document.getElementById('ref_cod_biblioteca').value = parent.document.getElementById('ref_cod_biblioteca').value;
</script>
