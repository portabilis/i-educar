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
	
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de A&ccedil;&oatilde;es do Governo - Noticias!" );
		$this->processoAp = "551";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $cod_acao_governo;
	var $noticia;
		
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
				unset($_SESSION["noticias"]);
				unset($_SESSION["noticias"]["inserido"]);
				unset($_SESSION["noticias"]["removidos"]);
				
			}
		
			if(isset($_GET['remover_noticia']) && is_numeric($_GET['remover_noticia']) && $this->permiteEditar())
			{
				$obj_not = new clsPmiacoesAcaoGovernoNoticia($this->cod_acao_governo,$_GET['remover_noticia']);
				$obj_not->excluir();
				header("location: acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}&display={$_GET["display"]}");
				die;
					
			}
		}	
		
		@session_write_close();
		
		if(!isset($_GET['cod_acao_governo']))
			echo "<script>if(window.opener == null)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";
		else
		{
			$obj_acao = new clsPmiAcoesAcaoGoverno($_GET['cod_acao_governo']);
			if(!$det_acao = $obj_acao->detalhe())
				echo "<script>if(window.opener == null)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";
			
		}
		
		

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_acao_governo", $this->cod_acao_governo );
		$i = 0;
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];

/**
 * 
 */
		$obj_acao_governo = new clsPmiacoesAcaoGoverno($this->cod_acao_governo);
		$det_acao_governo = $obj_acao_governo->detalhe();
		
		if(!$det_acao_governo = $obj_acao_governo->detalhe())
			header("Location: acoes_acao_lst.php");
		
		if($det_acao_governo['numero_acao'])
			$this->campoRotulo( "cod_acao","N&uacute;mero da a&ccedil;&atilde;o", "{$det_acao_governo['numero_acao']}");
		$this->campoRotulo("nome_acao", "Nome da a&ccedil;&atilde;o", "{$det_acao_governo['nm_acao']}");

		if(!isset($_POST["inc"]) ){	
			
	
			if(isset($_GET["excluir_noticia"]) && $_GET["passo"] != 2)
			{
				
				unset( $_SESSION["noticias"]["inserido"][$_GET["excluir_noticia"]],$_GET["excluir_noticia"]);	
				header("Location: acoes_noticia.php?cod_acao_governo={$this->cod_acao_governo}&passo=2");
			}

				
		}
		else
		{
			if($_POST["inc"] == 2)
			{
				$existe = false;
				if(!empty($_SESSION["noticias"]["inserido"]))
				{
					foreach ($_SESSION["noticias"]["inserido"] as $key => $valor) {
						if($valor == $this->noticia){
							$existe = true;
							break;
						}
					
					}
				}
				if(!$existe){
					$_SESSION["noticias"]["inserido"][$this->noticia] = $this->noticia;
				}
			}
				
		}	


		$valorPadrao = array( ''=>"Selecione clicando no botao ao lado" );
		$valor = $valorPadrao;

		$this->campoLista("noticia", "Noticia Portal", $valor, $this->ref_idpes_requerente, "var objFav = document.getElementById( 'noticia' );if( objFav.value == 0 ) { var texto = this.options[this.selectedIndex].text; var pos = objFav.options.length; objFav.options[pos] = new Option( texto, this.value, false, false ); objFav.selectedIndex = pos;}",false,false,"<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'acoes_pesquisa_noticia.php?campo=noticia\'></iframe>');\">",false,true);

		$this->campoOculto("inc", "1");
		$this->campoRotulo("incluir", "Incluir noticia", "<a href='#' onclick=\"document.getElementById('inc').value=2;acao();\"><img src='imagens/banco_imagens/entrada2.gif' title='Incluir' border=0></a>");


		$this->campoQuebra2();
		$tabela = "<table border=0 width='300' cellpadding=3 id=\"tb_anexos\"><tr bgcolor='A1B3BD' align='center'><td colspan=2>Noticias</td></tr>";
		$cor = "#D1DADF";
		if(!empty($_SESSION["noticias"]["inserido"]))
		{				
			foreach ($_SESSION["noticias"]["inserido"] as $indice=>$valor)
			{
			
				$db = new clsBanco();
			
				$db->Consulta( "SELECT n.data_noticia, n.titulo, n.cod_not_portal FROM not_portal n where  n.cod_not_portal = {$indice} ORDER BY n.data_noticia DESC {$limit}" );			
				$db->ProximoRegistro();
				list ($data, $titulo, $id_noticia) = $db->Tupla();
				$cor = $cor == "#D1DADF" ? "#E4E9ED" : "#D1DADF";
				if(strlen($titulo) >= 40)
					$titulo = substr($titulo,0,40)."...";
				$tabela .= "<tr bgcolor=$cor align='center'><td>{$titulo}</td><td><a href=acoes_noticia.php?cod_acao_governo={$this->cod_acao_governo}&excluir_noticia={$id_noticia}><img border=0 title='Excluir' src='imagens/banco_imagens/excluirrr.gif'></a></td></tr>";
			}	
			$enviar = "document.getElementById(\"$this->__nome\").submit()";
		}else{
			$enviar = "isEmpty(\"Atenção nenhuma noticia foi selecionada, \\n para inserir uma nova noticia clique na lupa acima!\");";
			$tabela .= "<tr bgcolor=$cor align='center'><td>Nenhum noticia adicionada</td></tr>";
			
		}
		
		$tabela .= "</table>";
		
		$this->campoRotulo("tab", "Noticias", $tabela);
		
		$this->acao_enviar = "{$enviar}";
		$this->url_cancelar = "acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}";
		$this->nome_url_cancelar = "Cancelar";
		
	}

	function Novo() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		if( $_POST["inc"] == 1)
		{

			$noticias = array();
			$objAcaonoticia = new clsPmiAcoesAcaoGovernoNoticia();
			$objAcaonoticia->setCamposLista( "ref_cod_not_portal" );
			$listanoticias = $objAcaonoticia->lista( $this->cod_acao_governo);
			if($listanoticias)
			{
				foreach ($listanoticias as $key => $noticia) {
					$noticias[$noticia] = $noticia;	
				}			
			}
			if($_SESSION["noticias"]["inserido"]){
				foreach ($_SESSION["noticias"]["inserido"] as $key => $valor)
				{
					if(!array_key_exists($valor,$noticias))
					{
						$objAcaonoticia = new clsPmiacoesAcaoGovernoNoticia($this->cod_acao_governo,$valor,$this->pessoa_logada);
						$objAcaonoticia->cadastra();
							//return false;
					}
				}
			}
			
			header("location: acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}");	
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
<script>
function isEmpty(msg){
	
	alert(msg);
	return false;
}
</script>