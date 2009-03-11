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
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de A&ccedil;&oatilde;es do Governo - Fotos!" );
		$this->processoAp = "551";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $cod_acao_governo;
	var $foto;
	var $nome_foto;
	var $data_foto;
		
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
		
			if(isset($_GET['remover_foto']) && is_numeric($_GET['remover_foto']) && $this->permiteEditar())
			{
				$obj_cat = new clsPmiacoesAcaoGovernofoto($_GET['remover_foto'],null,$this->cod_acao_governo);
				$obj_cat->setCamposLista("caminho");
				$obj_det = $obj_cat->detalhe();

				if(file_exists("arquivos/acoes/fotos/small/".$obj_det["caminho"]))
					unlink("arquivos/acoes/fotos/small/".$obj_det["caminho"]);
				
				if(file_exists("arquivos/acoes/fotos/big/".$obj_det["caminho"]))
					unlink("arquivos/acoes/fotos/big/".$obj_det["caminho"]);
				
				if(file_exists("arquivos/acoes/fotos/original/".$obj_det["caminho"]))
					unlink("arquivos/acoes/fotos/original/".$obj_det["caminho"]);
															
				$obj_cat->excluir();
				header("location: acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}&display={$_GET["display"]}");
				die;
					
			}
		}	
		
		@session_write_close();
		
		if(!isset($_GET['cod_acao_governo']))
			echo "<script>if(window.opener != null)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";
		else
		{
			$obj_acao = new clsPmiacoesAcaoGoverno($_GET['cod_acao_governo']);
			if(!$det_acao = $obj_acao->detalhe())
				echo "<script>if(window.opener != null)window.location = \"acoes_acao_lst.php\"; else window.close();</script>";
			
		}
		
		
		//echo "<script>if(window.opener != null)window.location = \"acoes_acao_lst.php\";</script>";	
		//echo "<script>window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1)); window.parent.location = window.parent.location;</script>";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_acao_governo", $this->cod_acao_governo );
		$i = 0;
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];

		
		if(!isset($_POST["inc"]) ){	
			

			if(isset($_GET["excluir_foto"]) && $_GET["passo"] != 2)
			{
			
				$_SESSION["acoes"]["removidos"][$_GET["excluir_foto"]] = $_GET["excluir_foto"];
				unset( $_SESSION["acoes"]["inserido"][$_GET["excluir_foto"]]);	

				header("Location: acoes_foto.php?cod_acao_governo={$this->cod_acao_governo}&passo=2");
				
			}
				
				
		}
		else
		{
			if($_POST["inc"] == 2)
			{
				$existe = false;

				if(!$existe){
					//salvar fotos
					if ($_FILES['foto']['name'] )
					{
	
							$type = array_shift(explode("/", $_FILES['foto']['type']));
							if($type == "image")
							{
							
								$foto = explode(".",$_FILES['foto']['name']);
								$novocaminho = date('Ymdhis')."".substr(md5($foto[0]), 0, 10).$foto[1];
								$caminho = "tmp/acoes_foto_".date('Ymdhis')."".substr(md5($foto[0]), 0, 10).".".$foto[1];
								//$caminho = "tmp/".$nome_arq;
								$tmp = 0;
								while(file_exists($caminho))
								{
									$caminho = "tmp/acoes_foto_".date('Ymdhis')."".substr(md5($foto[0]), 0, 10).".".$foto[1];
									//$caminho = "tmp/".$nome_arq;
								}
								if(!copy($_FILES['foto']['tmp_name'], $caminho))
									return false;
				
								$_SESSION["acoes"]["inserido"][] = array($this->nome_foto,$caminho,$this->data_foto);	
							}
							else
							{
								echo "<script>alert('Tipo de imagem inválido');</script>";
							}
					}	
									
				}
			}
				
		}	


		$this->campoTexto("nome_foto","Nome foto","",25,255,true);
		$this->campoData("data_foto","Data foto","",true);
		$this->campoArquivo("foto","foto","",25);
		$this->campoOculto("inc", "1");
		$this->campoRotulo("incluir", "Incluir foto", "<a href='#' onclick=\"document.getElementById('inc').value=2;acao();\"><img src='imagens/banco_imagens/entrada2.gif' title='Incluir' border=0></a>");


		$this->campoQuebra2();
		$tabela = "<table border=0 width='300' cellpadding=3 id=\"tb_anexos\"><tr bgcolor='A1B3BD' align='center'><td colspan=2>foto</td></tr>";
		$cor = "#D1DADF";
		
		if(!empty($_SESSION["acoes"]["inserido"]))
		{				
			foreach ($_SESSION["acoes"]["inserido"] as $indice=>$valor)
			{
				
				$cor = $cor == "#D1DADF" ? "#E4E9ED" : "#D1DADF";
				$tabela .= "<tr bgcolor=$cor align='center'><td>{$valor[0]}</td><td><a href=acoes_foto.php?cod_acao_governo={$this->cod_acao_governo}&excluir_foto={$indice}><img border=0 title='Excluir' src='imagens/banco_imagens/excluirrr.gif'></a></td></tr>";
			}	
			$enviar = "document.getElementById(\"$this->__nome\").submit()";
		}else{
			$enviar = "window.parent.isEmpty(\"Atenção nenhuma foto foi selecionada, \\n para inserir um nova foto clique no botão\\n \\\"Incluir Foto\\\"!\");";
			$tabela .= "<tr bgcolor=$cor align='center'><td>Nenhum foto adicionada</td></tr>";
			
		}
		
		$tabela .= "</table>";
		$this->campoRotulo("tab", "Fotos", $tabela);
		
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
	//	echo 

		if( $_POST["inc"] == 1)
		{
			$foto = array();
			if($_SESSION["acoes"]["inserido"]){
				foreach ($_SESSION["acoes"]["inserido"] as $key => $valor)
				{
					if(file_exists($valor[1]))
					{
						$foto = explode("/",$valor[1]);
						$nome_foto = $foto[1];
						//$nome_arq = explod
						//$nova_foto ="arquivos/acoes/fotos/original{$foto[1]}";
						//copy($valor[1],$nova_foto);
						$this->geraFotos($valor[1]);
						unlink($valor[1]);
					}
						$objAcaofoto = new clsPmiacoesAcaoGovernoFoto(null,$this->pessoa_logada,$this->cod_acao_governo,$valor[0],$nome_foto,$valor[2]);
						
					if(!$objAcaofoto->cadastra())
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
			echo "<script>window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1)); window.parent.location = window.parent.location;</script>";
			//header("location: acoes_foto.php?cod_acao_governo={$this->ref_cod_processo_1}");	
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
		
		$retorno = (($obj_secretaria_responsavel_det != false && $status == 0) || ($setor_cad == $setor_pai && $status == 0 ) || ($obj_secretaria_responsavel_det != false && $status == 1))? true : false;	
		return $retorno;
	}	
	
	function geraFotos($fotoOriginal){
		list ($imagewidth, $imageheight, $img_type) = @GetImageSize($fotoOriginal);
		$src_img_original = "";

		$fim_largura = $imagewidth;
		$fim_altura = $imageheight;

		$extensao = ($img_type == "2") ? ".jpg" : (($img_type == "3") ? ".png" : "");
		$nome_do_arquivo = array_pop(explode("/",$fotoOriginal));//date('Y-m-d')."-".substr(md5($fotoOriginal), 0, 10).$extensao;
		$caminhoDaBig = "arquivos/acoes/fotos/big/{$nome_do_arquivo}";
		$caminhoDaFotoOriginal = "arquivos/acoes/fotos/original/{$nome_do_arquivo}";
		
		if ($imagewidth > 700)
		{
			$new_w = 700;
			$ratio = ($imagewidth / $new_w);
			$new_h = ceil($imageheight / $ratio);
			
			$fim_largura = $new_w;
			$fim_altura = $new_h;

			if ( !file_exists($caminhaDaBig) )
			{
				if ($img_type=="2")
				{
					$src_img_original = @imagecreatefromjpeg($fotoOriginal);
					$dst_img = @imagecreatetruecolor($new_w,$new_h);
					imagecopyresized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,imagesx($src_img_original),imagesy($src_img_original));
					imagejpeg($dst_img, $caminhoDaBig);
				}
				else if ($img_type=="3")
				{
					$src_img_original=@ImageCreateFrompng($fotoOriginal);

					$dst_img=@imagecreatetruecolor($new_w,$new_h);
					ImageCopyResized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,ImageSX($src_img_original),ImageSY($src_img_original));
					Imagepng($dst_img, $caminhoDaBig);
				}
			}
		}
		else
		{

			if ( !file_exists($caminhoDaBig) )
			{

				copy ($fotoOriginal, $caminhoDaBig);

				if ($img_type=="2")
				{
					$src_img_original = @imagecreatefromjpeg($fotoOriginal);
				}
				else if ($img_type=="3")
				{
					$src_img_original=@imagecreatefrompng($fotoOriginal);
				}
			}
		}
		
		$new_w = 100;
		$ratio = ($imagewidth / $new_w);
		$new_h = round($imageheight / $ratio);

		$caminhoDaSmall = "arquivos/acoes/fotos/small/{$nome_do_arquivo}";
		
			
		if ( !file_exists($caminhaDaBig) )
		{
		
			if ($img_type=="2")
			{
			
				$dst_img = @imagecreatetruecolor($new_w,$new_h);
				@imagecopyresized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,imagesx($src_img_original),imagesy($src_img_original));

				@imagejpeg($dst_img, $caminhoDaSmall);
				
			}
			else if ($img_type=="3")
			{
				$dst_img=@imagecreatetruecolor($new_w,$new_h);
				@imageCopyResized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,ImageSX($src_img_original),imageSY($src_img_original));

				@imagepng($dst_img, $caminhoDaSmall);
			}
		}
		
		copy($fotoOriginal, $caminhoDaFotoOriginal);
		if( ! ( file_exists( $fotoOriginal ) && file_exists( $caminhoDaSmall ) && file_exists( $caminhoDaBig ) ) )
		
		{
		
			die( "<center><br>Um erro ocorreu ao inserir a foto.<br>Por favor tente novamente.</center>" );
		}	
	
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
