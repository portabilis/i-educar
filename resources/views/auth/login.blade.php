@extends('layout.public')

@section('content')
    <h2>Acesse sua conta</h2>
    @if(config('legacy.config.url_cadastro_usuario'))
        <div>Não possui uma conta? <a target="_blank" href="{{ config('legacy.config.url_cadastro_usuario') }}" rel="noopener">Crie sua conta agora</a>.</div>
    @endif

    <form action="{{ Asset::get('login') }}" method="post" id="form-login">

        <label for="login">Matrícula:</label>
        <input type="text" name="login" id="login" value="{{ old('login') }}">

        <label for="password">Senha:</label>
        <input type="password" name="password" id="password">
        <i class="fa fa-eye-slash" id="eye" onclick="showPassword()" onkeyup="showPassword()" aria-hidden="true"></i>

        <button id="form-login-submit" type="submit" class="submit">Entrar</button>

        <div class="remember">
            <a href="{{ route('password.request') }}">Esqueceu sua senha?</a>
        </div>

        @if(config('services.passport.enabled'))
        <div id="sso" style="display: block;">
            <div style="border-top: 1px solid #DDDDDD; margin-top: 1.5rem; padding: 1.5rem 0 0; position: relative">
                <span style="position: absolute; top: -.75rem; width: 50px; background: white; left: calc(50% - 25px); text-align: center; color: #999999">OU</span>
                <a href="{{ route('socialite.redirect') }}?intended={{ session()->get('url.intended') }}" style="padding: .75rem 1.25rem; background: #0052f5; text-align: center; color: white; display: block; border-radius: 3px">
                    {{ config('services.passport.label') }}
                </a>
            </div>
        </div>
        @endif

    </form>

    <script>
        function showPassword() {
            var input = document.getElementById("password");
            var eye = document.getElementById("eye");

            if (input.type === "password") {
                input.type = "text";
                eye.classList.remove("fa-eye-slash");
                eye.classList.add("fa-eye");
            } else {
                input.type = "password";
                eye.classList.remove("fa-eye");
                eye.classList.add("fa-eye-slash");
            }
        }
    </script>

    @if (config('legacy.app.recaptcha_v3.public_key') && config('legacy.app.recaptcha_v3.private_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{config('legacy.app.recaptcha_v3.public_key')}}"></script>
        <script src="{{ Asset::get("/intranet/scripts/jquery/jquery-1.8.3.min.js") }} "></script>

        <script>
            let grecaptchaKey = "{{config('legacy.app.recaptcha_v3.public_key')}}";
            let form = $('#form-login');

            grecaptcha.ready(function() {
                form.submit(function(e) {
                    e.preventDefault();
                    grecaptcha.execute(grecaptchaKey, {action: 'submit'})
                        .then((token) => {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'grecaptcha';
                            input.value = token;

                            form.append(input);

                            $(this).unbind('submit').submit();
                        });
                });
            });
        </script>
    @endif
@endsection
