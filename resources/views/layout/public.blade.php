<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}" />
    <title>@if(isset($title)) {!! html_entity_decode($title) !!} - @endif {{ html_entity_decode(config('legacy.app.entity.name')) }} - i-Educar</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" type="text/css" href="{{ url('intranet/styles/login.css') }}">

    <!-- Google Tag Manager -->
    <script>
        dataLayer = [{
            'slug': '{{$config['app']['database']['dbname']}}',
            'user_id': 0
        }];

        (function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
            var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '{{ config('legacy.gtm') }}');
    </script>
    <!-- End Google Tag Manager -->

    @if($errors->count() && str_contains($errors->first(), 'errou a senha muitas vezes' ))
    <script>
        window.onload = function() {
            document.getElementById("form-login-submit").disabled = true;
            setTimeout(function () {
                document.getElementById("form-login-submit").disabled = false;
            }, 60000);
        }
    </script>
    @endif
</head>

<body>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ config('legacy.gtm') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<div id="main">

    <div>
        <img alt="Logo" width="150px" src="{{ config('legacy.config.ieducar_image') ?? url('intranet/imagens/brasao-republica.png') }}"/>
    </div>

    <h1>{{ config('legacy.config.ieducar_entity_name') }}</h1>

    @if (session('status'))
        <p class="success">{{ session('status') }}</p>
    @endif

    @if($errors->count())
        <p class="error">{{ $errors->first() }}</p>
    @endif

    <div id="login-form" class="box shadow">
        @yield('content')
    </div>

</div>

<div id="footer">
    <p>
        {!! config('legacy.config.ieducar_login_footer') !!}
    </p>

    <div class="footer-social">

        {!! config('legacy.config.ieducar_external_footer') !!}

        @if(config('legacy.config.facebook_url') || config('legacy.config.linkedin_url') || config('legacy.config.twitter_url'))
            <div class="social-icons">
                <p> Siga-nos nas redes sociais&nbsp;&nbsp;</p>
                @if(config('legacy.config.facebook_url'))
                    <a target="_blank" href="{{ config('legacy.config.facebook_url')}}"><img src="{{ url('intranet/imagens/icon-social-facebook.png') }}"></a>
                @endif
                @if(config('legacy.config.linkedin_url'))
                    <a target="_blank" href="{{ config('legacy.config.linkedin_url')}}"><img src="{{ url('intranet/imagens/icon-social-linkedin.png') }}"></a>
                @endif
                @if(config('legacy.config.twitter_url'))
                    <a target="_blank" href="{{ config('legacy.config.twitter_url')}}"><img src="{{ url('intranet/imagens/icon-social-twitter.png') }}"></a>
                @endif
            </div>
        @endif
    </div>
</div>

</body>
</html>
