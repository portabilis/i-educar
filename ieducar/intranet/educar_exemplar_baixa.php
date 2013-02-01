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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar" );
		$this->processoAp = "606";
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

	var $cod_exemplar;
	var $ref_cod_fonte;
	var $ref_cod_motivo_baixa;
	var $ref_cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $permite_emprestimo;
	var $preco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $data_aquisicao;

	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_biblioteca;
	var $ref_cod_acervo;
	
	var $nm_biblioteca;

	function Inicializar()
	{
		//$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_exemplar=$_GET["cod_exemplar"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

		if( is_numeric( $this->cod_exemplar ) )
		{

			$obj = new clsPmieducarExemplar( $this->cod_exemplar );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_obra = new clsPmieducarAcervo($this->ref_cod_acervo);
				$det_obra = $obj_obra->detalhe();

				$obj_biblioteca = new clsPmieducarBiblioteca($det_obra["ref_cod_biblioteca"]);
				$obj_det = $obj_biblioteca->detalhe();

				$this->ref_cod_biblioteca = $det_obra["ref_cod_biblioteca"];
				$this->ref_cod_acervo = $det_obra["titulo"];


				//$this->ref_cod_instituicao = $obj_det["nm_biblioteca"];
				//$this->ref_cod_escola = $obj_det["ref_cod_escola"];
				$this->nm_biblioteca = $obj_det["nm_biblioteca"];


				//$this->data_aquisicao = dataFromPgToBr( $this->data_aquisicao );

			/*$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11 ) )
			{
				$this->fexcluir = true;
			}*/

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}" : "educar_exemplar_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_exemplar", $this->cod_exemplar );

		$this->campoRotulo("biblioteca","Biblioteca",$this->nm_biblioteca);
		$this->campoRotulo("obra","Obra",$this->ref_cod_acervo);



		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarMotivoBaixa" ) )
		{
			$objTemp = new clsPmieducarMotivoBaixa();
			$lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_motivo_baixa']}"] = "{$registro['nm_motivo_baixa']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMotivoBaixa nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_motivo_baixa", "Motivo Baixa", $opcoes, $this->ref_cod_motivo_baixa );

		$this->nome_url_sucesso = "Efetuar Baixa";
		$this->acao_enviar = "if(confirm(\"Deseja baixar este exemplar?\"))acao();";

	}

	function Novo()
	{
/*		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

		$this->preco = str_replace(".","",$this->preco);
		$this->preco = str_replace(",",".",$this->preco);

		$obj = new clsPmieducarExemplar( $this->cod_exemplar, null, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_exemplar_lst.php" );
			die();
			return true;
		}
*/
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarExemplar\nvalores obrigatorios\nis_numeric( $this->ref_cod_fonte ) && is_numeric( $this->ref_cod_acervo ) && is_numeric( $this->ref_cod_situacao ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->permite_emprestimo ) && is_numeric( $this->preco )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

		$this->preco = str_replace(".","",$this->preco);
		$this->preco = str_replace(",",".",$this->preco);

		$obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_exemplar_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarExemplar\nvalores obrigatorios\nif( is_numeric( $this->cod_exemplar ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
	/*	@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );


		$obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, 0, $this->data_aquisicao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_exemplar_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarExemplar\nvalores obrigatorios\nif( is_numeric( $this->cod_exemplar ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;*/
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
