<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <link href="https://fonts.googleapis.com/css?family=Muli:700&display=swap" rel="stylesheet">
  @if(config('legacy.app.gtm.id'))
  <!-- Google Tag Manager -->
  <script>
  (function (w, d, s, l, i) {
    w[l] = w[l] || [];
    w[l].push({
      'gtm.start':
        new Date().getTime(), event: 'gtm.js',
    });
    var f = d.getElementsByTagName(s)[0],
      j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
    j.async = true;
    j.src =
      'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
    f.parentNode.insertBefore(j, f);
  })(window, document, 'script', 'dataLayer', '{{ config('legacy.app.gtm.id') }}');
  </script>
  <!-- End Google Tag Manager -->
  @endif
  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body, html {
    font-family: 'Muli', sans-serif;
    font-weight: 700;
    font-size: 24px;
    color: #506073;
  }

  .container {
    width: 100vw;
    min-height: 100vh;
    padding: 0 48px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url({{ Asset::get('/svg/bg.svg') }});
    background-size: 100% 100%;
    background-repeat: no-repeat;
  }

  .content {
    text-align: center;
    padding: 48px 0;
  }

  .image {
    max-width: 530px;
    margin: 0 auto;
  }

  .image svg {
    width: 100%;
  }

  .title {
    color: #003473;
    font-size: 40px;
    margin-bottom: 24px;
  }

  .text {
    margin-bottom: 24px;
  }

  .btn {
    display: inline-block;
    background: #003473;
    color: white;
    font-size: 17px;
    padding: 24px 72px;
    text-decoration: none;
    border-radius: 6px;
  }

  @media (max-width: 400px) {
    body, html {
      font-size: 16px;
    }

    .title {
      font-size: 24px;
    }

    .btn {
      font-size: 13px;
      padding: 16px 32px;
    }
  }
  </style>
</head>
<body>
  @if(config('legacy.app.gtm.id'))
  <!-- Google Tag Manager (noscript) -->
  <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ config('legacy.app.gtm.id') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
  </noscript>
  <!-- End Google Tag Manager (noscript) -->
  @endif
  @yield('content')
</body>
</html>
