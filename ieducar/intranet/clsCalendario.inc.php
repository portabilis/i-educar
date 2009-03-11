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

class clsCalendario{


	var $permite_trocar_ano = 0;

	var $largura_externa  = 400;

	var  $largura_interna = 250;

	var $padding = 5;

	var $COR = array(	0 				   => "#FADEAF",
						"LARANJA_CLARO"    => "#FADEAF",

						1 				   => "#93BDC9",
						"AZUL_ESCURO"      => "#93BDC9",

						2 				   => "#BCD39D",
						"VERDE_ESCURO"     => "#BCD39D",

						3 				   => "#C7D5E0",
						"AZUL_CLARO"       => "#C7D5E0",

						4 				   => "#E5D6DD",
						"ROSA"     		   => "#E5D6DD",

						5 				   => "#E9D1AF",
						"LARANJA_ESCURO"   => "#E9D1AF",

						6 				   => "#E9E6BB",
						"AMARELO"    	   => "#E9E6BB",

						7 				   => "#C9D9CF",
						"VERDE_CLARO"      => "#C9D9CF",

						8 				   => "#DDE3D9",
						"CINZA"            => "#DDE3D9",
						);


	/**
	 *
	 *
	 * @var array
	 */
	var $array_icone= array('A' => array('nome' => 'Anota&ccedil;&otilde;es', 'link' => '/intranet/imagens/i-educar/letra_a.gif')

								  ,''

									);


	var $array_icone_dias = array();


	/**
	 * Array das cores da legenda
	 *
	 * @var array
	 */
	var $array_cor = array('#F7F7F7');

	/**
	 * Array das legendas
	 *
	 * @var array
	 */
	var $array_legenda = array('Padrao');

	/**
	 * Array da cor para os dias da semana
	 *
	 * @var array
	 */
	var $array_cor_dia_padrao = array();

	/**
	 * Dias do mes
	 *
	 * @var array
	 */
	var $array_dias = array();

	/**
	 * acao quando for clicado em cima do dia
	 *
	 * @var array
	 */
	var $all_days_onclick;

	/**
	 * acao quando for clicado em cima do dia
	 *
	 * @var array
	 */
	var $all_days_url;


	/**
	 * acao quando for clicado em cima do dia
	 *
	 * @var array
	 */
	var $array_onclick_dias = array();

	/**
	 * Adicioar um div flutuante quando posicionar o mouse sobre o dia
	 *
	 * @var unknown_type
	 */
	var $array_div_flutuante_dias = array();

	function resetAll(){
		$this->array_div_flutuante_dias = array();
		$this->array_onclick_dias = array();
		$this->array_dias = array();
		$this->array_cor_dia_padrao = array();
		$this->array_legenda = array('Padrao');
		$this->array_cor = array('#F7F7F7');
		$this->largura_externa  = 400;

		$this->largura_interna = 250;

		$this->padding = 5;
	}

	function setLargura($int_largura){

		$this->largura_externa = $int_largura;

		if($int_largura > 250)
			$this->largura_interna = $this->largura_externa - 121;
		else
			$this->largura_interna = '40%';

		$this->padding = (floor((($int_largura - 30) / 7 ) / 10) * 2);

	}

	function diaDescricao($arr_dias,$array_mensagem_dias){

		if(is_array($arr_dias)){
			foreach ($arr_dias as $key => $dia) {
				$this->array_div_flutuante_dias[$key] = $array_mensagem_dias[$key];
			}

		}
	}

	function diaOnClick($arr_dias,$array_onclick_dias){

		if(is_array($arr_dias)){
			foreach ($arr_dias as $key => $dia) {
				$this->array_onclick_dias[$dia][] = $array_onclick_dias[$key];
			}

		}

	}

	function adicionarIconeDias($arr_dias,$id_icone){

		if(is_array($arr_dias)){

			foreach ($arr_dias as $key => $dia) {
				if(key_exists($id_icone,$this->array_icone))
					$this->array_icone_dias[$dia] = $id_icone;
					$this->array_icone[$id_icone]['utilizado'] = true;
			}

		}

	}

	/**

	 * 	 *
	 * @param STRING $str_legenda
	 * @param STRING $str_cor
	 *  #FADEAF - 0 - LARANJA_CLARO
     *
	 *	#93BDC9 - 1 - AZUL_ESCURO
     *
	 * 	#BCD39D - 2 - VERDE_ESCURO
     *
	 *	#C7D5E0 - 3 - AZUL_CLARO
     *
	 *	#E5D6DD - 4 - ROSA
     *
	 *	#E9D1AF - 5 - LARANJA_ESCURO
     *
	 *	#E9E6BB - 6 - AMARELO
     *
	 *  #C9D9CF - 7 - VERDE_CLARO
     *
	 *	#DDE3D9 - 8 - CINZA
	 */

