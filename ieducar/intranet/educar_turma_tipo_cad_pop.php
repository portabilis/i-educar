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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Turma Tipo" );
		$this->processoAp = "570";
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

	var $cod_turma_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $sgl_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;
	var $ref_cod_escola;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_turma_tipo=$_GET["cod_turma_tipo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 570, $this->pessoa_logada,7, "educar_turma_tipo_lst.php" );

		if( is_numeric( $this->cod_turma_tipo ) )
		{

			$obj = new clsPmieducarTurmaTipo( $this->cod_turma_tipo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				//$obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_cod_escola );
				//$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
				//$this->ref_cod_instituicao = $det_ref_cod_escola["ref_cod_instituicao"];

				$this->fexcluir = $obj_permissoes->permissao_excluir( 570, $this->pessoa_logada,7 );
				$retorno = "Editar";
			}
		}
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_turma_tipo_det.php?cod_turma_tipo={$registro["cod_turma_tipo"]}" : "educar_turma_tipo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_turma_tipo", $this->cod_turma_tipo );

		if ($_GET['precisa_lista'])
		{
			$obrigatorio = true;
			// foreign keys
			$get_escola = false;
			include("include/pmieducar/educar_campo_lista.php");
		}
		else 
		{
			$this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
		}
		// text
		$this->campoTexto( "nm_tipo", "Turma Tipo", $this->nm_tipo, 30, 255, true );
		$this->campoTexto( "sgl_tipo", "Sigla", $this->sgl_tipo, 15, 15, true );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTurmaTipo( null, null, $this->pessoa_logada, $this->nm_tipo, $this->sgl_tipo, null, null, 1, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
//			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
//			header( "Location: educar_turma_tipo_lst.php" );
			echo "<script>
						if (parent.document.getElementById('ref_cod_turma_tipo').disabled)
							parent.document.getElementById('ref_cod_turma_tipo').options[0] = new Option('Selectione um tipo de turma', '', false, false);
						parent.document.getElementById('ref_cod_turma_tipo').options[parent.document.getElementById('ref_cod_turma_tipo').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
						parent.document.getElementById('ref_cod_turma_tipo').value = '$cadastrou';
						parent.document.getElementById('ref_cod_turma_tipo').disabled = false;
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarTurmaTipo\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo ) && is_string( $this->sgl_tipo )\n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTurmaTipo($this->cod_turma_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->sgl_tipo, null, null, 1, $this->ref_cod_instituicao);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_turma_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarTurmaTipo\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_turma_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTurmaTipo($this->cod_turma_tipo, $this->pessoa_logada, null, null, null, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_turma_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarTurmaTipo\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_turma_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
	Event.observe(window, 'load', Init);
	function Init()
	{
		$('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
	}
<?
}
?>

</script>