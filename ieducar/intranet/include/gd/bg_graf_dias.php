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

	$width = 60;
	$height = 205;
	$maxval = $_GET["maxval"];
	$linhas_horizon = 10;
	$graph_no = rand( 0, 100 );
		
	header ( "Content-type: image/png" );

	$im = @imagecreate( $width, $height ) or die( "Cannot Initialize new GD image stream" );
	$background_color = imagecolorallocate( $im, 230, 230, 230);
	$text_color = imagecolorallocate ( $im, 0, 0, 0);
	$back_line = imagecolorallocate ( $im, 220, 220, 220 );
	$inner_text = imagecolorallocate ( $im, 150, 150, 150 );
	
	// cria as linhas horizontais
	for( $i = $height - 1, $j = 0; $i > 0; $i-=$linhas_horizon, $j++ ) {
		imageline ( $im, 0, $i, $width, $i, $back_line );
		$valorAtual = ( $j * $maxval ) / 20;
		imagestring( $im, 0, 3, $i, number_format( $valorAtual, 2, ",", "." ), $inner_text );
	}
	imagepng($im);
	imagedestroy($im);
?>