	function adicionarLegenda($str_legenda, $str_cor){

		//$this->array_cod_legenda[] =  $str_cod_legenda;
		$key = array_search($str_legenda,$this->array_legenda);
		if(!empty($key))
			if($this->array_legenda[$key] == $str_legenda)
				return;
		$this->array_legenda[] = $str_legenda;
		$str_cor = strtoupper($str_cor);
		$this->array_cor[] = $this->COR["{$str_cor}"];


	}
	/**
	 * 	 *Legenda padrao
	 * @param STRING $str_legenda
	 * @param STRING $str_cor
	 *  #FADEAF - 0 - LARANJA_CLARO
     *
	 *	#93BDC9 - 1 - AZUL_ESCURO
     *
	 * 	#BCD39D - 2 - VERDE_ESCURO
     *
	 *	#C7D5E0 - 3 - AZUL_CLARO
     *
	 *	#E5D6DD - 4 - ROSA
     *
	 *	#E9D1AF - 5 - LARANJA_ESCURO
     *
	 *	#E9E6BB - 6 - AMARELO
     *
	 *  #C9D9CF - 7 - VERDE_CLARO
     *
	 *	#DDE3D9 - 8 - CINZA
	 */
	function setLegendaPadrao($str_legenda, $str_cor = "#F7F7F7"){

		$this->array_legenda[0] = $str_legenda;
		$this->array_cor[0] = $this->COR["{$str_cor}"];

	}

	/**

	 * 	 *Atribui uma cor padrao para os dias(0 Dom - 6 Sab) da semana ex - todas as segundas 1
	 * @param ARRAY $arr_dia_semana
	 * @param STRING $str_cor
	 *  #FADEAF - 0 - LARANJA_CLARO
     *
	 *	#93BDC9 - 1 - AZUL_ESCURO
     *
	 * 	#BCD39D - 2 - VERDE_ESCURO
     *
	 *	#C7D5E0 - 3 - AZUL_CLARO
     *
	 *	#E5D6DD - 4 - ROSA
     *
	 *	#E9D1AF - 5 - LARANJA_ESCURO
     *
	 *	#E9E6BB - 6 - AMARELO
     *
	 *  #C9D9CF - 7 - VERDE_CLARO
     *
	 *	#DDE3D9 - 8 - CINZA
	 */
	function setCorDiaSemana($arr_dia_semana, $str_cor){
		$str_cor = strtoupper($str_cor);
		if(is_array($arr_dia_semana))
			foreach ($arr_dia_semana as $dia)
				$this->array_cor_dia_padrao["{$dia}"] = $this->COR["{$str_cor}"];
		else
			$this->array_cor_dia_padrao["{$arr_dia_semana}"] = $str_cor;
	}
	/**
	 * Adiciona os dias do mes com a sua legenda
	 *
	 * @param unknown_type $str_cod_legenda
	 * @param unknown_type $dias
	 */
	function adicionarArrayDias($str_cod_legenda,$dias){

		$key = array_shift(array_keys($this->array_legenda, $str_cod_legenda));
		foreach ($dias as $dia)
		{
			$dia = (int)$dia;
			$this->array_dias["{$dia}"] = $key;
		}

		ksort($this->array_dias);

	}

