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
require_once("include/pmieducar/clsPmieducarCategoriaObra.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Categoria obras" );
		$this->processoAp = "598";
		$this->addEstilo('localizacaoSistema');
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

	var $id;
	var $descricao;
	var $observacoes;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->id = $_GET["id"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11,  "educar_categoria_lst.php");

		if(is_numeric($this->id)){
			$obj = new clsPmieducarCategoriaObra($this->id);
			$registro = $obj->detalhe();
			if($registro){
				//passa todos os valores obtidos no registro para atributos do objeto
				foreach($registro AS $campo => $val){
					$this->$campo = $val;
				}

				$obj_permissoes = new clsPermissoes();
				if($obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11)){
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_categoria_obra_det.php?id={$registro["id"]}" : "educar_categoria_lst.php";
		$this->nome_url_cancelar = "Cancelar";

    	$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    	$localizacao = new LocalizacaoSistema();
    	$localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         										  "educar_biblioteca_index.php" => "i-Educar - Categoria",
         																	 "" => "{$nomeMenu} Obra"));
    	$this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar(){
		$this->campoOculto("id", $this->id);
		$this->campoTexto("descricao", "Descri&ccedil;&atilde;o", $this->descricao, 30, 255, true);
		$this->campoMemo("observacoes", "Observa&ccedil;&otilde;es", $this->observacoes, 60, 5, false );
	}

	function Novo(){
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11,  "educar_categoria_lst.php" );

		$obj = new clsPmieducarCategoriaObra(0, $this->descricao, $this->observacoes);
		$cadastrou = $obj->cadastra();
		if($cadastrou){
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_categoria_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarCategoriaObra\nvalores obrigat&oacute;rios\nis_string( $this->descricao )\n-->";
		return false;
	}

	function Editar(){
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11, "educar_categoria_lst.php");

		$obj = new clsPmieducarCategoriaObra($this->id, $this->descricao, $this->observacoes);
		$editou = $obj->edita();
		if( $editou ){
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_categoria_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarCategoriaObra\nvalores obrigat&oacute;rios\nif( is_numeric( $this->id ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir(){
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11,  "educar_categoria_lst.php");

		$obj = new clsPmieducarCategoriaObra($this->id);
		$excluiu = $obj->excluir();
		if($excluiu){
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_categoria_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "N&atilde;o &eacute; poss&iacute;vel excluir esta categoria. Verifique se a mesma possui v&iacute;nculo com obras.<br>";
		echo "<!--\nErro ao excluir clsPmieducarCategoriaObra\nvalores obrigat&oacute;rios\nif( is_numeric( $this->id ) && is_numeric( $this->pessoa_logada ) )\n-->";
		$this->array_botao[] = 'Voltar';
		$this->array_botao_url_script[] = "go('educar_categoria_obra_det.php?id=". $this->id ."')";
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