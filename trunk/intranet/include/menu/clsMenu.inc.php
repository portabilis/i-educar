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
?>

<html>
<head>

	<link rel=stylesheet type='text/css' href='http://nat-bp-spo.cobra.com.br/styles/menu.css' >
	<script type='text/javascript' src='http://nat-bp-spo.cobra.com.br/scripts/dom.js'></script>
	<script type='text/javascript' src='http://nat-bp-spo.cobra.com.br/scripts/menu.js'></script>
</head>
<body>
<script>
/************************************************************
Coolmenus Beta 4.04 - Copyright Thomas Brattli - www.dhtmlcentral.com
Last updated: 03.22.02
*************************************************************/
/*Browsercheck object*/

menu[0] = new Array("Estoque",1,'','', '');
menu[1] = new Array("Higor",2,1,'','');
menu[2] = new Array("Cadastro",4,'','','');


</script>
<table>
<tr>
<td id="as">
assas
</td>

<td id="pega">

<script>
				var posx = DOM_ObjectPosition_getPageOffsetLeft(document.getElementById('pega'));
				var posy = DOM_ObjectPosition_getPageOffsetTop(document.getElementById('pega'));
				MontaMenu(menu, posx,posy);
</script>
<td>
</tr>
</body>

</html>

