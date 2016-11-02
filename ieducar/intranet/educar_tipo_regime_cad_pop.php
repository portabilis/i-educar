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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Regime" );
		$this->processoAp = "568";
		$this->renderBanner = false;
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

	var $cod_tipo_regime;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
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



		$this->cod_tipo_regime=$_GET["cod_tipo_regime"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 568, $this->pessoa_logada,3, "educar_tipo_regime_lst.php" );

		if( is_numeric( $this->cod_tipo_regime ) )
		{

			$obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;


				//** verificao de permissao para exclusao
				$this->fexcluir = $obj_permissoes->permissao_excluir(568,$this->pessoa_logada,3);
				//**
				$retorno = "Editar";
			}
		}
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_regime_det.php?cod_tipo_regime={$registro["cod_tipo_regime"]}" : "educar_tipo_regime_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_tipo_regime", $this->cod_tipo_regime );

		// foreign keys
		// foreign keys
		if ($_GET['precisa_lista'])
		{
			$get_escola = false;
			$obrigatorio = true;
			include("include/pmieducar/educar_campo_lista.php");
		}// text
		else 
		{
			$this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
		}
		$this->campoTexto( "nm_tipo", "Nome Tipo", $this->nm_tipo, 30, 255, true );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			echo "<script>
						if (parent.document.getElementById('ref_cod_tipo_regime').disabled)
							parent.document.getElementById('ref_cod_tipo_regime').options[0] = new Option('Selecione um tipo de regime', '', false, false);
						parent.document.getElementById('ref_cod_tipo_regime').options[parent.document.getElementById('ref_cod_tipo_regime').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
						parent.document.getElementById('ref_cod_tipo_regime').value = '$cadastrou';
						parent.document.getElementById('ref_cod_tipo_regime').disabled = false;
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
			die();
//			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
//			header( "Location: educar_tipo_regime_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarTipoRegime\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_tipo )\n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_tipo_regime_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarTipoRegime\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_regime ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 0, $this->ref_cod_instituicao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_tipo_regime_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarTipoRegime\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_regime ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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

<script>

<?

if (!$_GET['precisa_lista'])
{
?>
	Event.observe(window, 'load', Init, false);

	function Init() 
	{
		$('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
	}
	
<?
}
?>

</script>