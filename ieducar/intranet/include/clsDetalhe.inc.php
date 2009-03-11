<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja�								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
if(class_exists("clsPmiajudaPagina"))
{
	require_once ( "include/pmiajuda/clsPmiajudaPagina.inc.php" );
}

class clsDetalhe 
{
	var $titulo;
	var $banner = false;
	var $bannerLateral = false;
	var $titulo_barra;
	var $bannerClose = false;
	var $largura;
	var $detalhe = array();

	var $url_novo;
	var $caption_novo = "Novo";
	var $url_editar;
	var $url_cancelar;
	var $nome_url_cancelar = "Voltar";

	var $array_botao;
	var $array_botao_url;
	var $array_botao_url_script;

	function addBanner( $strBannerUrl = "", $strBannerLateralUrl = "", $strBannerTitulo = "", $boolFechaBanner = true )
	{
		if( $strBannerUrl != "" )
		{
			$this->banner = $strBannerUrl;
		}
		if( $strBannerLateralUrl != "" )
		{
			$this->bannerLateral = $strBannerLateralUrl;
		}
		if( $strBannerTitulo != "" )
		{
			$this->titulo_barra = $strBannerTitulo;
		}
		$this->bannerClose = $boolFechaBanner;
	}

	function addDetalhe( $detalhe )
	{
		$this->detalhe[] = $detalhe;
	}

	function Gerar ()
	{
		return false;
	}
 
	function RenderHTML()
	{
		$this->titulo_barra= "Intranet";
		$this->Gerar();

		$retorno = "";
		if( $this->banner )
		{
			$retorno .= "<table width='100%' style=\"height:100%\" border='0' cellpadding='0' cellspacing='0'><tr>";
			$retorno .= "<td class=\"barraLateral\" width=\"21\" valign=\"top\"><a href='#'><img src=\"{$this->bannerLateral}\" align=\"right\" border=\"0\" alt=\"$this->titulo_barra\" title=\"$this->titulo_barra\"></a></td><td valign='top'>";
		}

		$script = explode("/",$_SERVER["PHP_SELF"]);
		$script = $script[count($script)-1];

		$width = empty( $this->largura ) ? "" : "width='$this->largura'";

		/*
		* adiciona os botoes de help para a pagina atual
		*/
		$url = parse_url($_SERVER['REQUEST_URI']);
		$url = ereg_replace( "^/", "", $url["path"] );
		if( strpos($url,"_det.php") !== false )
		{
			$tipo = "det";
		}
		else if( strpos($url,"_lst.php") !== false )
		{
			$tipo = "lst";
		}
		else if( strpos($url,"_pdf.php") !== false )
		{
			$tipo = "pdf";
		}
		else
		{
			$tipo = "cad";
		}
		
		$barra = "<b>{$this->titulo}</b>";
		if(class_exists("clsPmiajudaPagina"))
		{
			$ajudaPagina = new clsPmiajudaPagina();
			$lista = $ajudaPagina->lista(null,null,$url);
			if( $lista )
			{
				$barra = "
				<table border=\"0\" cellpading=\"0\" cellspacing=\"0\" width=\"100%\">
					<tr>
					<script type=\"text/javascript\">document.help_page_index = 0;</script>
					<td width=\"20\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Bot�o de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta p�gina\"></a></td>
					<td><b>{$this->titulo}</b></td>
					<td align=\"right\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Bot�o de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta p�gina\"></a></td>
					</tr>
				</table>";
			}
		}

		$retorno .= "
			<!-- detalhe begin -->
			<table class='tableDetalhe' $width border='0' cellpadding='2' cellspacing='2'>
				<tr>
					<td class='formdktd' colspan='2' height='24'>{$barra}</td>
				</tr>
				";

		if ( empty( $this->detalhe ) )
		{
			$retorno .= "<tr><td class='tableDetalheLinhaSim' colspan='2'>N&atilde;o h&aacute; informa&ccedil;&atilde;o a ser apresentada.</td></tr>\n";
		}
		else
		{
			if (is_array($this->detalhe))
			{
				reset( $this->detalhe );
				$campo_anterior = "";
				$md = true;
				foreach($this->detalhe as $pardetalhe)
				{
//					if($pardetalhe[0] && $pardetalhe[1])
//					{
						$campo = $pardetalhe[0].":";
						$texto = $pardetalhe[1];
//					}
					if ($campo == $campo_anterior)
					{
						$campo = "";
					}
					else
					{
						$campo_anterior = $campo;
						$md = !$md;
					}

					if ($campo == "-:")
					{
						if (empty( $texto ))
							$texto = "&nbsp;";

						$retorno .= "<tr><td colspan='2' class='' width='20%'><span class='form'><b>$texto</b></span></td></tr>\n";
					}
					else
					{
						$classe = $md ? 'formmdtd' : 'formlttd';

						$retorno .= "<tr><td class='$classe' width='20%'>$campo</td><td class='$classe'>$texto</td></tr>\n";
					}
				}
			}
		}
		$retorno .= "<tr><td class='tableDetalheLinhaSeparador' colspan='2'></td></tr>\n";

		if ( !empty( $this->url_editar ) || !empty( $this->url_cancelar ) || $this->array_botao )
		{
			$retorno .= "
				<tr>
					<td colspan='2' align='center'>
						<script type=\"text/javascript\" language='javascript'>
							function go( url ) {
								document.location = url;
							}
						</script>
						";

			if ($this->url_novo)
			{
				$retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_novo\" );' value=' {$this->caption_novo} '>&nbsp;\n";
			}
			if ($this->url_editar)
			{
				$retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_editar\" );' value=' Editar '>&nbsp;\n";
			}
			if ($this->url_cancelar)
			{
				$retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_cancelar\" );' value=' $this->nome_url_cancelar '>&nbsp;\n";
			}
			$retorno .= "</td></tr>";

			if ($this->array_botao_url || $this->array_botao_url_script)
			{

				$retorno .= "<tr><td colspan=2><table width='100%' summary=''><tr><td></td><td height='1' width='90%' bgcolor='#858585' style='font-size: 0px;'>&nbsp;</td><td></td></tr></table></td></tr><tr><td colspan='2' align='center'>";
			}

			if($this->array_botao_url)
			{
				for ( $i = 0; $i < count($this->array_botao); $i++)
				{
					$retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"".$this->array_botao_url[$i]."\" );' value='".$this->array_botao[$i]."'>&nbsp;\n";
				}
			}
			elseif ($this->array_botao_url_script)
			{
					for ( $i = 0; $i < count($this->array_botao); $i++)
					{
						$retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='{$this->array_botao_url_script[$i]}' value='".$this->array_botao[$i]."'>&nbsp;\n";
					}
			}

			if ($this->array_botao_url || $this->array_botao_url_script)
			{
				$retorno .= "</td></tr>";
			}

			$retorno .= "<tr><td colspan='2' height='1' bgcolor='black' style='font-size: 0px;'>&nbsp;</td></tr>";
		}
		$retorno .= "
					</table><br><br>
					<!-- detalhe end -->";

		if( $this->bannerClose )
		{
			$retorno .= "
							<!-- Fechando o Banner (clsDetalhe) -->
						</td>
					</tr>
				</table>
			";
		}

		return $retorno;
	}
}
?>