	/**
	 * retorna o calendario
	 *
	 * @param unknown_type $mes
	 * @param unknown_type $ano
	 * @return unknown
	 */
	function getCalendario($mes,$ano,$nome,$mixVariaveisMantidas) {

		$array_color = $array_color;
		$array_legenda = $array_legenda;

		if(isset($mixVariaveisMantidas["{$nome}_mes"]) && is_numeric($mixVariaveisMantidas["{$nome}_mes"]))
			$mes = $mixVariaveisMantidas["{$nome}_mes"];

		if(isset($mixVariaveisMantidas["{$nome}_ano"]) && is_numeric($mixVariaveisMantidas["{$nome}_ano"]) && $this->permite_trocar_ano == true)
			$ano = $mixVariaveisMantidas["{$nome}_ano"];

	     // Array com todos os dias da semana
	     $diasDaSemana = array('DOM','SEG','TER','QUA','QUI','SEX','SAB');
	     $diasDaSemana = array('DOM','SEG','TER','QUA','QUI','SEX','SAB');

		$mesesDoAno = array(
								"1" => "JANEIRO"
								,"2" => "FEVEREIRO"
								,"3" => "MAR&Ccedil;O"
								,"4" => "ABRIL"
								,"5" => "MAIO"
								,"6" => "JUNHO"
								,"7" => "JULHO"
								,"8" => "AGOSTO"
								,"9" => "SETEMBRO"
								,"10" => "OUTUBRO"
								,"11" => "NOVEMBRO"
								,"12" => "DEZEMBRO"
							);

	     // Qual o primeiro dia do mes
	     $primeiroDiaDoMes = mktime(0,0,0,$mes,1,$ano);

	     // Quantos dias tem o mes
	     $NumeroDiasMes = date('t',$primeiroDiaDoMes);

	     // Retrieve some information about the first day of the
	     // month in question.
		 $dateComponents = getdate($primeiroDiaDoMes);

	     // What is the name of the month in question?
	     $NomeMes = $mesesDoAno[$dateComponents['mon']];

	     // What is the index value (0-6) of the first day of the
	     // month in question.
	     $DiaSemana = $dateComponents['wday'];

	     // Create the table tag opener and day headers
 			//GET
 			$linkFixo = $strUrl . "?";
			if( is_array( $mixVariaveisMantidas ) )
			{
				foreach ( $mixVariaveisMantidas as $key => $value )
				{
					if( $key != "{$nome}_mes" &&  $key != "{$nome}_ano")
					{
							$linkFixo .= "$key=$value&";
					}
				}
			}
			else
			{
				if( is_string( $mixVariaveisMantidas ) )
				{
					$linkFixo .= "$mixVariaveisMantidas&";
				}
			}
			//
			$linkFixo  = $linkFixo == "?" ? "" : $linkFixo;

		if($mes == 12)
		{
			if($this->permite_trocar_ano)
			{
				$mes_posterior_mes = 1;
				$mes_anterior_mes = 11;
				$ano_posterior_mes = $ano + 1;
				$ano_anterior_mes = $ano;

				$mes_ano = $mes;
				$ano_posterior_ano = $ano + 1;
				$ano_anterior_ano = $ano - 1;

				//$ano++;
			}else{
				$mes_posterior_mes = 1;
				$mes_anterior_mes = 11;
				$ano_posterior_mes = $ano;
				$ano_anterior_mes = $ano;
			}
		}elseif ($mes == 1){
			if($this->permite_trocar_ano)
			{
				$mes_posterior_mes = 2;
				$mes_anterior_mes = 12;
				$ano_posterior_mes = $ano;
				$ano_anterior_mes = $ano - 1;

				$mes_ano = $mes;
				$ano_posterior_ano = $ano + 1;
				$ano_anterior_ano = $ano - 1;
			}else{
				$mes_posterior_mes = 2;
				$mes_anterior_mes = 12;
				$ano_posterior_mes = $ano;
				$ano_anterior_mes = $ano;
			}
		}
		else{
			if($this->permite_trocar_ano)
			{
				$mes_posterior_mes = $mes + 1;
				$mes_anterior_mes = $mes - 1;
				$ano_posterior_mes = $ano;
				$ano_anterior_mes = $ano;

				$mes_ano = $mes;
				$ano_posterior_ano = $ano + 1;
				$ano_anterior_ano = $ano - 1;
			}else{
				$mes_posterior_mes = $mes + 1;
				$mes_anterior_mes = $mes - 1;
				$ano_posterior_mes = $ano;
				$ano_anterior_mes = $ano;
			}
		}



		$form = "<form id=\"form_calendario\" name=\"form_calendario\" method=\"post\" action=\"{$linkFixo}\">
					<input type=\"hidden\" id=\"nome\" name=\"nome\" value=\"\">
					<input type=\"hidden\" id=\"dia\" name=\"dia\" value=\"\">
					<input type=\"hidden\" id=\"mes\" name=\"mes\" value=\"\">
					<input type=\"hidden\" id=\"ano\" name=\"ano\" value=\"\">
				</form>";

		if($this->permite_trocar_ano == true)
		{
			$select = "<select name=\"mes\" id=\"smes\" onchange=\"acaoCalendario('{$nome}','',this.value,'{$ano}');\">\">";
			foreach ($mesesDoAno as $key => $mes_)
			{
				$selected = ($dateComponents['mon'] == $key) ? "selected='selected'" : "";
				$select .="<option value='{$key}' $selected>{$mes_}</option>";

			}

			$select .= "</select>";

			$cab = "<a href='#' onclick='acaoCalendario(\"{$nome}\",\"\",\"{$mes_anterior_mes}\",\"{$ano_anterior_mes}\")'><img  src='/intranet/imagens/i-educar/seta_esq.gif' border='0' style='margin-right:5px;' alt='M&ecirc;s Anterior'></a>{$select}<a href='#' onclick='acaoCalendario(\"{$nome}\",\"\",\"{$mes_posterior_mes}\",\"{$ano_posterior_mes}\")'><img src='/intranet/imagens/i-educar/seta_dir.gif' border='0' style='margin-left:5px;' alt='M&ecirc;s Posterior'></a>
					<a href='#' onclick='acaoCalendario(\"{$nome}\",\"\",\"{$mes_ano}\",\"{$ano_anterior_ano}\")'><img src='/intranet/imagens/i-educar/seta_esq.gif' border='0' style='margin-right:5px;' alt='M&ecirc;s Anterior'></a>{$ano}<a href='#' onclick='acaoCalendario(\"{$nome}\",\"\",\"{$mes_ano}\",\"{$ano_posterior_ano}\")'><img src='/intranet/imagens/i-educar/seta_dir.gif' border='0' style='margin-left:5px;' alt='M&ecirc;s Posterior'></a>";
		}else
		{
			$cab = "<a href='javascript:void(1);' onclick='acaoCalendario(\"{$nome}\",\"\",\"{$mes_anterior_mes}\",\"{$ano_anterior_mes}\")'><img src='/intranet/imagens/i-educar/seta_esq.gif' border='0' style='margin-right:5px;' alt='M&ecirc;s Anterior'></a>{$NomeMes}&nbsp;{$ano}href='#' onclick='acaoCalendario(\"{$nome}\",\"\",\"{$mes_posterior_mes}\",\"{$ano_anterior_mes}\")'><img src='/intranet/imagens/i-educar/seta_dir.gif' border='0' style='margin-left:5px;' alt='M&ecirc;s Posterior'>";
		}



	     $calendario  = "<div id='d_calendario' ><table class='calendar' cellspacing='0' cellpadding='0' width='{$this->largura_externa}' border='0'>";
	     $calendario .= "<tr><td class='cal_esq' >&nbsp;</td><td background='/intranet/imagens/i-educar/cal_bg.gif' width='100%' class='mes'>$cab</td><td align='right' class='cal_dir'>&nbsp;</td></tr>";
	     $calendario .= "<tr><td colspan='3' class='bordaM' >$form";
		 $calendario .= "<table cellspacing='0' cellpadding='0' width='100%' border=0 class='header'><tr>";

		 // Create the calendar headers
	     foreach($diasDaSemana as $day) {
	     	if(end($diasDaSemana) == $day)
	     	  $calendario .= "<td style='width: 45px;'>$day</td>";
	     	 else
	     	  $calendario .= "<td style='border-right: 1px dotted #FFFFFF;width: 45px;'>$day</td>";
	     }

	     $calendario .= "</tr>";
	     $calendario .= "</table>";
	     $calendario .= "</td></tr>";

	     $calendario .= "<tr><td colspan='3' style='padding: 3px' valign='top' class='bordaF'>";
		 $calendario .= "<table cellspacing='5' cellpadding='0' width='100%' >";
	     // Create the rest of the calendar

	     // Initiate the day counter, starting with the 1st.

	     $diaCorrente = 1;

	     $calendario .= "<tr>";

	     // The variable $DiaSemana is used to
	     // ensure that the calendar
	     // display consists of exactly 7 columns.

	     if ($DiaSemana > 0) {
	     	$completar_dias = $DiaSemana;
	     	   $day = date ("d", mktime (0,0,0,$dateComponents["mon"],-$completar_dias+1,$dateComponents["year"]));
	     	for($a = 0 ; $a < $completar_dias ; $a++)
	     	{
				$calendario .= "<td class='dayLastMonth' style='padding-left:{$this->padding}px;'>{$day}</td>";
				$day++;
	     	}

	     }
	     while ($diaCorrente <= $NumeroDiasMes) {

	          // Seventh column (Saturday) reached. Start a new row.

	          if ($DiaSemana == 7) {

	               $DiaSemana = 0;
	               $calendario .= "</tr><tr>";

	          }

			$style_dia ="background-color:{$this->array_cor[0]};";

			if($this->array_cor_dia_padrao[$DiaSemana])
				$style_dia ="background-color:{$this->array_cor_dia_padrao[$DiaSemana]};";


			if (key_exists($diaCorrente,$this->array_dias) /*&& $DiaSemana != 0 && $DiaSemana != 6*/) {
			 	$key = $this->array_dias[$diaCorrente];
			 	$cor = $this->array_cor[$key];
			 	$style_dia ="background-color:{$cor};";
			 }


			 $onclick = "";

			if($this->all_days_onclick)
			{
				$onclick = "onclick=\"{$this->all_days_onclick}\"";
			}elseif($this->all_days_url)
			{
				$onclick = "onclick=\"document.location='{$this->all_days_url}&dia={$diaCorrente}&mes={$mes}&ano={$ano}';\"";
			}

			if (key_exists($diaCorrente,$this->array_onclick_dias))
			{
				$onclick = "onclick=\"{$this->array_onclick_dias[$diaCorrente]};\"";
				//break;
			}

			$icone = "";
			if(key_exists($diaCorrente,$this->array_icone_dias)){
					$icone = "<img src='{$this->array_icone[$this->array_icone_dias[$diaCorrente]]["link"]}' border='0'  align='right' style='padding-right:5px;'>";
			}

			 $message = "";
 			$diaCorrente_ = strlen($diaCorrente) == 1 ? "0".$diaCorrente : $diaCorrente;
 			$NomeMes = strtolower($NomeMes);

			 if (key_exists($diaCorrente,$this->array_div_flutuante_dias)) {
			 	$message = "onmouseover=\"ShowContent('{$diaCorrente}','{$mes}','{$ano}','{$nome}'); return true;\"";
			 	$mouseout = "onmouseout=\"HideContent(event,'{$diaCorrente}','{$mes}','{$ano}','{$nome}')\" ";
			 	$mensagens .= "<div $mouseout class='div_info' style='display:none; z-index: 10;' id=\"{$nome}_div_dia_{$diaCorrente}{$mes}{$ano}\">
			 						<div style='margin:0px 15px 0px 0px;font-size: 14px; z-index: 0; border-bottom: 1px solid #000000;'>{$diaCorrente_} de {$NomeMes} de $ano
			 						</div>
							      	<div style='align:left;padding-top:5px;z-index: 0;' class='dia'>
									{$this->array_div_flutuante_dias[$diaCorrente]}
									</div>
							</div>";

			 }
		
               $calendario .= "<td style='{$style_dia}padding-left:{$this->padding}px;' id='{$nome}_td_dia_{$diaCorrente}{$mes}{$ano}' class='day' $onclick $message>{$icone} $diaCorrente_</td>";

	          // Increment counters

	          $diaCorrente++;
	          $DiaSemana++;

	     }

	     // Complete the row of the last week in month, if necessary

	     if ($DiaSemana != 7) {

	          $remainingDays = 7 - $DiaSemana;
	       
		     	for($a = 1 ; $a <= $remainingDays ; $a++)
		     	{
				//dayLastMonth
					$calendario .= "<td class='dayLastMonth' style='padding-left:{$this->padding}px;'>{$a}</td>";
			
		     	}

	     }

	     if($this->array_legenda)
	     {

		     $calendario .= "<tr><td colspan='7'>";
		     $calendario .= "<table cellspacing=2 cellpadding=0 class='legenda' width=100%>
		     				<tr>";
		     $cont = 0;
		     foreach ($this->array_legenda as $key => $legenda){
		     	$style = "style='background-color:{$this->array_cor["{$key}"]};'";
		     	$calendario .= "<td {$style} class='cor'>&nbsp;</td><td>{$legenda}</td>";
		     	$cont++;
		     	if($cont == 3){
		     		$calendario .= "</tr><tr>";
		     		$cont = 0;
		     	}
		     }
			     $calendario .= "</tr></table>";
			     $calendario .="</td></tr>";
	     }

	     if($this->array_icone_dias)
	     {

		     $calendario .= "<tr><td colspan='7'>";
		     $calendario .= "<table cellspacing=2 cellpadding=0 class='legenda' width=100%>
		     				<tr align='left'>";
		     $cont = 0;

		     foreach ($this->array_icone as $key => $legenda){
		     	if($legenda['utilizado'])
		     	{
		     		$style = "style='background-color:{$this->array_cor["{$key}"]};'";
					$icone = "";
					$icone = "<img src='{$this->array_icone[$key]["link"]}' border='0'  align='right' style='padding-right:5px;'>";
			     	$calendario .= "<td {$style} align='left'>$icone</td><td width='100%'>{$legenda['nome']}</td>";
			     	$cont++;
			     	if($cont == 3){
			     		$calendario .= "</tr><tr>";
			     		$cont = 0;
			     	}
		     	}
		     }
		     $calendario .= "</tr></table>";
		     $calendario .="</td></tr>";
	     }
	 		$calendario .= "</table>";
	     $calendario .= "</td></tr>";
		 $calendario .= "</table></div>";
		 $calendario .= $mensagens;
	     return $calendario;

	}

};
?>
