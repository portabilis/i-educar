<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Intranet</title>
    <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />

    <link rel=stylesheet type='text/css' href='styles/reset.css?rand=1' />
    <link rel=stylesheet type='text/css' href='styles/portabilis.css?rand=1' />
    <link rel=stylesheet type='text/css' href='styles/min-portabilis.css?rand=1' />

  <script type='text/javascript' src='scripts/jquery/jquery.js'></script>

    <style rel=stylesheet type='text/css'>
      #flash-container, #menu, #corpo, #cabecalho #ccorpo, #rodape {
        width: 800px;
        margin-left: auto;
        margin-right: auto;
      }
    </style>

  <script type="text/javascript">
    var $j = jQuery.noConflict();

    function loginpage_onload() {
      $j('#login').focus();

      var domainName = window.location.hostname;

      if (domainName.indexOf('treinamento') < 0)
        $j('.only-for-clients').fadeIn('slow');
    }

    // set up google analytics
    var domainName = "#&GOOGLE_ANALYTICS_DOMAIN_NAME&#";

    // track only production requests.
    if (domainName.indexOf('local.') < 0 && domainName.indexOf('test.') < 0) {
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '***REMOVED***']);
      _gaq.push(['_setDomainName', domainName]);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    }
  </script>

	</head>
	<body onload="loginpage_onload();">

		<div id="corpo">

  <div id="flash-container">
    <!--[if lt IE 7]>
    <p style="min-height: 32px;" class="flash update-browser"><strong>Seu navegador est&aacute desatualizado.</strong> Para melhor navega&ccedil;&atildeo  no sistema, por favor, atualize seu navegador.<a href="http://br.mozdev.org/download/" target="_blank"><img style="margin-top:4px;" src="http://www.mozilla.org/contribute/buttons/110x32bubble_r_pt.png" alt="Firefox" width="110" height="32" style="border-style:none;" title="Mozilla Firefox" /></a></p>
    <![endif]-->

    <!--p style="min-height: 0px;" class="flash box shadow"><strong>Novo!</strong> acompanhe as &uacute;ltimas novidades do i-Educar em <a href="http://ieducar.com.br/wiki" class="decorated texto-normal">ieducar.com.br/wiki</a></p-->

    <p style="min-height: 0px;" class="info box shadow only-for-clients hidden">

      <strong>Novidade!</strong> <span class="decorated">Agora pais e alunos podem imprimir o boletim escolar de casa via internet!</span>

      <br /><br />Al&eacute;m do boletim escolar os pais podem visualizar ocorr&ecirc;ncias disciplinares dos filhos, e em breve ser&aacute; poss&iacute;vel consultar o acervo das bibliotecas escolares, consultar lista de materiais escolares, pr&eacute;-reservar matriculas e enviar recados para os pais / alunos.

      <br /><br /><strong>Ficou interessado?</strong> Entre em contato para saber como habilitar este servi&ccedil;o na sua escola: (48) 3055-3001.
    </p>

    <!--p style="min-height: 0px;" class="flash error"><strong>Importante, aviso de manuten&ccedil;&atilde;o:</strong> No dia 28/02/2012 (ter&ccedil;a feira) a partir das 18hs os sistemas poder&atilde;o estar inst&aacute;veis ou indispon&iacute;veis, devido melhorias na infraestrutura.</p-->

    <!--p style="min-height: 0px;" class="flash error"><strong>Importante, aviso de manuten&ccedil;&atilde;o:</strong> durante os dias 28 e 29 de abril (s&aacute;bado e domingo) os sistemas poder&atilde;o estar inst&aacute;veis ou indispon&iacute;veis, devido melhorias na infraestrutura.</p-->

    <!--p style="min-height: 0px;" class="flash error"><strong>Aviso importante:</strong> no momento nosso suporte via telefone esta enfrentando dificuldades, devido um incidente na sede de nossa empresa, o suporte via e-mail funciona normalizado.</p-->

    <!-- #&ERROLOGIN&# -->

  </div>
      <div id="login-form" class="box shadow">
        <h2>Entrar</h2>
        <p class="explication"></p>

		    <form action="" method="post">
        <table>
          <tbody><tr>
			      <td>
              <label class="" for="login">Matr&iacute;cula:</label>
				    </td>
				    <td>
              <input type="text" name="login" id="login"></td>
			    </tr>
          <tr>
				    <td>
              <label class="" for="senha">Senha:</label>
				    </td>
				    <td>
              <input type="password" name="senha" id="senha">
              <a class="light block small" href="/module/Usuario/RedefinirSenha">Esqueceu sua senha?</a>
            </td>
          </tr>
          <tr>
            <td class="spacer"></td>
            <td><!-- #&RECAPTCHA&# --></td>
          </tr>
          <tr>
            <td></td>
				    <td>
              <input type="submit" class="submit" src="imagens/nvp_bot_entra_webmail.jpg" value="Entrar">
            </td>
          </tr>
        </tbody></table>
		    </form>
      </div>


      <div id="extra" class="texto-normal">
        <div id="notices">
          <p class="title">Not&iacute;cias</p>
          <ul class="unstyled">
            <li><a class="decorated" href="http://www.estadao.com.br/noticias/impresso,fabrica-em-ceu-faz-bicicletas-de-bambu-para-alunos-,901184,0.htm">F&aacute;brica em CEU faz bicicletas de bambu para alunos</a></li>
          </ul>
        </div>

        <div id="service-info">
          <p class="requiriments title">Requisitos</p>
          <p class="explication">Para melhor uso do sistema, recomendamos:</p>
          <ul class="requiriments unstyled">
            <li>- Navegador <a target="_blank" class="decorated" href="http://br.mozdev.org/download/">Mozilla Firefox</a></li>
            <li>- Leitor PDF (para relat&oacute;rios) <a target="_blank" class="decorated" href="http://get.adobe.com/br/reader/download/">Adobe Reader</a> ou <a target="_blank" class="decorated" href="http://www.foxitsoftware.com/downloads#reader">Foxit</a></li>
          </ul>
        </div>

		  <p id="rodape" class="texto-normal">
        Portabilis Tecnologia - suporte@portabilis.com.br -

        <a target="_blank" class="decorated" href="http://www.teamviewer.com/pt/download/index.aspx">suporte remoto</a> -

        <a href="http://ieducar.com.br/wiki" class="decorated">ajuda</a>
		  </p>
    </div>
  </body>
</html>
