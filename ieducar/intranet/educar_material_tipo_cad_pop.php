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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Material Did&aacute;tico" );
		$this->processoAp = "563";
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

	var $cod_material_tipo;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $nm_tipo;
	var $desc_tipo;
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

		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();

		$obj_permissao->permissao_cadastra(563, $this->pessoa_logada,3,"educar_tipo_usuario_lst.php");
		//**

		$this->cod_material_tipo=$_GET["cod_material_tipo"];


		if( is_numeric( $this->cod_material_tipo ) )
		{

			$obj = new clsPmieducarMaterialTipo( $this->cod_material_tipo );
			$registro  = $obj->detalhe();


			if( $registro )
			{

				/***ativacao de registro ***/
	/*			if($_GET["ativar"] == "true")
				{
					if( $registro["ativo"] == 0)
					{
						if(!$obj_permissao->permissao_excluir(563,$this->pessoa_logada))
						{
							echo "<script>alert('Usuário sem permissão para ativar material!'); document.location='educar_material_tipo_lst.php?ativo=excluido';</script>";
							die;
						}

						$obj->ativo = 1;
						$obj->ref_usuario_exc =  $this->pessoa_logada;
						if($obj->edita())
							echo "<script>alert('Ativação realizada com sucesso'); document.location='educar_material_tipo_lst.php?ativo=excluido';</script>";
						else
							echo "<script>alert('Erro ao ativar material didático!'); document.location='educar_material_tipo_lst.php?ativo=excluido';</script>";
					}
					else{
						echo "<script>alert('Tipo de material já se encontra ativo!'); document.location='educar_material_tipo_lst.php';</script>";
					}
				}
				*/
				/*if(!$registro["ativo"])
					header( "Location: educar_material_tipo_lst.php" );
				*/
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				//** verificao de permissao para exclusao
				$this->fexcluir = $obj_permissao->permissao_excluir(563,$this->pessoa_logada,3);
				//**
				$retorno = "Editar";
			}
		}
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_material_tipo_det.php?cod_material_tipo={$registro["cod_material_tipo"]}" : "educar_material_tipo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_material_tipo", $this->cod_material_tipo );
		if ($_GET['precisa_lista'])
		{
			// foreign keys
			$obrigatorio = true;
			include("include/pmieducar/educar_campo_lista.php");
		}
		else 
		{
			$this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
		}
		// text
		$this->campoTexto( "nm_tipo", "Material Didático", $this->nm_tipo, 40, 255, true );
		$this->campoMemo( "desc_tipo", "Descrição", $this->desc_tipo, 38, 5, false );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarMaterialTipo( null,$this->pessoa_logada,null,$this->nm_tipo,$this->desc_tipo,null,null,1,$this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			echo "<script>
						if (parent.document.getElementById('ref_cod_material_tipo').disabled)
							parent.document.getElementById('ref_cod_material_tipo').options[0] = new Option('Selecione um tipo de material', '', false, false);
						parent.document.getElementById('ref_cod_material_tipo').options[parent.document.getElementById('ref_cod_material_tipo').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
						parent.document.getElementById('ref_cod_material_tipo').value = '$cadastrou';
						parent.document.getElementById('ref_cod_material_tipo').disabled = false;
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
//			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
//			header( "Location: educar_material_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarMaterialTipo\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_instituicao ) && is_string( $this->nm_tipo ) \n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarMaterialTipo( $this->cod_material_tipo,null,$this->pessoa_logada,$this->nm_tipo,$this->desc_tipo,null,null,1,$this->ref_cod_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_material_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarMaterialTipo\nvalores obrigatorios\nif( is_numeric( $this->cod_material_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarMaterialTipo( $this->cod_material_tipo,null,$this->pessoa_logada,null,null,null,null,0 );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_material_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarMaterialTipo\nvalores obrigatorios\nif( is_numeric( $this->cod_material_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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