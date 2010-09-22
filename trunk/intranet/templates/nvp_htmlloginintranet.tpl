<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Intranet</title>

		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
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
		<style type="text/css">
			BODY,TABLE,TD {
				color: #000000;
				background-color: #FFFFFF;
				font-family: verdana, arial, heveltica, sans;
				font-size: 11px;
				background-repeat: no-repeat;
				margin: 0 0 0 0;
			}
			A:link, A:visited, A:active, A:hover {
				color: #0033CC;
				font-family: verdana, arial, heveltica, sans;
				font-size: 11px;
			}
			LABEL{
				float: left;
				width: 80px;
				font-family: 'Trebuchet Ms';
				font-size: 14px;
				text-align: right;
				font-weight: bold;
			}
			INPUT{
				margin-bottom:5px;
				width: 140px;
			}
			.botao{
				margin-left: 159px;
				width:66px;
			}
                        .erro{
                            color: red;
                            font-family: 'Trebuchet Ms';
                            font-size: 14px;
							padding-left: 80px;
                        }
		</style>
	</head>
	<body onload="loginpage_onload();">
		<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
			<tr>
				<td valign="top" align="left" width="211">&nbsp;</td>
				<td style="padding-left:10px;">					
					<br /><br /><br />
					<img src="imagens/nvp_tit_intranet.jpg" border="0" alt="Bem vindo" title="Bem vindo" /><br /><br />
					<!-- #&ERROLOGIN&# -->
					<form action="" method="post">
						<label for="login">Matr&iacute;cula:</label>
						<input type="text" name="login" id="login" value="" size="15" /><br />
						
						<label for="senha">Senha:</label>
						<input type="password" name="senha" id="senha" size="15" /><br />

						<input type="image" class="botao" src="imagens/nvp_bot_entra_webmail.jpg" value="Entrar" />
					</form>
					<br /><br /><br /><br /><br /><br />
					
				</td>
			</tr>
		</table>
	</body>
</html>
