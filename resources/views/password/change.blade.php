@extends('layout.public')

@section('content')
    <h2>Alterar senha</h2>
    <p>Para sua segurança, sua senha deverá ser alterada no primeiro acesso</p>
    <form action="{{ route('post-change-password') }}" method="post">

        {{csrf_field()}}

        <label for="login">Matrícula:</label>
        <input type="text" name="login" id="login">

        <label for="password">Senha:</label>
        <input type="password" name="password" id="password">

        <label for="password-confirm">Confirme:</label>
        <input type="password" name="password_confirmation" id="password-confirm">

        <button type="submit" class="submit">Entrar</button>
    </form>
@endsection
