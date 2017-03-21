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
	class clsGraficoDiario
	{
		var $arrayValores;
		var $tableResult;
		var $datainicio = array();
		var $datafim = array();
		var $dataAtual = array();
		var $mostraWeekend;
		var $legenda;
		var $titulo;
		var $mesAtual = 0;
		var $maxVal = 0;
		var $altura = 200;
		var $timeDataAtual;
		var $mesesExtenso = array( "", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" );
		var $ultimoDiaMes = array( 0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
		var $diasSemana = array( "Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado" );
		
		function clsGraficoDiario( $arrayValores, $dataInicio, $dataFim=false, $titulo=false, $legenda=false, $mostraWeekend=false )
		{
			$this->arrayValores = $arrayValores;
			$this->datainicio = $dataInicio;
			$this->dataAtual = $dataInicio;
			$this->legenda = $legenda;
			$this->titulo = $titulo;
			$this->mostraWeekend = $mostraWeekend;
			$this->timeDataAtual = strtotime( $this->dataAtual[2] . "/" . $this->dataAtual[1] . "/" . $this->dataAtual[0] );
			if( $dataFim )
			{
				$this->datafim = $dataFim;
			}
			else 
			{
				$this->datafim[0] = date( "d", time() );
				$this->datafim[1] = date( "m", time() );
				$this->datafim[2] = date( "Y", time() );
			}
			foreach ( $this->arrayValores AS $ano => $anoValue )
			{
				foreach ( $anoValue AS $mes => $mesValue )
				{
					foreach ( $mesValue AS $dia => $diaValue )
					{
						foreach ( $diaValue AS $key => $valor )
						{
							if( $valor > $this->maxVal )
							{
								$this->maxVal = $valor;
							}
						}
					}
				}
			}
			$this->maxVal = 100;
		}
		
		function addZero( $valor )
		{
			if( $valor < 10 )
			{
				return "0" . $valor;
			}
			return $valor;
		}
		
		function linhaDias( $mes )
		{
			$retorno = "";
			// coluna dos numeros
			$retorno .= "<td class=\"graf_dias\"><span class=\"graf_diasem\">Valores</td>\n";
			
			// coloca a linha abaixo do grafico com os dias do mes
			for( $i = 1; $i <= 31; $i++ )
			{
				// até o ultimo dia do mes ele coloca numeros, depois coloca em branco
				if( $i <= $this->ultimoDiaMes[$mes + 0] )
				{
					$strData =  $this->dataAtual[2] . "/" . $this->mesAtual . "/" . $this->addZero( $i );
					$indiceSemana = date( "w", strtotime( $strData ) );
					$legDia = substr( $this->diasSemana[$indiceSemana], 0, 3 );
					$estilo = ( $indiceSemana == 6 || $indiceSemana == 0 ) ? "graf_diafindi": "graf_dias";
					$retorno .= "<td class=\"$estilo\">" . $this->addZero( $i ) . "<br><span class=\"graf_diasem\">{$legDia}</td>\n";
				}
				else 
				{
					$retorno .= "<td class=\"graf_dias\">&nbsp;</td>\n";
				}
			}
			$retorno .= "<td class=\"graf_dias\">Média</td>\n";
			$retorno .= "</tr><tr><td colspan=\"33\" class=\"graf_divisoes\">&nbsp;</td></tr>\n";
			return $retorno;
		}
		
		function insereMedia( $ultimoDia, $somaVals, $diasUteis )
		{
			$retorno = "";
			// completa os campos de grafico em branco até o dia 31
			$retorno .= $this->preencheGrafVazio( $ultimoDia, 31 );

			// coloca o grafico com as medias do mes
			$mediaVals[0] = $somaVals[0] / $diasUteis;
			$mediaVals[1] = $somaVals[1] / $diasUteis;
			$mediaVals[2] = $somaVals[2] / $diasUteis;
			$mediaVals[3] = $somaVals[3] / $diasUteis;
			
			$alturas = array();
			$alturas[0] = ceil( $mediaVals[0] * $this->altura / $this->maxVal );
			$alturas[1] = ceil( $mediaVals[1] * $this->altura / $this->maxVal );
			$alturas[2] = ceil( $mediaVals[2] * $this->altura / $this->maxVal );
			$alturas[3] = ceil( $mediaVals[3] * $this->altura / $this->maxVal );
			
			$retorno .= "<td valign=\"bottom\" class=\"graf_graficos\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>\n";
			$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b1.gif\" width=\"5\" height=\"" . min( ceil( $mediaVals[0] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $mediaVals[0], 2, ",", "." ) . "\"></td>\n";
			$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b2.gif\" width=\"5\" height=\"" . min( ceil( $mediaVals[1] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $mediaVals[1], 2, ",", "." ) . "\"></td>\n";
			$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b3.gif\" width=\"5\" height=\"" . min( ceil( $mediaVals[2] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $mediaVals[2], 2, ",", "." ) . "\"></td>\n";
			$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b4.gif\" width=\"5\" height=\"" . min( ceil( $mediaVals[3] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $mediaVals[3], 2, ",", "." ) . "\"></td>\n";
			$retorno .= "</tr></table></td>\n";
			$retorno .= "</tr><tr>\n";
			return $retorno;
		}
		
		function preencheGrafVazio( $ini, $fim )
		{
			$retorno = "";
			for( $i = $ini; $i < $fim; $i++ )
			{
				$retorno .= "<td class=\"graf_graficos\">&nbsp;</td>\n";
			}
			return $retorno;
		}
		
		function geraHTML( $insideTemplate=false )
		{
			$diasArray = array();
			$somaVals = array( 0, 0, 0, 0 );
			$diasUteis = 0;
			$ultimoDia = 0;
			$fechouMes = false;
			$retorno = "";
			if( ! $insideTemplate )
			{
				$retorno.= "<html><head><title>Grafico Diario</title><link rel=stylesheet type='text/css' href='styles/styles.css'><link rel=stylesheet type='text/css' href='styles/novo.css'></head><body>";
			}
			if( $this->titulo )
			{
				$retorno .= "<table border=\"0\" cellpading=\"0\" cellspacing=\"0\"><tr><td align=\"center\" class=\"graf_titulo\">{$this->titulo}</td></tr></table><br>\n";
			}
			$retorno .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";
			while ( $this->dataAtual[2] < $this->datafim[2] || ( $this->dataAtual[2] == $this->datafim[2] && $this->dataAtual[1] < $this->datafim[1] ) || ( $this->dataAtual[2] == $this->datafim[2] && $this->dataAtual[1] == $this->datafim[1] && $this->dataAtual[0] < $this->datafim[0] ) ) {
				$fechouMes = false;
				if( isset( $this->arrayValores[$this->dataAtual[2]][$this->dataAtual[1]][$this->dataAtual[0]] ) )
				{
					$infoDoDia = $this->arrayValores[$this->dataAtual[2]][$this->dataAtual[1]][$this->dataAtual[0]];
				}
				else 
				{
					$infoDoDia = array( 0, 0, 0, 0 );
				}
				// virou o mes?
				if( $this->mesAtual != $this->dataAtual[1] )
				{
					// teve mes passado? 
					if( $this->mesAtual )
					{
						$retorno .= $this->insereMedia( $ultimoDia, $somaVals, $diasUteis );
						$retorno .= $this->linhaDias( $this->dataAtual[1] );
					}
					
					// escreve a linha em destaque para o proximo mes
					$retorno .= "</tr><tr><td colspan=\"33\" class=\"graf_meses\">" . $this->mesesExtenso[$this->dataAtual[1] + 0] . " de {$this->dataAtual[2]}</td></tr><tr background=\"imagens/graf_bg.gif\"><td background=\"imagens/bg_graf_dias.png\">&nbsp;</td>\n";
					
					// se é o primeiro mes preenche com campos em branco até o dia de inicio
					if( ! $this->mesAtual )
					{
						$retorno .= $this->preencheGrafVazio( 1, $this->dataAtual[0] );
					}
					
					$this->mesAtual = $this->dataAtual[1];
					$fechouMes = true;
					$somaVals = array( 0, 0, 0, 0 );
					$diasUteis = 0;
				}
				$diaDaSemana = date( "w", $this->timeDataAtual );
				if( ! $this->mostraWeekend && ( $diaDaSemana == 6 || $diaDaSemana == 0 ) )
				{
					$retorno .= $this->preencheGrafVazio( 0, 1 );
				}
				else 
				{
					// escreve os valores do dia atual
					$retorno .= "<td valign=\"bottom\" class=\"graf_graficos\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>\n";
					$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b1.gif\" width=\"5\" height=\"" . min( ceil( $infoDoDia[0] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $infoDoDia[0], 2, ",", "." ) . "\"></td>\n";
					$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b2.gif\" width=\"5\" height=\"" . min( ceil( $infoDoDia[1] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $infoDoDia[1], 2, ",", "." ) . "\"></td>\n";
					$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b3.gif\" width=\"5\" height=\"" . min( ceil( $infoDoDia[2] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $infoDoDia[2], 2, ",", "." ) . "\"></td>\n";
					$retorno .= "<td valign=\"bottom\"><img src=\"imagens/b4.gif\" width=\"5\" height=\"" . min( ceil( $infoDoDia[3] * $this->altura / $this->maxVal ), $this->altura ) . "\" borde=\"0\" title=\"" . number_format( $infoDoDia[3], 2, ",", "." ) . "\"></td>\n";
					$retorno .= "</tr></table></td>\n";
					
					$somaVals[0] += $infoDoDia[0];
					$somaVals[1] += $infoDoDia[1];
					$somaVals[2] += $infoDoDia[2];
					$somaVals[3] += $infoDoDia[3];
					$diasUteis++;
				}
				$ultimoDia = $this->dataAtual[0];
				
				// passa a data atual para o proximo dia
				$this->timeDataAtual += 60 * 60 * 24;
				$this->dataAtual[0] = date( "d", $this->timeDataAtual );
				$this->dataAtual[1] = date( "m", $this->timeDataAtual );
				$this->dataAtual[2] = date( "Y", $this->timeDataAtual );
			}
			// se o grafico acabou no meio de um mes vamos termina-lo
			if( ! $fechouMes )
			{
				$retorno .= $this->insereMedia( $ultimoDia, $somaVals, $diasUteis );
				$retorno .= $this->linhaDias( $this->dataAtual[1] );
			}
			$retorno .= "</table>";
			
			if( is_array( $this->legenda ) )
			{
				$retorno .= "<br><br><table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";
				$retorno .= "<tr><td class=\"graf_legenda\">{$this->legenda[0]}:</td><td class=\"graf_legenda\"><img src=\"imagens/bl1.gif\" border=\"\" width=\"15\" height=\"5\"></td></tr>\n";
				$retorno .= "<tr><td class=\"graf_legenda\">{$this->legenda[1]}:</td><td class=\"graf_legenda\"><img src=\"imagens/bl2.gif\" border=\"\" width=\"15\" height=\"5\"></td></tr>\n";
				$retorno .= "<tr><td class=\"graf_legenda\">{$this->legenda[2]}:</td><td class=\"graf_legenda\"><img src=\"imagens/bl3.gif\" border=\"\" width=\"15\" height=\"5\"></td></tr>\n";
				$retorno .= "<tr><td class=\"graf_legenda\">{$this->legenda[3]}:</td><td class=\"graf_legenda\"><img src=\"imagens/bl4.gif\" border=\"\" width=\"15\" height=\"5\"></td></tr>\n";
				$retorno .= "</table><br><br>\n";
			}
			if( ! $insideTemplate )
			{
				$retorno.= "</body></html>";
			}
			return $retorno;
		}
		
		function printHTML()
		{
			echo $this->geraHTML();
		}
	}		
?>