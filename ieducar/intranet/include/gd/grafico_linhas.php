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

	$linhas = array();
	//$linhas[0] = array( 1500000, 1422566, 1200000, 1422566, 1110000, 900000, 890000, 737000 );
	$linhas[0] = array( 1500000, 1422566, 120000, 1422566, 1110000, 900000, 890000, 20 );
	$legenda = array( "pontuaçao", "asteroides", "naves", "xxx", "yyyy" );

	$width =800;
	$height = 300;
	$maxval = 1422566;
	$linhas_horizon = 10;
	$bottom_height = 30;
	$coordenadas = $linhas;
	$graph_no = rand( 0, 100 );
	$mapHTML = "<map name=\"graph$graph_no\">";
	header ("Content-type: image/png");

	$im = @imagecreate( $width, $height ) or die( "Cannot Initialize new GD image stream" );
	$background_color = imagecolorallocate( $im, 230, 230, 230);
	$text_color = imagecolorallocate ( $im, 0, 0, 0);
	$back_line = imagecolorallocate ( $im, 200, 200, 200 );
	$inner_text = imagecolorallocate ( $im, 80, 100, 120 );
	
	$linecolor = array( 
		imagecolorallocate( $im, 14, 165, 3), 
		imagecolorallocate( $im, 49, 113, 244), 
		imagecolorallocate( $im, 217, 0, 0), 
		imagecolorallocate( $im, 230, 222, 5), 
		imagecolorallocate( $im, 250, 153, 0), 
		imagecolorallocate( $im, 11, 94, 31), 
		imagecolorallocate( $im, 7, 241, 230), 
		imagecolorallocate( $im, 0, 0, 0), 
		imagecolorallocate( $im, 123, 96, 15), 
		imagecolorallocate( $im, 192, 30, 194) 
	);
	$x_steps = floor( $width / ( count( $coordenadas[0] ) - 1 ) );
	
	// cria as linhas verticais
	for( $i = 0; $i < ( count( $coordenadas[0] ) - 1 ); $i++ ) {
		imageline ( $im, ( $x_steps * $i ), 0, ( $x_steps * $i ), $height - $bottom_height, $back_line );
	}
	// cria as linhas horizontais
	for( $i = $height - $bottom_height; $i > 0; $i-=$linhas_horizon ) {
		imageline ( $im, 0, $i, $width, $i, $back_line );
	}
	
	function drawdot( $coords, $tipo, $valor ) {
		global $im, $mapHTML, $inner_text, $linecolor;
		imageline ( $im, $coords[0], $coords[1], $coords[2], $coords[3], $linecolor[$tipo] );
		imageellipse ( $im, $coords[0], $coords[1], 3, 3, $linecolor );
		imagestring( $im, 1, $coords[0] + 2, $coords[1] + 3, $valor, $inner_text );
	}
	
	$legenda_coluna = 0;
	for( $i = 0; $i < count( $coordenadas ); $i++ ) {
		if( $i && ! ( $i % 3 ) ) $legenda_coluna++;
		$proporcao = ( $height - $bottom_height - min( $coordenadas[0] ) ) * 0.98 / max( $coordenadas[$i] );
		$legenda_height = $height - $bottom_height + $i % 3 * 10;
		imageline ( $im, $legenda_coluna * 80 + 5, $legenda_height + 5, $legenda_coluna * 80 + 15, $legenda_height + 5, $linecolor[$i] );
		imagestring( $im, 1, $legenda_coluna * 80 + 18, $legenda_height, $legenda[$i], $text_color );
		for( $j = 0; $j < count( $coordenadas[$i] ) - 1; $j++ ) {
			$x_ini = $x_steps * $j;
			$x_end = $x_steps * ( $j + 1 );
			$y_ini = $height - $bottom_height - ( $coordenadas[$i][$j] - min( $coordenadas[$i] ) ) * $proporcao;
			$y_end = $height - $bottom_height - ( $coordenadas[$i][( $j + 1 )] - min( $coordenadas[$i] ) ) * $proporcao ;
			drawdot( array( $x_ini, $y_ini, $x_end, $y_end ), $i, $coordenadas[$i][$j] );
		}
		drawdot( array( $x_end, $y_end, $width - 1, $y_end ), $i, $coordenadas[$i][$j] );
	}
	imagepng($im);
	imagedestroy($im);

?>