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
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de A&ccedil;&oatilde;es do Governo - Setor!" );
		$this->processoAp = "551";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $cod_acao_governo;
	var $setor;
		
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
			if(isset($_GET['remover_setor']) && is_numeric($_GET['remover_setor']) && $this->permiteEditar())
			{
				$obj_cat = new clsPmiacoesAcaoGovernoSetor($this->cod_acao_governo,$_GET['remover_setor']);
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
			
	
			if(isset($_GET["excluir_setor"]) && $_GET["passo"] != 2  && $_SESSION['acao_det'] == $this->cod_acao_governo)
			{
				
				unset( $_SESSION["acoes"]["inserido"][$_GET["excluir_setor"]],$_GET["excluir_setor"]);	
				header("Location: acoes_setor.php?cod_acao_governo={$this->cod_acao_governo}&passo=2");
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
						if($valor == $this->setor){
							$existe = true;
							break;
						}
					
					}
				}
				if(!$existe){
					$_SESSION["acoes"]["inserido"][$this->setor] = $this->setor;
				}
			}
				
		}	


		$array = array( 0 => "Selecione um clicando na lupa" );

		$setores = array('' => 'Selecione');
		
		$obj_setor = new clsSetor();

		$obj_setor_lista = $obj_setor->lista(null,null,null,null,null,null,null,null,null,1,0,null,null,"nm_setor",null,null,null,null,null,null,$cod_setor);
		
		if($obj_setor_lista)
		{
			foreach ($obj_setor_lista as $secretaria)
			{
				$setores[$secretaria["cod_setor"]] = $secretaria["sgl_setor"];
				
			}
		}
		$this->campoLista("setor","Setor",$setores,'','',false,'','','',true);
		$this->campoOculto("inc", "1");

		$this->campoRotulo("incluir", "Incluir setor", "<a href='#' onclick=\"document.getElementById('inc').value=2;acao();\"><img src='imagens/banco_imagens/entrada2.gif' title='Incluir' border=0></a>");


		$this->campoQuebra2();
		$tabela = "<table border=0 width='300' cellpadding=3 id=\"tb_anexos\"><tr bgcolor='A1B3BD' align='center'><td colspan=2>Setores</td></tr>";
		$cor = "#D1DADF";
		if(!empty($_SESSION["acoes"]["inserido"]))
		{				
			foreach ($_SESSION["acoes"]["inserido"] as $indice=>$valor)
			{
				$obj_acoes_cat = new clsSetor($indice);
				$det_acao =$obj_acoes_cat->detalhe();
				$cor = $cor == "#D1DADF" ? "#E4E9ED" : "#D1DADF";
				$tabela .= "<tr bgcolor=$cor align='center'><td>{$det_acao["sgl_setor"]}</td><td><a href=acoes_setor.php?cod_acao_governo={$this->cod_acao_governo}&excluir_setor={$valor}><img border=0 title='Excluir' src='imagens/banco_imagens/excluirrr.gif'></a></td></tr>";
			}	
			$enviar = "document.getElementById(\"$this->__nome\").submit()";
		}else{
			$enviar = "window.parent.isEmpty(\"Atenção nenhum setor foi selecionado, \\n para inserir um novo setor clique no botão\\n \\\"Incluir Setor\\\"!\");";
			$tabela .= "<tr bgcolor=$cor align='center'><td>Nenhum setor adicionado</td></tr>";
			
		}
		
		$tabela .= "</table>";
		
		$this->campoRotulo("tab", "Setores", $tabela);
		
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

			$setores = array();
			$objAcaosetor = new clsPmiacoesAcaoGovernoSetor();
			$objAcaosetor->setCamposLista( "ref_cod_setor" );
			$listasetores = $objAcaosetor->lista( $this->cod_acao_governo);
			if($listasetores)
			{
				foreach ($listasetores as $key => $setor) {
					$setores[$setor] = $setor;	
				}			
			}
			if($_SESSION["acoes"]["inserido"]){
				foreach ($_SESSION["acoes"]["inserido"] as $key => $valor)
				{
					if(!array_key_exists($valor,$setores))
					{
						$objAcaosetor = new clsPmiacoesAcaoGovernoSetor($this->cod_acao_governo,$valor,$this->pessoa_logada);
						$objAcaosetor->cadastra();
							//return false;
					}
				}
			}
			
			echo "<script>window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1)); window.parent.location = window.parent.location;</script>";
			//echo "<script>window.opener.location = window.opener.location; window.close();</script>";

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
