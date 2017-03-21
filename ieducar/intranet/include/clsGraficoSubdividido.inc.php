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
	class clsGraficoSubdividido
	{
		var $arrayValores;
		var $maxVal;
		var $maxWidth;
		var $deslocamentos;
		var $charIdentacao = " &nbsp; -";
		var $titulo;
		var $totalGeral;
		
		function clsGraficoSubdividido( $arrayValores, $maxVal, $maxWidth, $titulo = false, $totalGeral=false )
		{
			$this->arrayValores = $arrayValores;
			$this->maxVal = $maxVal;
			$this->maxWidth = $maxWidth;
			$this->deslocamentos = array();
			$this->titulo = $titulo;
			$this->totalGeral = $totalGeral;
		}
		
		function geraHTML()
		{
			$colspan = ( $this->totalGeral ) ? 4: 3;
			
			$getLinhas = $this->getLinha( $this->arrayValores, 0, 0 );
			$retorno = "
			
			<!doctype HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
			<html>
				<head>
					<title> Gráfico </title>
					<link rel=stylesheet type='text/css' href='styles/novo.css'>
					</script>	
					<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
				</head>
				<body>			
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" align=\"center\">\n";
			if( $this->titulo )
			{
				$retorno .= "<tr><td colspan=\"{$colspan}\" class=\"graf_titulo\">{$this->titulo}</td></tr>
							<tr><td class=\"grafsub_barra\" colspan=\"2\"></td><td class=\"grafsub_barra\" >Valores</td><td class=\"grafsub_barra\">Percentagem</td></tr>";
			}
			$retorno .= $getLinhas;
			if( $this->titulo )
			{
				$retorno .= "<tr><td class=\"grafsub_barra\" colspan=\"2\"></td><td class=\"grafsub_barra\" colspan=\"2\" align=\"center\">Total</tr>
							 <tr><td class=\"grafsub_barra\" colspan=\"2\"></td><td class=\"grafsub_barra\">{$this->totalGeral}</td><td class=\"grafsub_barra\">100%</td></tr>
							 <tr><td colspan=\"{$colspan}\" class=\"graf_titulo\">&nbsp;</td></tr>";
			}
			$retorno .= "</table>";
			return $retorno;
		}
		
		function printHTML()
		{
			echo $this->geraHTML();
		}
		
		// monta as linhas recursivamente
		function getLinha( $arr, $nivel, $nmPai )
		{
			$retorno = "";
			$identacao = str_repeat( $this->charIdentacao, max( $nivel - 1, 0 ) );
			if( ! isset( $this->deslocamentos[$nivel] ) )
			{
				$this->deslocamentos[$nivel] = 0;
			}
			
			// primeiro verifica o campo graf_default
			if( isset( $arr["graf_default"] ) )
			{
				if ( is_numeric( $arr["graf_default"] ) )
				{
					if( $nmPai )
					{
						// exibe o campo com a barra correspondente
						$porcentagem = "";
						$proporcao = round( ( $arr["graf_default"] / $this->maxVal ) * $this->maxWidth );
						$retorno .= "<tr><td class=\"graf_legenda\">{$identacao}{$nmPai}</td><td class=\"grafsub_barra\" background=\"imagens/bbg.gif\">";
						$retorno .= "<img src=\"imagens/binvisivel.gif\" width=\"{$this->deslocamentos[$nivel]}\" height=\"5\" border=\"0\" title=\"". number_format( $arr["graf_default"], 2, ",", "." ) . "\">";
						if( $this->totalGeral )
						{
							$porcentagem = number_format( ( $arr["graf_default"] / $this->totalGeral ) * 100, 2, ",", "." );
						}
						$complete = 0;
						if( $nivel > 1 )
						{
							$this->deslocamentos[$nivel] += $proporcao;
							if( $this->deslocamentos[( $nivel -1 )] )
							{
								$complete = $this->deslocamentos[( $nivel -1 )] - $this->deslocamentos[$nivel];
							}
						}
						else 
						{
							$this->deslocamentos = array();
						}
						$retorno .= "<img src=\"imagens/bl{$nivel}.gif\" width=\"{$proporcao}\" height=\"5\" title=\"". number_format( $arr["graf_default"], 2, ",", "." ) . "\">";
						if( $complete )
						{
							$retorno .= "<img src=\"imagens/binvisivel.gif\" width=\"{$complete}\" height=\"5\" title=\"". number_format( $arr["graf_default"], 2, ",", "." ) . "\">";
						}
						$retorno .= "</td><td class=\"grafsub_barra\" width=\"50\">". number_format( $arr["graf_default"], 2, ",", "." );
						if( $this->totalGeral )
						{
							$retorno .= "</td><td class=\"grafsub_barra\" width=\"50\">({$porcentagem}%)";
						}
						$retorno .= "</td></tr>\n";
					}
				}
				else 
				{
					// exibe apenas um rotulo vazio
					$retorno .= "<tr><td class=\"tipo\">{$identacao}{$nmPai}</td><td>&nbsp;</td></tr>\n";
				}
			}
			
			$identacao = str_repeat( $this->charIdentacao, $nivel );
			//$deslocamentoLinha = 0;
			
			// passa todos os itens
			foreach ( $arr AS $key => $value )
			{
				if( $key != "graf_default" )
				{
					if( is_array( $value ) )
					{
						// chama recursivamente o metodo
						$retorno .= $this->getLinha( $value, $nivel + 1, $key );
					}
					else if ( is_numeric( $value ) )
					{
						// exibe o campo com a barra correspondente
						$proporcao = round( ( $value / $this->maxVal ) * $this->maxWidth );
						$retorno .= "<tr><td class=\"grafsub_tipo\">{$identacao}<b>{$key}</b></td><td class=\"grafsub_barra\" background=\"imagens/bbg.gif\">";
						$retorno .= "<img src=\"imagens/binvisivel.gif\" width=\"{$this->deslocamentos[$nivel]}\" height=\"5\" border=\"0\" title=\"". number_format( $arr["graf_default"], 2, ",", "." ) . "\">";
						$retorno .= "<img align=\"left\" src=\"imagens/bl" . ( $nivel + 1 ) . ".gif\" width=\"{$proporcao}\" height=\"5\" title=\"". number_format( $value, 2, ",", "." ) . "\"></td><td class=\"grafsub_barra\">". number_format( $value, 2, ",", "." ) . "</td></tr>\n";
						$this->deslocamentos[$nivel] += $proporcao;
					}
				}
			}
			$retorno .= "</body>
							</html>";
			return  $retorno;
		}
	}
?>