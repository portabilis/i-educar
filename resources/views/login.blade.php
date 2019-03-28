<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <title>i-Educar</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" type="text/css" href="{{ url('intranet/styles/login.css') }}">

    <!-- Google Tag Manager -->
    <script>
        dataLayer = [{
            'slug': '{{ $slug }}',
            'user_id': 0
        }];
    </script>
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
            var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '{{ $tagmanager }}');</script>
    <!-- End Google Tag Manager -->

</head>

<body>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<div id="main">

    <div>
        <img alt="Logo" width="150px" height="150px" src="{{ $logo }}"/>
    </div>

    <h1>{{ $name }}</h1>

    {!! $error !!}

    <div id="login-form" class="box shadow">
        <h2>Acesse sua conta</h2>
        @if($register)
            <div>Não possui uma conta? <a target="_blank" href="{{ $register }}">Crie sua conta agora</a>.</div>
        @endif

        <form action="" method="post">

            <label for="login">Matrícula:</label>
            <input type="text" name="login" id="login">

            <label for="password">Senha:</label>
            <input type="password" name="senha" id="password">

            <button type="submit" class="submit">Entrar</button>

            <div class="remember">
                <a href="/module/Usuario/RedefinirSenha">Esqueceu sua senha?</a>
            </div>

        </form>

    </div>

</div>

<div id="footer">
    <p>
        {!! $footerLogin !!}
    </p>

    <div class="footer-social">

        {!! $footer !!}

        <div class="social-icons">
            {!! $social !!}
        </div>
    </div>
</div>

</body>
</html>
