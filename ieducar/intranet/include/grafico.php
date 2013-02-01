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
//legenda(array("OI","as","Carroçaassss","Bacalhao","Arroz","Soja","Feijao","Macarrão","Batata"));
@session_start();
$grafico = $_SESSION['grafico'];
$legenda = $_SESSION['legenda'];
@session_write_close();
if($legenda)
{
	legenda($legenda["legenda"],3,$legenda["nome_imagem"]);
	@session_start();
	unset($_SESSION['legenda']);
	@session_write_close();
	
}
if($grafico)
{
	$titulo = $grafico['titulo'];
	$legenda_intervalo =  $grafico['legenda_intervalo'];
	$qtd_linhas_horizontais = $grafico['qtd_linha'];
	$font_size = $grafico['font_size'];
	$width = $grafico['width'];
	$height = $grafico['height'];
	$coordenadas = $grafico['coordenadas'];
	$image_name = $grafico['nome_imagem'];
	grafico_linhas($titulo,$legenda_intervalo,$qtd_linhas_horizontais,$font_size,$width,$height,$image_name, $coordenadas );
	@session_start();
	unset($_SESSION['grafico']);
	@session_write_close();
}
function drawdot( $coords, $tipo, $valor, $im, $linecolor ) {

		$inner_text = $linecolor[$tipo];// imagecolorallocate ( $im, 255, 255, 255 );
		imageline ( $im, $coords[0], $coords[1], $coords[2], $coords[3], $linecolor[$tipo] );
		imageellipse ( $im, $coords[0], $coords[1], 3, 3, $linecolor );
		if($valor==0)
			$valor = "";
		imagestring( $im, 3, $coords[0] + 8, $coords[1] - 10, $valor, $inner_text );
}

