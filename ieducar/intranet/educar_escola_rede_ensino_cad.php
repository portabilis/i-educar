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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Escola Rede Ensino" );
		$this->processoAp = "647";
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

	var $cod_escola_rede_ensino;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_rede;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_escola_rede_ensino=$_GET["cod_escola_rede_ensino"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 647, $this->pessoa_logada, 3,  "educar_escola_rede_ensino_lst.php" );

		if( is_numeric( $this->cod_escola_rede_ensino ) )
		{

			$obj = new clsPmieducarEscolaRedeEnsino( $this->cod_escola_rede_ensino );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if( $obj_permissoes->permissao_excluir( 647, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro["cod_escola_rede_ensino"]}" : "educar_escola_rede_ensino_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_escola_rede_ensino", $this->cod_escola_rede_ensino );

		// Filtros de Foreign Keys
		$obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_rede", "Rede Ensino", $this->nm_rede, 30, 255, true );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 647, $this->pessoa_logada, 3,  "educar_escola_rede_ensino_lst.php" );


		$obj = new clsPmieducarEscolaRedeEnsino( null,null,$this->pessoa_logada,$this->nm_rede,null,null,1,$this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_escola_rede_ensino_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarEscolaRedeEnsino\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_rede )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 647, $this->pessoa_logada, 3,  "educar_escola_rede_ensino_lst.php" );


		$obj = new clsPmieducarEscolaRedeEnsino( $this->cod_escola_rede_ensino,$this->pessoa_logada,null,$this->nm_rede,null,null,1,$this->ref_cod_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_escola_rede_ensino_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarEscolaRedeEnsino\nvalores obrigatorios\nif( is_numeric( $this->cod_escola_rede_ensino ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 647, $this->pessoa_logada, 3,  "educar_escola_rede_ensino_lst.php" );


		$obj = new clsPmieducarEscolaRedeEnsino( $this->cod_escola_rede_ensino,$this->pessoa_logada,null,null,null,null,0 );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_escola_rede_ensino_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarEscolaRedeEnsino\nvalores obrigatorios\nif( is_numeric( $this->cod_escola_rede_ensino ) && is_numeric( $this->pessoa_logada ) )\n-->";
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