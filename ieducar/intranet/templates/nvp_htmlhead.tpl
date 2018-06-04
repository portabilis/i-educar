<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="pt" class="no-js">
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  <!--meta http-equiv="Content-Type" content="text/html; charset=utf-8" /-->
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="-1" />
  <!-- #&REFRESH&# -->

  <title><!-- #&TITULO&# --></title>

  <script>
    dataLayer = [{
      'slug': '<!-- #&SLUG&# -->',
      'user_id': <!-- #&USER_ID&# -->,
      'user_name': '<!-- #&USERLOGADO&# -->',
      'user_email': '<!-- #&USEREMAIL&# -->'
    }];
  </script>

  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
              j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
              'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
              })(window,document,'script','dataLayer','<!-- #&GOOGLE_TAG_MANAGER_ID&# -->');</script>
  <!-- End Google Tag Manager -->

  <link rel=stylesheet type='text/css' href='/intranet/styles/main.css?5' />
  <link rel=stylesheet type='text/css' href='/intranet/styles/styles.css?5' />
  <link rel=stylesheet type='text/css' href='/intranet/styles/novo.css?5' />
  <link rel=stylesheet type='text/css' href='/intranet/styles/menu.css?5' />
  <link rel=stylesheet type='text/css' href='/intranet/styles/font-awesome.css?5' />
  <!--link rel=stylesheet type='text/css' href='styles/reset.css?5' />
  <link rel=stylesheet type='text/css' href='styles/portabilis.css?5' /-->
  <link rel=stylesheet type='text/css' href='/intranet/styles/min-portabilis.css?5?rand=4' />
  <link rel=stylesheet type='text/css' href='/intranet/styles/mytdt.css?5' />
  <link rel=stylesheet type='text/css' href='/intranet/styles/jquery.modal.css?5' />
  <script src="https://maps.google.com/maps/api/js?sensor=true" type="text/javascript" charset="utf-8"></script>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

  <!-- #&ESTILO&# -->

<script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script>

  <script type="text/javascript" src="/intranet/scripts/padrao.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/novo.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/dom.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/menu.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/ied/forms.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/ied/phpjs.js?5"></script>

  <script type="text/javascript" src="/intranet/scripts/jquery/jquery-1.8.3.min.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/jquery/jquery.modal.min.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/prototype/prototype-1.7.1.0.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/scriptaculous/effects.js?5"></script>
  <script type="text/javascript" src="/intranet/scripts/jquery.mask.min.js?5"></script>
  <script type="text/javascript">
    window.ambiente = '<!-- #&CORE_EXT_CONFIGURATION_ENV&# -->';

    var running = false;
    var altura  = null;

    function changeImage(div_id)
    {
      var id     = /[0-9]+/.exec(div_id.element.id);
      var imagem = $('seta_'+id);
      var src    = imagem.src.indexOf('arrow-up') ;

      imagem.src = (src!= -1) ?
        'imagens/arrow-down2.png' : 'imagens/arrow-up2.png' ;

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

<link rel=stylesheet type='text/css' href='/intranet/styles/custom.css?5' />
<body <!-- #&BODYSCRIPTS&# -->>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<!-- #&GOOGLE_TAG_MANAGER_ID&# -->"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <div id="DOM_expansivel" class="DOM_expansivel"></div>
