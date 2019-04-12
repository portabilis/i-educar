@extends('layout.public')

@section('content')
    <h2>Alterar senha</h2>

    <form action="{{ route('password.update') }}" method="post">

        <input type="hidden" name="token" value="{{ $token }}">

        <label for="login">Matrícula:</label>
        <input type="text" name="login" id="login">

        <label for="password">Senha:</label>
        <input type="password" name="password" id="password">

        <label for="password-confirm">Confirme:</label>
        <input type="password" name="password_confirmation" id="password-confirm">

        <button type="submit" class="submit">Entrar</button>

        <div class="remember">
            <a href="{{ route('login') }}">Fazer login?</a>
        </div>

    </form>
@endsection
