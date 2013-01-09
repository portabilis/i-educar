<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Intranet</title>
    <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />

    <link rel=stylesheet type='text/css' href='styles/reset.css?rand=3' />
    <link rel=stylesheet type='text/css' href='styles/portabilis.css?rand=3' />
    <link rel=stylesheet type='text/css' href='styles/min-portabilis.css?rand=3' />
    <link rel=stylesheet type='text/css' href='styles/login.css?rand=4' />

  <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>

  <script type="text/javascript">

    var $j = jQuery.noConflict();

    function currentSO() {
      var so = undefined;

      if (navigator && navigator.platform) {
        var platform = navigator.platform.toLowerCase();

        if (platform.indexOf('win') > -1)
          so = 'windows';
        else if (platform.indexOf('linux') > -1)
          so = 'linux';
        else if (platform.indexOf('mac') > -1)
          so = 'macOS';
        else if (platform.indexOf('x11') > -1)
          so = 'unix';
        else
          so = platform;
      }

      return so;
    }

    function loginpage_onload() {
      var domainName = window.location.hostname;

      if (domainName.indexOf('treinamento') < 0 && domainName.indexOf('demonstracao') < 0)
        $j('.only-for-clients').show();

      $j('.fade-in').fadeIn('slow');

      $j('#login').focus();

      // used for support links
      if (currentSO() == 'windows')
        $j('.visible-for-windows-so').show();
      else
        $j('.visible-for-non-windows-so').show();
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
	<body onload="loginpage_onload();" class="hidden fade-in">
    <div id="flash-container">
      <!--[if lt IE 7]>
      <p style="min-height: 32px;" class="flash update-browser"><strong>Seu navegador est&aacute desatualizado.</strong> Para melhor navega&ccedil;&atildeo  no sistema, por favor, atualize seu navegador.<a href="http://br.mozdev.org/download/" target="_blank"><img style="margin-top:4px;" src="http://www.mozilla.org/contribute/buttons/110x32bubble_r_pt.png" alt="Firefox" width="110" height="32" style="border-style:none;" title="Mozilla Firefox" /></a></p>
      <![endif]-->

      <!--p style="min-height: 0px;" class="info box shadow only-for-clients hidden">
        <strong>Novidade!</strong> <span class="decorated">Agora pais e alunos podem imprimir o boletim escolar de casa via internet!</span>

        <br /><br />Al&eacute;m do boletim escolar os pais podem visualizar ocorr&ecirc;ncias disciplinares dos filhos, e em breve ser&aacute; poss&iacute;vel consultar o acervo das bibliotecas escolares, consultar lista de materiais escolares, pr&eacute;-reservar matriculas e enviar recados para os pais / alunos.

        <br /><br /><strong>Ficou interessado?</strong> Entre em contato para saber como habilitar este servi&ccedil;o na sua escola: (48) 3055-3001.
      </p-->

      <!--p style="min-height: 0px;" class="flash error"><strong>Importante, aviso de manuten&ccedil;&atilde;o:</strong> No dia 28/02/2012 (ter&ccedil;a feira) a partir das 18hs os sistemas poder&atilde;o estar inst&aacute;veis ou indispon&iacute;veis, devido melhorias na infraestrutura.</p-->

      <!--p style="min-height: 0px;" class="flash error"><strong>Importante, aviso de manuten&ccedil;&atilde;o:</strong> durante os dias 28 e 29 de abril (s&aacute;bado e domingo) os sistemas poder&atilde;o estar inst&aacute;veis ou indispon&iacute;veis, devido melhorias na infraestrutura.</p-->

      <!--p style="min-height: 0px;" class="flash error"><strong>Aviso importante:</strong> no momento nosso suporte via telefone esta enfrentando dificuldades, devido um incidente na sede de nossa empresa, o suporte via e-mail funciona normalizado.</p-->

      <!-- #&ERROLOGIN&# -->
    </div>

    <div id="corpo">
      <div id="login-form" class="box shadow">
        <h2>Entrar</h2>
        <p class="explication"></p>

        <form action="" method="post">
        <table>
          <tbody><tr>
    		    <td>
              <label class="" for="login">Matr&iacute;cula:</label>
              <input type="text" name="login" id="login"></td>
    	    </tr>

          <tr>
    		    <td>
              <label class="" for="senha">Senha:</label>
              <input type="password" name="senha" id="senha">
            </td>
          </tr>
          <tr>
            <td><!-- #&RECAPTCHA&# --></td>
          </tr>
          <tr>
    		    <td>
              <input type="submit" class="submit" src="imagens/nvp_bot_entra_webmail.jpg" value="Entrar">
            </td>
          </tr>
          <tr>
            <td>
              <p class="forget-password"><a class="light small" href="/module/Usuario/RedefinirSenha">Esqueceu sua senha?</a></p>

              <p><a target="_blank" class="light small" href="http://educacao.portabilis.com.br/">Acesso professores, pais e alunos.</a> <a target="_blank" class="decorated light small" href="http://www.portabilis.com.br/produto/educacao-19#destaques">Saiba mais</a>
            </p>
            </td>
          </tr>
        </tbody></table>
        </form>

      </div> <!-- end login-form -->

      <div id="service-info">
        <p class="requiriments title">Requisitos</p>
        <p class="explication">Para melhor uso do sistema, recomendamos:</p>
        <ul class="requiriments unstyled">
          <li>- Navegador <a target="_blank" class="light decorated" href="http://br.mozdev.org/download/">Mozilla Firefox</a></li>
          <li>- Leitor PDF (para relat&oacute;rios) <a target="_blank" class="light decorated" href="http://get.adobe.com/br/reader/">Adobe Reader</a> ou <a target="_blank" class="light decorated" href="http://www.foxitsoftware.com/downloads#reader">Foxit</a></li>
        </ul>
      </div>

      <div class="clear"></div>

    </div> <!-- end corpo -->

    <div id="rodape" class="texto-normal">
		  <p>
        Portabilis Tecnologia - suporte@portabilis.com.br -

        <a target="_blank" class="decorated hidden visible-for-windows-so" href="http://www.teamviewer.com/download/TeamViewerQS_pt.exe">suporte remoto</a>

        <a target="_blank" class="decorated hidden visible-for-non-windows-so" href="http://www.teamviewer.com/pt/download/index.aspx">suporte remoto</a>

        -

        <a href="http://ieducar.com.br/wiki" class="decorated">ajuda</a>
		  </p>
    </div> <!-- end rodape -->

  </body>
</html>
