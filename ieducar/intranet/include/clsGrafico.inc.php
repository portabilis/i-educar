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

	class clsGrafico
	{
		
		/*****************************************************************************************
		* Enter description here...
		*
		* @var array $arrValores; -> array que possui um rotulo 
		*			 e é seguido de um valor ou array de valores
		* @var int $intTamGrafico; -> Define o tamanho em px da largura do grafico
		* @var str $titulo; -> Define o título do gráfico
		* @var array ou str $legConteudo; -> define o conteudo  da legenda 
		*
		*
		*		EXEMPLO 1 DE COMO USAR A FUNCAO
		*			$dados = array("Jan"=>array(420,300,100,412) ,"Fev"=>array(400,200,340,321), 	
		*           "Mar"=>array(280,350,300,380), "Abr"=>array(400,200,300,432), 
	    *	        "Mai"=>array(405,220,150,255),"Jun"=> array(480,230,310,231), 	
	    *		    "Jul"=>array(350,200,300,123), "Ago"=>array(400,200,30,264),  
	    *		    "Set"=>array(405,220,150,255),"Out"=> array(480,230,310,231), 
		*			"Nov"=>array(350,200,300,123), "Dec"=>array(400,200,30,264)
	  	*			  );	
		*		    $leg = array("A","B","C","D") ;
		*           $exemplo1 = new clsGrafico($dados,"Gráfico Teste",500,$leg);
		*           echo $exemplo1->graficoBarraHor();
		*
		*	        EXEMPLO 2 DE COMO USAR A FUNCAO 
		*			$dados2 = array("Dom"=>10, "Seg"=>400,"Ter"=>150, "Qua"=>200, "Qui"=>350,"Sex"=>10,"Sab"=>150 );	
		*           $exemplo2 = new clsGrafico($dados2,"Gráfico Teste",500,"Legenda");
		*           echo $exemplo2->graficoBarraHor();
		*			os Exemplos podem ser usados chamando grafico vertical ou horizontal
	    *****************************************************************************************/
		var $arrValores;
		var $intTamGrafico;
		var $titulo;
		var $legConteudo;
		var $align;
		var $globalAlign;
		
		
		function clsGrafico($valor, $titulo="Gráfico", $tgrafico=600,  $legConteudo="" )
		{
			$this->arrValores = $valor;			
			$this->intTamGrafico = $tgrafico;
			$this->titulo = $titulo;
			$this->legConteudo = $legConteudo;
			$this->align = "center";
			$this->globalAlign = "center";
		}
		
		function setAlign( $strAlign )
		{
			$this->align = $strAlign;
		}
		function setGlobalAlign( $strAlign )
		{
			$this->globalAlign = $strAlign;
		}
			
		/**
		 * Enter description here...
		 * A função graficoBarraHor gera graficos de barra em posição horizontal
		 *
		 *
		 * @return $retorno; //$retorno possui o conteudo em HTML da pagina
		 */
		function graficoBarraHor()
		{
			$valor =  $this->arrValores;		
			$tgrafico = $this->intTamGrafico;
			$titulo = $this->titulo;
			$legConteudo = $this->legConteudo ;
			$retorno .= "<html lang=\"pt\">
	<head>
	   	<title> <!-- #&TITULO&# --> </title>
		<link rel=stylesheet type='text/css' href='styles/styles.css'>
		<link rel=stylesheet type='text/css' href='styles/novo.css'>
		
		<script type='text/javascript' src='scripts/padrao.js'></script>
		<script type='text/javascript' src='scripts/novo.js'></script>
		<script type='text/javascript' src='scripts/dom.js'></script>
		
		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
		<meta http-equiv=\"Pragma\" content=\"no-cache\">
		<meta http-equiv=\"Expires\" content=\"-1\">
		<!-- #&REFRESH&# -->
		
		<meta name='Author' content='Prefeitura de Itajaí'>
		<meta name='Description' content='Portal da Prefeitura de Itajaí'>
		<meta name='Keywords' content='portal, prefeitura, itajaí, serviço, cidadão'>
	</head>
	<body>";
			
			$retorno = ereg_replace("<!-- #&TITULO&# -->",$this->titulo,$retorno);
			
			$retorno .= "<table border='0' cellpadding='0' cellspacing='1' align=\"{$this->globalAlign}\"><tr><td colspan=\"4\" align=\"center\" class=\"graf_titulo\">{$titulo}</td></tr>\n";
			//$retorno .= "<tr><td colspan=2 align=center>&nbsp;</td></tr>\n";
			$maior = 0;						 // verifica qual o maior valor para servir como escala do grafico
			$cont = 0;						 // conta qual o numero maximo de barras que é passado para trocar a cor de forma correta			
			if(is_array($valor))
			{
				foreach ($valor as $rotulo=>$v ) 
				{
					$indice =0;
					if(is_array($v))
					{
						foreach ($v as $key => $parametro)	
						{
							if($parametro > $maior)
							{
								// encontra qual o maior valor e armazena em $maior quando se passa array
								$maior = $parametro;
							}
							$indice++;
							$totalGeral[$key] += $parametro;
						}	
						if($indice > $cont)
						{
							// verifica quantas barras de cor diferente o grafico ira possuir  											
							$cont = $indice;	
						}
					}
					else
					{
						if($v > $maior)
						{
							// encontra qual o maior valor e armazena em $maior	quando se passa uma str	
							$maior = $v;			
						}
						$totalGeral += $v;
					}
				}
			}
			$vcor = 1;  		        // variavel usada para mudar a cor da barra quando incrementada
		    $title=$legConteudo;		// varivel title recebe a legenda para coloca-la como title nas imagens    
		    $ctitle=0;					// variavel que coloca a legenda como titulo nas barras conforme a cor 
			if(is_array($valor))
			{
				foreach ($valor as $rotulo=>$v) 
				{								
					$retorno .= "<tr><td rowspan={$cont} class=\"grafsub_tipo\" style=\"text-align:{$this->align}; border-right: 1px solid #000000;\">{$rotulo}</td>\n";
					// a variavel passada é um array ?????
					if(is_array($v)) 
					{	
						foreach ($v as $key => $parametro)	
						{	
							// troca a cor da barra conforme o valor de $vcor
							switch ($vcor)  		 
							{									
								case 1:
										$color = "grafico_he.png";
										$vcor++;
										break;
								case 2:
										$color = "grafico_hk.png";
										$vcor++;
										break;
								case 3:
										$color = "grafico_hx.png";
										$vcor++;
										break;
								case 4:
										$color = "grafico_hp.png";
										$vcor++;
										break;
										
								case 5:
										$color = "grafico_hh.png";
										$vcor++;
										break;
							}								
							 //  calculo necessario para inserir valores conforme a escala $tgrafico
							$aux = ($tgrafico*$parametro)/$maior;
							
							// calcula a %
							$porcentagem = number_format( ( $parametro / $totalGeral[$key] ) * 100, 2, ",", "." );
							 //$aux = (($tgrafico/$maior)*$parametro);		
							// arredondamento dos valores
							$aux = round($aux);
							// espaco aumentado para descricao da barra
							$tam_tabela = $tgrafico + 50 ;
							
							$aux_altura = 5;
							
							//montagem das barras do grafico usando imagens
							$retorno .= "<td width=\"{$tam_tabela}\" class=\"grafsub_barra\" background=\"imagens/bbg.gif\"><img src='imagens/{$color}' vspace='0' hspace='0' width='{$aux}' height='{$aux_altura}' alt='{$title[$ctitle]} {$parametro}' title='{$title[$ctitle]} {$parametro}'>\n";
							$retorno .= "<td width=\"50\" class=\"grafsub_barra\">" . number_format( $parametro, 2, ",", "." ) . "</td>\n";
							$retorno .= "<td width=\"50\" class=\"grafsub_barra\">{$porcentagem}%</td>\n";
							
							$retorno.= "</td></tr>\n";						
							//incrementa $vcor para mudar a cor da barra 
							if ($vcor==($cont+1))
							{
								$vcor=1;
							}		
							// incrementa $ctitle para trocar o titulo para a barra seguinte conforme sua cor
							if ($ctitle==($cont-1))
							{
								$ctitle=-1;
							}		
							$ctitle++;			
						 }
					}
					// se nao eh array 
					else
					{
						//  calculo necessario para inserir valores conforme a escala $tgrafico
						$aux = ($tgrafico*$v)/$maior;
						//$aux = (($tgrafico/$maior)*$v); 
						$porcentagem = number_format( ( $v / $totalGeral ) * 100, 2, ",", "." );
						// arredondamento dos valores
						$aux = round($aux);
						//montagem das barras do grafico usando imagens
						$retorno .= "<td width={$tgrafico} background=\"imagens/bbg.gif\"><img src='imagens/grafico_hp.png' width='{$aux}' height='5' alt='{$legConteudo} " . number_format( $v, 2, ",", "." ) . "' title='{$legConteudo} " . number_format( $v, 2, ",", "." ) . "'></td>\n";
						$retorno .= "<td width=\"50\" class=\"grafsub_barra\">" . number_format( $v, 2, ",", "." ) . "</td>\n";
						$retorno .= "<td width=\"50\" class=\"grafsub_barra\">{$porcentagem}%</td>\n";
					}
				}
				// gera um espaçamento entre as variaveis caso exista barras de cores diferentes
				//$cont == 0 ? $retorno .= "" :$retorno .= "<tr><td style=\"border-right: 1px solid #000000\">&nbsp;</td></tr>\n"; /* separacao do grafico caso exista mais de 1 parametro */
			}			
			$retorno .= "<tr><td colspan=\"4\" align=\"center\" class=\"graf_titulo\">&nbsp;</td></tr>\n</table>";
			// insere conteudo da legenda caso ela exista 
			// verifica se eh um array ..
			if(is_array($legConteudo))
			{		
				$cont=0;  					// conta qual o numero maximo de barras que é passado para trocar a cor de forma correta			
											// monta o inicio da tabela 
				$retorno .= "\n<table border='0' cellpadding='0' cellspacing='0'  align=\"{$this->globalAlign}\"><tr><td colspan=2 align=center width={$tgrafico}>Legenda</td></tr>\n	";
				$vcor =1;					// controla a cor das barras para exibir na legenda
				foreach ($legConteudo as $legx )
				{									
					switch ($vcor)
						{								
							case 1:
									$color = "grafico_he.png";
									$vcor++;
									break;
							case 2:
									$color = "grafico_hk.png";
									$vcor++;
									break;
							case 3:
									$color = "grafico_hx.png";
									$vcor++;
									break;
							case 4:
									$color = "grafico_hp.png";
									$vcor++;
									break;								
							case 5:
									$color = "grafico_hh.png";
									$vcor++;
									break;
						}		
						// monta a legenda
						$retorno .= "<tr><td align = center>{$legx}</td><td><img src='imagens/{$color}' vspace='0' hspace='0' width='100' height='8' alt='{$legx}' title='{$legx}'></td></tr>\n";																	
						// passa para a cor seguinte
						if ($vcor==($cont+1))
						{
							$vcor=1;
					 	}
					}
					$retorno .= "</table>\n";
			}	
			// se nao eh array ...
			else 
			{			
					if($legConteudo!="")
					{
					// monta a legenda
					$retorno .= "<table border='0' cellpadding='0' cellspacing='0' ><tr><td>&nbsp;</td></tr><tr>\n<td colspan=2 align=center width={$tgrafico}>Legenda </td></tr>\n	";
					$retorno .= "<tr><td width=100 align=center>$legConteudo</td><td><img src='imagens/grafico_hp.png' vspace='0' align= left hspace='0' width='100' height='8' alt='{$legConteudo}' title='{$legConteudo}'></td></tr>\n<tr><td>&nbsp;</td></tr></table>\n";	
					}											
			}	
			$retorno .= "</body></html>";		    
			return $retorno;
		}		
			
		/**
		 * Enter description here...
		 *A função graficoBarraVer gera graficos de barra em posição vertical 
		 *
		 *
		 * @return $retorno; //$retorno possui o conteudo em HTML da pagina
		 *
		 */
		function graficoBarraVer()
		{
			$valor =  $this->arrValores;			
			$tgrafico = $this->intTamGrafico;
			$titulo = $this->titulo;
			$legConteudo = $this->legConteudo ;			
			$maior = 0;								 // verifica qual o maior valor para se align=\"{$this->globalAlign}\"rvir como escala do grafico
			$cont = 0;								 // conta qual o numero maximo de barras que é passado para trocar a cor de forma correta			
			$cont2 =0;								 // conta o número de barras
			foreach ($valor as $rotulo=>$v ) 		
			{
				// ira servir como contador para saber a qtidade de barras
				$indice =0;	
				//verifica se eh um array 
				if(is_array($v))
				{
					foreach ($v as $parametro)	
					{
						if($parametro > $maior)
						{
							// encontra qual o maior valor existente para servir como base conforme escala passada por $tgrafico 								
							$maior = $parametro; 	
						}
						$indice++;
					}	
					if($indice > $cont)
					{
						// verifica a quantidade de barras que será necessario no grafico
						$cont = $indice;												
					}
				}
				else//caso nao seja array 
				{
						if($v > $maior)
						{
							// encontra qual o maior valor existente para servir como base conforme escala passada por $tgrafico 
							
							$maior = $v;				
						}	
				}
				$cont2++;
			}			
		$retorno .="	<html lang=\"pt\">
	<head>
	   	<title> <!-- #&TITULO&# --> </title>
		<link rel=stylesheet type='text/css' href='styles/styles.css'>
		<link rel=stylesheet type='text/css' href='styles/novo.css'>
		
		<script type='text/javascript' src='scripts/padrao.js'></script>
		<script type='text/javascript' src='scripts/novo.js'></script>
		<script type='text/javascript' src='scripts/dom.js'></script>
		
		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
		<meta http-equiv=\"Pragma\" content=\"no-cache\">
		<meta http-equiv=\"Expires\" content=\"-1\">
		<!-- #&REFRESH&# -->
		
		<meta name='Author' content='Prefeitura de Itajaí'>
		<meta name='Description' content='Portal da Prefeitura de Itajaí'>
		<meta name='Keywords' content='portal, prefeitura, itajaí, serviço, cidadão'>
	</head>
	<body>";
		
			$retorno = ereg_replace("<!-- #&TITULO&# -->",$this->titulo,$retorno);
			
			$aux2 = ($cont*$cont2)+$cont2+1; // conta quantas celulas a tabela deve possuir
			$retorno .= "<table border='0' cellpadding='0' cellspacing='0' align=\"{$this->globalAlign}\"><tr><td colspan={$aux2} align=center>{$titulo}</td></tr>	\n";
			$retorno .= "<tr><td align=center>&nbsp;</td>\n";			
			$title = $legConteudo;				  // recebe a legenda 
			$ctitle = 0;						  // variavel que coloca a legenda como titulo nas barras
			$vcor = 1;  						  // variavel para modificar a cor da barra 
			foreach ($valor as $rotulo=>$v) 
			{								
				//verifica se eh um array		
				if(is_array($v))
				{	
				
					foreach ($v as $parametro)	
					{	
					 	switch ($vcor)  		 // troca a cor da barra conforme o valor de $vcor
						{									
							case 1:
									$color = "grafico_vv.png";
									$vcor++;
									break;
							case 2:
									$color = "grafico_vk.png";
									$vcor++;
									break;
							case 3:
									$color = "grafico_vp.png";
									$vcor++;
									break;
							case 4:
									$color = "grafico_vu.png";
									$vcor++;
									break;
									
							case 5:
									$color = "grafico_vh.png";
									$vcor++;
									break;
						}								
						//  calculo necessario para inserir valores conforme a escala $tgrafico
						$aux = ($tgrafico*$parametro)/$maior;
						//$aux = (($tgrafico/$maior)*$parametro);	
						// arrdondamento da variavel
						$aux = round($aux);
						// monta as barras 
						$retorno .= "<td height={$tgrafico} valign=bottom><img src='imagens/{$color}' vspace='0' hspace='0' height='{$aux}' width='8' alt='{$title[$ctitle]} {$parametro}' title='{$title[$ctitle]} {$parametro}'></td>\n";						
						//verifica se a variavel $vcor eh maior q o numero de barras, se sim volta para reiniciar as cores 	
						if ($vcor==($cont+1))
						{
							$vcor=1;
					 	}
					 	//verifica se a variavel $ctitle eh maior q o numero de barras, se sim volta para reiniciar os titulos das barras 	
						if ($ctitle==($cont-1))       
						{
							$ctitle=-1;
					 	}		
					 	$ctitle++;
						
					 	
					 }
					 $retorno .= "<td>&nbsp;</td>";
				}
				//caso nao seja array
				else
				{
						//  calculo necessario para inserir valores conforme a escala $tgrafico
						$aux = ($tgrafico*$v)/$maior;
						//$aux = (($tgrafico/$maior)*$v); 
						// arredondamento de $aux
						$aux = round($aux);
						
						// monta o grafico 
						$retorno .= "<td height={$tgrafico} valign=bottom><img src='imagens/grafico_vp.png' vspace='0' hspace='0' height='{$aux}' width='8' alt='{$legConteudo} " . number_format( $v, 2, ",", "." ) . "' title='{$legConteudo} " . number_format( $v, 2, ",", "." ) . "'></td>\n";						
		
				}
				
				
			}		
			$retorno .= "</tr><tr><td></td>";
			
				$cont += 1;
				
				foreach ($valor as $rotulo=>$v)
				{
					// monta o grafico
					$retorno .= "<td colspan={$cont} style=\"text-align: center; border-top: 1px solid #000000; font-size:12px;\">{$rotulo}</td>\n";
				}
													
			
			$retorno .= "</tr><tr><td>&nbsp;</td></tr>";	
			$retorno .= "</table>";
			$cont-=1;
			// insere conteudo da legenda caso ela exista 
			// verifica se é um array 
			if(is_array($legConteudo))
			{		
				$cont=0;
				$retorno .= "\n<table border='0' cellpadding='0' cellspacing='0'  align=\"{$this->globalAlign}\"><tr><td colspan=2 align=center width={$tgrafico}>Legenda</td></tr>\n	";
				$vcor =1;
				foreach ($legConteudo as $legx )
				{									
					switch ($vcor)  		 // troca a cor da barra conforme o valor de $vcor
						{									
							case 1:
									$color = "grafico_vv.png";
									$vcor++;
									break;
							case 2:
									$color = "grafico_vk.png";
									$vcor++;
									break;
							case 3:
									$color = "grafico_vp.png";
									$vcor++;
									break;
							case 4:
									$color = "grafico_vu.png";
									$vcor++;
									break;
									
							case 5:
									$color = "grafico_vh.png";
									$vcor++;
									break;
						}				
						$retorno .= "<tr><td style=\"text-align: center; font-size:16px\">{$legx}</td><td><img src='imagens/{$color}' vspace='0' hspace='0' width='100' height='8' alt='{$legx}' title='{$legx}'></td></tr>\n";																	
						if ($vcor==($cont+1))
						{
							$vcor=1;
					 	}
					}
					$retorno .= "</table>\n";
			}	
			else // se nao é array 
			{			// se legenda nao é vazia  
					if($legConteudo!="")
					{
					// monta legenda	
					$retorno .= "<table border='0' cellpadding='0' cellspacing='0' ><tr><td>&nbsp;</td></tr><tr>\n<td colspan=2 align=center width={$tgrafico}>Legenda </td></tr>\n	";
					$retorno .= "<tr><td width=100 align=center>$legConteudo</td><td><img src='imagens/grafico_hp.png' vspace='0' align= left hspace='0' width='100' height='8' alt='{$legConteudo}' title='{$legConteudo}'></td></tr>\n<tr><td>&nbsp;</td></tr></table>\n";	
					}											
			}
			$retorno .= "</body></html>";
			return $retorno;
				
		}
	}		
	

?>