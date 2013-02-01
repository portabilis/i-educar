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
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de A&ccedil;&oatilde;es do Governo - Arquivos!" );
		$this->processoAp = "551";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $cod_acao_governo;
	var $arquivo;
	var $nome_arquivo;
		
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
		
			if(isset($_GET['remover_arquivo']) && is_numeric($_GET['remover_arquivo']))
			{
				$obj_cat = new clsPmiacoesAcaoGovernoArquivo($_GET['remover_arquivo'],null,$this->cod_acao_governo)	;
				$obj_cat->setCamposLista("caminho_arquivo");
				$obj_det = $obj_cat->detalhe();
				if(file_exists($obj_det["caminho_arquivo"]))
					unlink($obj_det["caminho_arquivo"]);				
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
		
	
			if(isset($_GET["excluir_arquivo"]) && $_GET["passo"] != 2)
			{
			
				$_SESSION["acoes"]["removidos"][$_GET["excluir_arquivo"]] = $_GET["excluir_arquivo"];
				unset( $_SESSION["acoes"]["inserido"][$_GET["excluir_arquivo"]]);	

				header("Location: acoes_arquivo.php?cod_acao_governo={$this->cod_acao_governo}&passo=2");
				
			}
				
				
		}
		else
		{
			if($_POST["inc"] == 2)
			{
				$existe = false;

				if(!$existe){
					//salvar fotos
					if ($_FILES['arquivo']['name'] )
					{

							$arquivo = explode(".",$_FILES['arquivo']['name']);
							$novocaminho = date('Ymdhis')."".substr(md5($arquivo[0]), 0, 10).$arquivo[1];
							$caminho = "tmp/acoes_arquivo_".date('Ymdhis')."".substr(md5($arquivo[0]), 0, 10).".".$arquivo[1];
							$tmp = 0;
							while(file_exists($caminho))
							{
									$caminho = "tmp/acoes_arquivo_".date('Ymdhis')."".substr(md5("{$arquivo[0]}{$mud}"), 0, 10).".".$arquivo[1];
							}
							if(!copy($_FILES['arquivo']['tmp_name'], $caminho))
								return false;
						$_SESSION["acoes"]["inserido"][] = array($this->nome_arquivo,$caminho);	
					}	
									
				}
			}
				
		}	


		$this->campoTexto("nome_arquivo","Nome arquivo","",25,255,true);
		$this->campoArquivo("arquivo","Arquivo","",25);
		$this->campoOculto("inc", "1");
		$this->campoRotulo("incluir", "Incluir arquivo", "<a href='#' onclick=\"document.getElementById('inc').value=2;acao();\"><img src='imagens/banco_imagens/entrada2.gif' title='Incluir' border=0></a>");


		$this->campoQuebra2();
		$tabela = "<table border=0 width='300' cellpadding=3 id=\"tb_anexos\"><tr bgcolor='A1B3BD' align='center'><td colspan=2>Arquivos</td></tr>";
		$cor = "#D1DADF";
		
		if(!empty($_SESSION["acoes"]["inserido"]))
		{				
			foreach ($_SESSION["acoes"]["inserido"] as $indice=>$valor)
			{
				
				$cor = $cor == "#D1DADF" ? "#E4E9ED" : "#D1DADF";
				$tabela .= "<tr bgcolor=$cor align='center'><td>{$valor[0]}</td><td><a href=acoes_arquivo.php?cod_acao_governo={$this->cod_acao_governo}&excluir_arquivo={$indice}><img border=0 title='Excluir' src='imagens/banco_imagens/excluirrr.gif'></a></td></tr>";
			}	
			$enviar = "document.getElementById(\"$this->__nome\").submit()";
		}else{
			$enviar = "window.parent.isEmpty(\"Atenção nenhum arquivo foi selecionado, \\n para inserir um novo arquivo clique no botão\\n \\\"Incluir Arquivo\\\"!\");";
			$tabela .= "<tr bgcolor=$cor align='center'><td>Nenhum arquivo adicionado</td></tr>";
			
		}
		
		$tabela .= "</table>";
		$this->campoRotulo("tab", "", $tabela);
		
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

			$arquivos = array();

			if($_SESSION["acoes"]["inserido"]){
				foreach ($_SESSION["acoes"]["inserido"] as $key => $valor)
				{

					if(file_exists($valor[1]))
					{	
						$arquivo = explode("/",$valor[1]);
						$novo_arquivo ="arquivos/acoes/{$arquivo[1]}";
						copy($valor[1],$novo_arquivo);
						unlink($valor[1]);
					}
						$objAcaoarquivo = new clsPmiacoesAcaoGovernoArquivo(null,$this->pessoa_logada,$this->cod_acao_governo,$valor[0],$novo_arquivo);
						if(!$objAcaoarquivo->cadastra())
							return false;
				}
			}
			
			if($_SESSION["acoes"]["removidos"]){
			
				foreach ($_SESSION["acoes"]["removidos"] as $key => $valor)
				{
				
					if(file_exists($valor[1]))
						unlink($valor[1]);
				}			
					
			}
			echo "<script>window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));window.parent.location = window.parent.location;</script>";	
		}
	//echo "<script>if(window.opener == null)window.location = \"acoes_acao_lst.php\";</script>";		
	//echo "<script> window.location = window.location;</script>";
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
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
