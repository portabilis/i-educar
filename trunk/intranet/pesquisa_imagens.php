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
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");
require_once ("include/funcoes.inc.php");
require_once( "include/imagem/clsPortalImagemTipo.inc.php" );
require_once( "include/imagem/clsPortalImagem.inc.php" );

$imagens = "<table cellpadding='4' celspacing='4' border='0' align='center'>";
		$imagens .= "<tr><td colspan='27' class='linha'>Ícones</td></tr>";
		$ObjImagem = new clsPortalImagem();
		$ObjImagem->setOrderby("cod_imagem");
		$detalheImagens = $ObjImagem->lista(false, 1);
		$cont = 0;
		foreach ($detalheImagens as $imagem)
		{
			if($cont == 0)
				$imagens .= "<tr>";
			$imagens .= "<td class='celula' onclick='enviar_img(\"{$imagem['cod_imagem']}\")' 
			
					onMouseOver=\"cor(this, '#CCCCCC')\" 
					onMouseOut=\"cor(this, '#FFFFFF')\">
					
					<img align='center' src='imagens/banco_imagens/{$imagem['caminho']}' alt='{$imagem['nm_imagem']}' title='{$imagem['nm_imagem']}'></td>";
					
			if($cont == 26) 
				$imagens .= "</tr>";				
			$cont = ($cont==26) ?  0 : $cont+1; 	
		}

$imagens .= "</table>";
?>
<html>
	<head>
		<title>Banco de Imagens</title>
				
		<style>
		.linha{
			border: 3px outset #AAAAAA;			
			background-color: #CCCCCC;
			font-size: 16px;
			color: 444444;
		}
		.celula{
			border: 1px solid #cccccc;
		}
		</style>
		<script>
		function enviar_img( img )
		{
			
			if(img)
			{		
				window.opener.document.getElementById('img_banco_').value = img;
				window.opener.document.getElementById('img_banco').value = img;				
			}
			window.close();
		}
		function cor(obj,novacor){
			obj.style.backgroundColor = novacor;
		}
		</script>
	</head>
	<body>
		<?=$imagens?>
	</body>
</html>