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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once("include/pmiacoes/geral.inc.php");
require_once( "include/Geral.inc.php" );

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de A&ccedil;&oatilde;es do Governo - Categorias!" );
		$this->processoAp = "551";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $cod_acao_governo;
	var $categoria;
		
	function Inicializar()
	{
		
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		
		$this->cod_acao_governo = $_GET['cod_acao_governo'];
		
		if(isset($_GET['cod_acao_governo']))
		{
			if(isset($_GET['limpa']))
			{
				unset($_SESSION["acoes"]);
				unset($_SESSION["acoes"]["inserido"]);
				unset($_SESSION["acoes"]["removidos"]);
			}
		
			if(isset($_GET['remover_categoria']) && is_numeric($_GET['remover_categoria']) && $this->permiteEditar())
			{
				$obj_cat = new clsPmiacoesAcaoGovernoCategoria($_GET['remover_categoria'],$this->cod_acao_governo)	;
				$obj_cat->excluir();
				header("location: acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}&display={$_GET["display"]}");
				die;
					
			}
		}	
		
		@session_write_close();
		
		if(!isset($_GET['cod_acao_governo']))
			echo "<script>if(window.parent == window)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";
		else
		{
			$obj_acao = new clsPmiacoesAcaoGoverno($_GET['cod_acao_governo']);
			if(!$det_acao = $obj_acao->detalhe())
				echo "<script>if(window.parent == window)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";
			
		}
			

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_acao_governo", $this->cod_acao_governo );
		$i = 0;
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];

		
		if(!isset($_POST["inc"]) ){	
	
			if($_GET["passo"] != 2)
			{
				unset( $_SESSION["acoes"]["inserido"][$_GET["excluir_categoria"]],$_GET["excluir_categoria"]);	
				header("Location: acoes_categoria.php?cod_acao_governo={$this->cod_acao_governo}&passo=2");
			}
				
		}
		else
		{
			if($_POST["inc"] == 2)
			{
				$existe = false;
				if(!empty($_SESSION["acoes"]["inserido"]))
				{
					foreach ($_SESSION["acoes"]["inserido"] as $key => $valor) {
						if($valor == $this->categoria){
							$existe = true;
							break;
						}
					
					}
				}
				if(!$existe){
					$_SESSION["acoes"]["inserido"][$this->categoria] = $this->categoria;
				}
			}
				
		}	


		$array = array( 0 => "Selecione um processo clicando na lupa" );

		$categorias = array('' => 'Selecione');
		$obj_cat = new clsPmiacoesCategoria();
		$lista_cat = $obj_cat->lista(null,null,null,1);
		if($lista_cat)
		{
			foreach ($lista_cat as $categoria)
			{
				$categorias[$categoria["cod_categoria"]] = $categoria["nm_categoria"];
			}
		}
		$this->campoLista("categoria","Categoria",$categorias,'','',false,'','','',true);
		$this->campoOculto("inc", "1");
		$this->campoRotulo("incluir", "Incluir categoria", "<a href='#' onclick=\"document.getElementById('inc').value=2;acao();\"><img src='imagens/banco_imagens/entrada2.gif' title='Incluir' border=0></a>");


		$this->campoQuebra2();
		$tabela = "<table border=0 width='300' cellpadding=3 id=\"tb_anexos\"><tr bgcolor='A1B3BD' align='center'><td colspan=2>Categorias</td></tr>";
		$cor = "#D1DADF";
		
		if(!empty($_SESSION["acoes"]["inserido"]))
		{				
			foreach ($_SESSION["acoes"]["inserido"] as $indice=>$valor)
			{
				$obj_acoes_cat = new clsPmiacoesCategoria($indice);
				$det_acao =$obj_acoes_cat->detalhe();
				
				$cor = $cor == "#D1DADF" ? "#E4E9ED" : "#D1DADF";
				$tabela .= "<tr bgcolor=$cor align='center'><td>{$det_acao["nm_categoria"]}</td><td><a href=acoes_categoria.php?cod_acao_governo={$this->cod_acao_governo}&excluir_categoria={$valor}><img border=0 title='Excluir' src='imagens/banco_imagens/excluirrr.gif'></a></td></tr>";
			}	
			$enviar = "document.getElementById(\"$this->__nome\").submit();";
		}else{
			$enviar = "window.parent.isEmpty(\"Atenção nenhuma categoria foi selecionada, \\n para inserir uma nova categoria clique no botão\\n \\\"Incluir Categoria\\\"!\");";
			$tabela .= "<tr bgcolor=$cor align='center'><td>Nenhuma categoria adicionada</td></tr>";
			
		}
		
		$tabela .= "</table>";
		$this->campoRotulo("tab", "Categorias", $tabela);
		
		$this->acao_enviar = "{$enviar}";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length*1-1));";
		$this->nome_url_cancelar = "Cancelar";
		
	echo "<script>if(window.parent == window)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";		
	}

	function Novo() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		if( $_POST["inc"] == 1)
		{

			$categorias = array();
			$objAcaoCategoria = new clsPmiacoesAcaoGovernoCategoria();
			$objAcaoCategoria->setCamposLista( "ref_cod_categoria" );
			$listaCategorias = $objAcaoCategoria->lista( null,$this->cod_acao_governo);
			if($listaCategorias)
			{
				foreach ($listaCategorias as $key => $categoria) {
					$categorias[$categoria] = $categoria;	
				}			
			}
			if($_SESSION["acoes"]["inserido"]){
				foreach ($_SESSION["acoes"]["inserido"] as $key => $valor)
				{
					if(!array_key_exists($valor,$categorias))
					{
						$objAcaoCategoria = new clsPmiacoesAcaoGovernoCategoria($valor,$this->cod_acao_governo);
						if(!$objAcaoCategoria->cadastra())
							return false;
					}
				}
			}
		
			//echo "<script>window.opener.location = window.opener.location; window.close();</script>";
			echo "<script>window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1)); window.parent.location = window.parent.location;</script>";
		}

		return true;
	}

	function Editar() 
	{

		return false;
	}

	function Excluir()
	{
	
		return false;
	}
	
	function permiteEditar()
	{
		$retorno = false;
	
		if($_SESSION['acao_det'] != $this->cod_acao_governo)
			return false;
		$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];
		
		//*
		$obj = new clsSetor();
		$setor_pai = array_shift(array_reverse($obj->getNiveis($setor_funcionario)));
		//*
		
		$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_pai);
		$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();

		$obj_acao = new clsPmiacoesAcaoGoverno($this->cod_acao_governo);
		$obj_acao_det = $obj_acao->detalhe();
		$status = $obj_acao_det["status_acao"];
		
		
		//**
			$func_cad = $obj_acao_det["ref_funcionario_cad"];	
			$obj_funcionario = new clsFuncionario($func_cad);
			$detalhe_func = $obj_funcionario->detalhe();
			$setor_cad = $detalhe_func["ref_cod_setor_new"];			
			$setor_cad = array_shift(array_reverse($obj->getNiveis($setor_cad)));
		//**
		
		//$isSecom = $setor_pai == 4327 ? true : false;
		
		$retorno = (($obj_secretaria_responsavel_det != false && $status == 0) || ($setor_cad == $setor_pai && $status == 0 ) || ($obj_secretaria_responsavel_det != false && $status == 1) )? true : false;	
		return $retorno;
	}		
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
