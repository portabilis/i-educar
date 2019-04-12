@extends('layout.public')

@section('content')
    <h2>Acesse sua conta</h2>
    @if(config('legacy.config.url_cadastro_usuario'))
        <div>Não possui uma conta? <a target="_blank" href="{{ config('legacy.config.url_cadastro_usuario') }}">Crie sua conta agora</a>.</div>
    @endif

    <form action="{{ route('login') }}" method="post">

        <label for="login">Matrícula:</label>
        <input type="text" name="login" id="login">

        <label for="password">Senha:</label>
        <input type="password" name="password" id="password">

        <button type="submit" class="submit">Entrar</button>

        <div class="remember">
            <a href="{{ route('password.request') }}">Esqueceu sua senha?</a>
        </div>

    </form>
@endsection