function grafico_linhas ( $nm_grafico = false,$array_legendas_intervalos = false, $qtd_linhas_horizontais = false, $font_size = 3, $width = 800, $height = 600,$image_name = false )
{
	$coordenadas = array();
	$coordenadas = func_get_arg(7);
	
	$width 	= $width;
	$height = $height;

	// Distancias entre a área total da imagem e a área do gráfico
	
	$deslocamento_graph_up 	  = 50;
	$deslocamento_graph_down  = 35;
	$deslocamento_graph_right = 60;
	$deslocamento_graph_left  = 20;
	
	$height_graph = ($height - ($deslocamento_graph_down+$deslocamento_graph_up) );
	
	
	// Tamanho da Fonte
	
	$fonts = array(
		"1" => 4.77,
		"2" => 6,
		"3" => 6.75,
		"4" => 7.83,
		"5" => 8.6
		
		);
	
	if($font_size > 5)
	{
		$font_size = 5;
	}
	$font_deslocamento = $fonts[$font_size];
	
	// Define Imagem
	$im = @imagecreate( $width, $height ) or die( "Cannot Initialize new GD image stream" );
			
	$background_color_image = imagecolorallocate( $im, 0, 0, 0);
	$text_color = imagecolorallocate ( $im, 0, 0, 0);
	$back_line = imagecolorallocate ( $im, 255, 255, 255 );
	$inner_text = imagecolorallocate ( $im, 255, 255, 255 );
	$background_color_graph = imagecolorallocate($im,220,220,220);
	$linecolor = array( 
		imagecolorallocate( $im, 100, 100, 100), 
		imagecolorallocate( $im,  49,  13, 244), 
		imagecolorallocate( $im, 220,   0,   0), 
		imagecolorallocate( $im, 152, 001, 154),
		imagecolorallocate( $im, 255, 100,   1), 
		imagecolorallocate( $im, 200, 151,  10), 
		imagecolorallocate( $im,  50, 180,  60), 
		imagecolorallocate( $im,  60, 160, 195), 
		imagecolorallocate( $im, 123, 096,  15), 
		imagecolorallocate( $im, 144, 143, 255), 
		imagecolorallocate( $im, 239, 100, 212),
		imagecolorallocate( $im,  39,  95,  35)
	);

	// Desenha Área do Gráfico
	
	imagefilledpolygon($im,array($deslocamento_graph_right,$deslocamento_graph_up,$deslocamento_graph_right,$height-$deslocamento_graph_down,$width-$deslocamento_graph_left,$height-$deslocamento_graph_down,$width-$deslocamento_graph_left,$deslocamento_graph_up),4,$background_color_graph);
	
	// Nome do Gráfico
	$font_size_titulo = 5;
	imagestring($im,$font_size_titulo, ($width - (strlen($nm_grafico)*$fonts[5]))/2, 15,$nm_grafico,$inner_text);

	$espaco_entre_palavras = 5;
	// Desenha Intervalos
	if(count($array_legendas_intervalos) > 0)
	{
		$posicao_anterior = $deslocamento_graph_right;
		$espaco_intervalo = ($width - $deslocamento_graph_right-$deslocamento_graph_left)/count($array_legendas_intervalos);
		
		for($i = 1; $i<count($array_legendas_intervalos); $i++)
		{
			//die(strlen($array_legendas_intervalos[$i-1]) * $font_deslocamento .">". ($espaco_intervalo-$espaco_entre_palavras));
			if($espaco_intervalo-$espaco_entre_palavras > 0)
			{
				while (strlen($array_legendas_intervalos[$i-1]) * $font_deslocamento > ($espaco_intervalo-$espaco_entre_palavras)) {
					$array_legendas_intervalos[$i-1] = substr($array_legendas_intervalos[$i-1],0,strlen($array_legendas_intervalos[$i-1])-1);
				}
			}else{
				
				$array_legendas_intervalos[$i-1] = "";
			}
				
			$diferenca_qtd_letras = strlen($array_legendas_intervalos[$i-1]);
			imageline($im, $deslocamento_graph_right+$espaco_intervalo*$i, $deslocamento_graph_up, $deslocamento_graph_right+$espaco_intervalo*$i, $height - $deslocamento_graph_down, $back_line);
			imagestring($im,$font_size, $posicao_anterior + ($espaco_intervalo - ($diferenca_qtd_letras * $font_deslocamento))/2 , $height-$deslocamento_graph_down+15,$array_legendas_intervalos[$i-1],$inner_text);
			$posicao_anterior = $deslocamento_graph_right+$espaco_intervalo*$i;
		}
		// Colocar a ultima legenda de intervalo
		if($espaco_intervalo-$espaco_entre_palavras > 0)
		{
			while (strlen($array_legendas_intervalos[$i-1]) * $font_deslocamento > ($espaco_intervalo-$espaco_entre_palavras)) {
				$array_legendas_intervalos[$i-1] = substr($array_legendas_intervalos[$i-1],0,strlen($array_legendas_intervalos[$i-1])-1);
			}
		}else 
		{
			$array_legendas_intervalos[$i-1]="";
		}
		$diferenca_qtd_letras = strlen($array_legendas_intervalos[$i-1]);
		imagestring($im,$font_size, $posicao_anterior + ($espaco_intervalo - ($diferenca_qtd_letras * $font_deslocamento))/2 , $height-$deslocamento_graph_down+15,$array_legendas_intervalos[$i-1],$inner_text);

	}
	reset($coordenadas);
	if(count($coordenadas) > 0)
	{
		$max_value = current($coordenadas);
		for ($i = 1; $i<=count($coordenadas);$i++)
		{
			$key = key($coordenadas);	
	
			$max_value = array_merge($max_value,$coordenadas[$key]);
			$prox = next($coordenadas);
			
		}

		$max_value = max($max_value);
	}
	
	// Desenha Linhas Horizontais
	if ($qtd_linhas_horizontais)
	{
		$espaco_intervalo_hor = $height_graph/($qtd_linhas_horizontais+1);
		$posicao_anterior = $height - $deslocamento_graph_down;
		$divisao_valores = $max_value/($qtd_linhas_horizontais+1);
		$deslocamento_vertical = 5;

		$valor_linha_horizontal = 0;
		imagestring($im,$font_size,$deslocamento_graph_right-5-strlen($valor_linha_horizontal)*$font_deslocamento,$posicao_anterior-$deslocamento_vertical,$valor_linha_horizontal,$inner_text);

		for ($i=0;$i<$qtd_linhas_horizontais;$i++)
		{
			$valor_linha_horizontal +=$divisao_valores*1.05;
			$valor_linha_horizontal_ant = $valor_linha_horizontal;
			$valor_linha_horizontal_formatado = number_format($valor_linha_horizontal,1,".","");
			
			imageline($im, $deslocamento_graph_right, $posicao_anterior -$espaco_intervalo_hor , $width - $deslocamento_graph_left, $posicao_anterior -$espaco_intervalo_hor, $back_line);
			imagestring($im,$font_size,$deslocamento_graph_right-5-strlen($valor_linha_horizontal_formatado)*$font_deslocamento,$posicao_anterior -$espaco_intervalo_hor-$deslocamento_vertical,$valor_linha_horizontal_formatado,$inner_text);
			$posicao_anterior = $posicao_anterior -$espaco_intervalo_hor;
		}
		$valor_linha_horizontal +=$divisao_valores;
		$valor_linha_horizontal_formatado = number_format($valor_linha_horizontal,1,".","");

		imagestring($im,$font_size,$deslocamento_graph_right-5-strlen($valor_linha_horizontal_formatado)*$font_deslocamento,$posicao_anterior-$espaco_intervalo_hor-$deslocamento_vertical,$valor_linha_horizontal_formatado,$inner_text);

	}
	
	// Desenha Linha do Gráfico
	if(count($coordenadas) > 0)
	{
		$max_value = $max_value * 1.05;
		$cor_linhas = 0;
		foreach ($coordenadas as $i => $array) 
		 {	
			$posicao_anterior = $deslocamento_graph_right;
			if(count($array) > 1)
			{
				$cont = 1;
				foreach ($array as $j=>$v) 
				 {			
				 	$j= key($array);				
					next($array);					
					$jm= key($array);	
					$x_ini = $posicao_anterior + ($espaco_intervalo)/2;
					$y_ini =  ($height- $deslocamento_graph_down) - (($height_graph * $coordenadas[$i][$j])/$max_value);
					$posicao_anterior = $deslocamento_graph_right+$espaco_intervalo*($cont);
					if($jm) 
					{ 
						$x_fim = $posicao_anterior + ($espaco_intervalo)/2;
						$y_fim =  ($height- $deslocamento_graph_down) - (($height_graph * $coordenadas[$i][$jm])/$max_value);
					}
					drawdot(array($x_ini,$y_ini,$x_fim,$y_fim),$cor_linhas,$coordenadas[$i][$j], $im, $linecolor);
					$cont++;
				}
				//drawdot(array($x_fim,$y_fim,$x_fim,$y_fim),$cor_linhas,$coordenadas[$i][$j], $im, $linecolor);
			}else 
			{
				$x_ini = $posicao_anterior + ($espaco_intervalo)/2;
				$y_ini =  ($height- $deslocamento_graph_down) - (($height_graph * $coordenadas[$i][0])/$max_value);
				$posicao_anterior = $deslocamento_graph_right+$espaco_intervalo*($j+1);
				drawdot(array($x_ini,$y_ini,$x_ini,$y_ini),$cor_linhas,$coordenadas[$i][0], $im, $linecolor);
			}
			$cor_linhas++;
		}
	}	
	
	imagegif($im,"tmp/$image_name");


	
	//imagegif($im,"/tmp/tmp/teste_imagem");
	
	
}

