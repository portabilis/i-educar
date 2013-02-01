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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
		$this->processoAp = "567";
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

	var $cod_infra_predio;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_escola;
	var $nm_predio;
	var $desc_predio;
	var $endereco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_infra_predio=$_GET["cod_infra_predio"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 567, $this->pessoa_logada,7, "educar_infra_predio_lst.php" );

//		if( is_numeric( $this->cod_infra_predio ) )
//		{
//
//			$obj = new clsPmieducarInfraPredio( $this->cod_infra_predio );
//			$registro  = $obj->detalhe();
//			if( $registro )
//			{
//				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
//					$this->$campo = $val;
//
//
//				//** verificao de permissao para exclusao
//				$this->fexcluir = $obj_permissoes->permissao_excluir(567,$this->pessoa_logada,7);
//				//**
//				$retorno = "Editar";
//			}
//			else
//			{
//				header( "Location: educar_infra_predio_lst.php" );
//				die();
//			}
//		}
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}" : "educar_infra_predio_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
//		die();
		return $retorno;
	}

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		// primary keys
		$this->campoOculto( "cod_infra_predio", $this->cod_infra_predio );


		//** 2 - Escola 1 - institucional 0 - poli-institucional
//		$obj_permissao = new clsPermissoes();
//		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
//
//		//busca instituicao e escola do usuario
//		$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
//		$obj_usuario->setCamposLista("ref_cod_instituicao,ref_cod_escola");
//		$det_obj_usuario = $obj_usuario->detalhe();
//
//
//		$instituicao_usuario = $det_obj_usuario["ref_cod_instituicao"];
//		$escola_usuario = $det_obj_usuario["ref_cod_escola"];
//
//		if( class_exists( "clsPmieducarEscola" ) )
//		{
//			$objTemp = new clsPmieducarEscola($escola_usuario);
//			$objTemp->setCamposLista("nm_escola");
//			$det_objTemp = $objTemp->detalhe();
//			$nome_escola = $det_objTemp["nm_escola"];
//
//		}
//		else
//		{
//			echo "<!--\nErro\nClasse clsPmieducarEscola nao encontrada\n-->";
//			$nome_escola =  "Erro na geracao";
//		}
//
//		// listagem escola - instituicao
//		if(!$this->ref_cod_escola)
//		{
//			$obrigatorio = true;
//			include("include/pmieducar/educar_pesquisa_instituicao_escola.php");
//			if($nivel_usuario == 2)
//				$this->campoRotulo("ref_cod_escola_","Escola",ucfirst($nome_escola));
//		}else{
//
//				$this->campoRotulo("ref_cod_escola_","Escola",ucfirst($nome_escola));
//				$this->campoOculto("ref_cod_escola",$escola_usuario);
//				//$this->campoOculto("ref_cod_instituicao",$escola_usuario);
//		}

		if ($_GET['precisa_lista'])
		{
			$obrigatorio = true;
			$get_escola	 = true;
			include("include/pmieducar/educar_campo_lista.php");
		}
		else 
		{
			$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);			
		}
		// text
		$this->campoTexto( "nm_predio", "Nome Prédio", $this->nm_predio, 30, 255, true );
		$this->campoMemo( "desc_predio", "Descrição Prédio", $this->desc_predio, 60, 10, false );
		$this->campoMemo( "endereco", "Endereço", $this->endereco, 60, 2, true );




	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
//		die($this->ref_cod_escola);
		$obj = new clsPmieducarInfraPredio( $this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			
			echo "<script>
						if (parent.document.getElementById('ref_cod_infra_predio').disabled)
							parent.document.getElementById('ref_cod_infra_predio').options[0] = new Option('Selecione um prédio', '', false, false);
						parent.document.getElementById('ref_cod_infra_predio').options[parent.document.getElementById('ref_cod_infra_predio').options.length] = new Option('$this->nm_predio', '$cadastrou', false, false);
						parent.document.getElementById('ref_cod_infra_predio').value = '$cadastrou';
						parent.document.getElementById('ref_cod_infra_predio').disabled = false;
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
//			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
//			header( "Location: educar_infra_predio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarInfraPredio\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_escola ) && is_string( $this->nm_predio ) && is_string( $this->endereco )\n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null,null, 1);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_infra_predio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarInfraPredio\nvalores obrigatorios\nif( is_numeric( $this->cod_infra_predio ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_infra_predio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarInfraPredio\nvalores obrigatorios\nif( is_numeric( $this->cod_infra_predio ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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
		$('ref_cod_escola').value = parent.document.getElementById('ref_cod_escola').value;
//		alert($F('ref_cod_escola'));
	}
	
<?}?>

</script>