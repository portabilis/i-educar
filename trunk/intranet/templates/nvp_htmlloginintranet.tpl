<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Intranet</title>
    <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />

		<script language="JavaScript" type="text/javascript">
			function loginpage_onload()
			{
				loginObj = document.getElementById( "login" );
				if( loginObj.value == "" )
				{
					loginObj.focus();
				}
			}
		</script>

    <link rel=stylesheet type='text/css' href='styles/reset.css' />
    <link rel=stylesheet type='text/css' href='styles/portabilis.css' />
    <style rel=stylesheet type='text/css'>
#flash-container, #menu, #corpo, #cabecalho #ccorpo, #rodape {
    width: 800px;
    margin-left: auto;
    margin-right: auto;    
}
    </style>
	</head>
	<body onload="loginpage_onload();">

		<div id="corpo">

		  <!--img src="imagens/nvp_tit_intranet.jpg" border="0" alt="Bem vindo" title="Bem vindo" /-->
      <h2>Entrar</h2>
      <p class="explication">Entre com sua conta</p>
      
		  <form action="" method="post">
      <table>
        <tr>
				  <td><label for="login">Matr&iacute;cula:</label></td>
			    <td><input type="text" name="login" id="login" /></td>
			  </tr>
        <tr>
				  <td><label for="senha">Senha:</label></td>
				  <td><input type="password" name="senha" id="senha" /></td>
        </tr>
        <tr>
          <td></td>
          <td><!-- #&ERROLOGIN&# --><input type="submit" src="imagens/nvp_bot_entra_webmail.jpg" value="Entrar" /></td>
        </tr>
      </table>
		  </form>					

		  <div id="rodape" class="texto-normal">
			  Portabilis Tecnologia <a href="http://www.portabilis.com.br/site/suporte" target="_blank">Suporte</a> | <a href="http://www.portabilis.com.br/site/fale-conosco" class="contact" target="_blank">Contate-nos</a>
		  </div>
    </div>	
	</body>
</html>