function legenda($array_legenda, $font_size = 3, $nome_imagem)
{
	// Define Imagem
	
	$fonts = array(
		"1" => 4.77,
		"2" => 6,
		"3" => 6.75,
		"4" => 7.83,
		"5" => 8.6
		
		);
	
	if($font_size > 5)
	{
		$font_size = 5;
	}
	
	$font_deslocamento = $fonts[$font_size];
	
	$max_legenda = strlen("Legenda");
	foreach ($array_legenda as $legenda)
	{
		if(strlen($legenda) > $max_legenda)
		{
			$max_legenda = strlen($legenda);
		}
	}
	
	$width = 30 + $max_legenda * $font_deslocamento;
	$height = 40 + count($array_legenda)*15;
	$posx_ini = 5;
	$posy_ini = 5;
	
	
	$im = @imagecreate( $width, $height ) or die( "Cannot Initialize new GD image stream" );
	
	$background_color_image = imagecolorallocate( $im, 230, 230, 230);
	$text_color = imagecolorallocate ( $im, 0, 0, 0);
	$back_line = imagecolorallocate ( $im, 200, 200, 200 );
	$inner_text = imagecolorallocate ( $im, 0, 0, 0 );
	$background_color_graph = imagecolorallocate($im,245,245,245);
	$linecolor = array( 
		imagecolorallocate( $im, 100, 100, 100), 
		imagecolorallocate( $im,  49,  13, 244), 
		imagecolorallocate( $im, 220,   0,   0), 
		imagecolorallocate( $im, 152, 001, 154),
		imagecolorallocate( $im, 255, 100,   1), 
		imagecolorallocate( $im, 200, 151,  10), 
		imagecolorallocate( $im,  50, 180,  60), 
		imagecolorallocate( $im,  60, 160, 195), 
		imagecolorallocate( $im, 123, 096,  15), 
		imagecolorallocate( $im, 144, 143, 255), 
		imagecolorallocate( $im, 239, 100, 212),
		imagecolorallocate( $im,  39,  95,  35)
	);
	
	imagepolygon($im,array(2,2,2,$height-2,$width-2,$height-2,$width-2,2),4,$inner_text);
	imagepolygon($im,array(4,4,4,$height-4,$width-4,$height-4,$width-4,4),4,$inner_text);
	imagestring($im,$font_size,($width - strlen("Legenda")*$font_deslocamento)/2,$posy_ini,"Legenda",$inner_text);
	$posy_ini += 30;
	$i = 0;
	foreach ($array_legenda as $legenda) {
		for($j = 0; $j < 3;$j++)
		{
			imageline($im,$posx_ini+5,$posy_ini+$j,$posx_ini+10,$posy_ini+$j,$linecolor[$i]);
		}
		imagestring($im,$font_size, $posx_ini+15,$posy_ini-6,$legenda,$inner_text);

		$posy_ini += 15;
		$i++;
	}
	
	imagegif($im,"tmp/$nome_imagem");

}

//grafico_linhas("Gráfico de Testes",array("JAN","FEV","MAR","ABR","MAI","JUN","JUL","AGO","SET"),10,5,800,600,array(20,2,45,4,5,15,25,27,48), array(5,6,7,12,99,47,18,36,65));

//legenda(array("OI","as","Carroçaassss","Bacalhao","Arroz","Soja","Feijao","Macarrão","Batata"));
?>