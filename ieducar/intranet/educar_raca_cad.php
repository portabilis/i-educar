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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Ra&ccedil;a" );
		$this->processoAp = "678";
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

	var $cod_raca;
	var $idpes_exc;
	var $idpes_cad;
	var $nm_raca;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_raca=$_GET["cod_raca"];

		$obj_permissao = new clsPermissoes();
		$obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 3, "educar_raca_lst.php");

		if( is_numeric( $this->cod_raca ) )
		{

			$obj = new clsCadastroRaca( $this->cod_raca );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );


				$this->fexcluir = $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 3);

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_raca_det.php?cod_raca={$registro["cod_raca"]}" : "educar_raca_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_raca", $this->cod_raca );

		// foreign keys
		/*$opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );
		if( $this->idpes_exc )
		{
			$objTemp = new clsPessoaFisica( $this->idpes_exc );
			$detalhe = $objTemp->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "idpes_exc", "idpes", "nome" );
		$parametros->setPessoa( "F" );
		$parametros->setPessoaNovo( 'S' );
		$parametros->setPessoaEditar( 'N' );
		$parametros->setPessoaTela( "frame" );
		$parametros->setPessoaCPF('N');
//		$parametros->setCodSistema(0);
		$this->campoListaPesq( "idpes_exc", "Idpes Exc", $opcoes, $this->idpes_exc, "pesquisa_pessoa_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
		$opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );
		if( $this->idpes_cad )
		{
			$objTemp = new clsPessoaFisica( $this->idpes_cad );
			$detalhe = $objTemp->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "idpes_cad", "idpes", "nome" );
		$parametros->setPessoa( "F" );
		$parametros->setPessoaNovo( 'S' );
		$parametros->setPessoaEditar( 'N' );
		$parametros->setPessoaTela( "frame" );
		$parametros->setPessoaCPF('N');
//		$parametros->setCodSistema(0);
		$this->campoListaPesq( "idpes_cad", "Idpes Cad", $opcoes, $this->idpes_cad, "pesquisa_pessoa_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
*/
		// text
		$this->campoTexto( "nm_raca", "Ra&ccedil;a", $this->nm_raca, 30, 255, true );

		// data

		// time

		// bool
		//$this->campoBoolLista( "ativo", "Ativo", $this->ativo );
		//$this->campoCheck( "ativo", "Ativo", ( $this->ativo == 't' ) );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsCadastroRaca( $this->cod_raca, null, $this->pessoa_logada, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, $this->ativo );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_raca_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsCadastroRaca\nvalores obrigatorios\nis_numeric( $this->idpes_cad ) && is_string( $this->nm_raca )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsCadastroRaca($this->cod_raca, $this->pessoa_logada, null, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_raca_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsCadastroRaca\nvalores obrigatorios\nif( is_numeric( $this->cod_raca ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsCadastroRaca($this->cod_raca, $this->pessoa_logada, null, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_raca_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsCadastroRaca\nvalores obrigat&otilde;rios\nif( is_numeric( $this->cod_raca ) )\n-->";
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