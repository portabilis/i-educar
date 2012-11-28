<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="pt">
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
  <!--meta http-equiv="Content-Type" content="text/html; charset=utf-8" /-->
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="-1" />
  <!-- #&REFRESH&# -->

  <title><!-- #&TITULO&# --></title>

  <link rel=stylesheet type='text/css' href='styles/main.css' />
  <link rel=stylesheet type='text/css' href='styles/styles.css' />
  <link rel=stylesheet type='text/css' href='styles/novo.css' />
  <link rel=stylesheet type='text/css' href='styles/menu.css' />
  <!--link rel=stylesheet type='text/css' href='styles/reset.css' />
  <link rel=stylesheet type='text/css' href='styles/portabilis.css' /-->
  <link rel=stylesheet type='text/css' href='styles/min-portabilis.css' />

  <!-- #&ESTILO&# -->

  <script type="text/javascript" src="scripts/padrao.js?1"></script>
  <script type="text/javascript" src="scripts/novo.js?1"></script>
  <script type="text/javascript" src="scripts/dom.js?1"></script>
  <script type="text/javascript" src="scripts/menu.js?1"></script>
  <script type="text/javascript" src="scripts/ied/forms.js?1"></script>
  <script type="text/javascript" src="scripts/ied/phpjs.js?1"></script>

  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js?load=effects"></script>

  <script type="text/javascript">
    var running = false;
    var altura  = null;

    function changeImage(div_id)
    {
      var id     = /[0-9]+/.exec(div_id.element.id);
      var imagem = $('seta_'+id);
      var src    = imagem.src.indexOf('arrow-up') ;

      imagem.src = (src!= -1) ?
        'imagens/arrow-down2.gif' : 'imagens/arrow-up2.gif' ;

      imagem.title = (src!= -1) ?
        imagem.title.replace('Abrir', 'Fechar') :
        imagem.title.replace('Fechar', 'Abrir');

      if (src != -1) {
        setCookie('menu_' + id, 'I', 30);
      }
      else {
        setCookie('menu_' + id, 'V', 30);
      }

      running = false;
      $('tablenum1').style.height = $('tablenum1').offsetHeight - altura;
    }

    function teste(div_id)
    {
      altura = div_id.element.offsetHeight;
    }

    function toggleMenu(div_id)
    {
      if (running) {
        return;
      }

      var src = $('link1_'+div_id).title.indexOf('Abrir');

      $('link1_'+div_id).title = (src!= -1) ?
        $('link1_'+div_id).title.replace('Abrir', 'Fechar') :
        $('link1_'+div_id).title.replace('Fechar', 'Abrir');

      $('link2_'+div_id).title = (src!= -1) ?
        $('link2_'+div_id).title.replace('Abrir', 'Fechar') :
        $('link2_'+div_id).title.replace('Fechar', 'Abrir');

      running = true;

      new Effect.toggle($('div_' + div_id), 'slide', {afterFinish: changeImage, duration: 0.3, beforeStart: teste});
    }
  </script>

  <!-- #&SCRIPT&# -->

  <script type="text/javascript">
  <!-- #&SCRIPT_HEADER&# -->
  </script>

  <script type="text/javascript">
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
<body <!-- #&BODYSCRIPTS&# -->>
  <div id="DOM_expansivel" class="DOM_expansivel"></div>
