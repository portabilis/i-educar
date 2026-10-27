<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('o erro é anunciado na hora pelo leitor de tela', function () {
    $this->withSession(['error' => 'Religião não encontrada.'])
        ->get('/intranet/educar_religiao_lst.php')
        ->assertOk()
        ->assertSee('<div class="flashMessages__text" role="alert">Religião não encontrada.</div>', false);
});

test('os demais avisos são anunciados sem interromper o leitor de tela', function (string $type) {
    $this->withSession([$type => 'Aviso de teste.'])
        ->get('/intranet/educar_religiao_lst.php')
        ->assertOk()
        ->assertSee('<div class="flashMessages__message -' . $type . '">', false)
        ->assertSee('<div class="flashMessages__text" role="status">Aviso de teste.</div>', false);
})->with(['success', 'notice', 'info']);

test('cada aviso de uma lista recebe o próprio papel', function () {
    $this->withSession([
        'success' => ['Primeiro aviso.', 'Segundo aviso.'],
        'errors' => (new ViewErrorBag)->put('default', new MessageBag(['nome' => 'O nome é obrigatório.'])),
    ])
        ->get('/intranet/educar_religiao_lst.php')
        ->assertOk()
        ->assertSeeInOrder([
            '<div class="flashMessages__text" role="status">Primeiro aviso.</div>',
            '<div class="flashMessages__text" role="status">Segundo aviso.</div>',
            '<div class="flashMessages__text" role="alert">O nome é obrigatório.</div>',
        ], false);
});

test('o botão de fechar tem nome e o horário fica fora da leitura', function () {
    $this->withSession(['error' => 'Falhou.'])
        ->get('/intranet/educar_religiao_lst.php')
        ->assertOk()
        ->assertSee('<button type="button" class="close-msg" aria-label="Fechar aviso">×</button><time aria-hidden="true">', false);
});

test('sem aviso na sessão não monta aviso nenhum', function () {
    $this->get('/intranet/educar_religiao_lst.php')
        ->assertOk()
        ->assertDontSee('flashMessages__text', false);
});

test('no login o erro é um alerta e a confirmação é um status', function () {
    auth()->logout();

    // Sem usuário logado, o login desvia para a tela de suspensão quando a
    // instituição não está ativa no i-Educar; o force pula essa checagem.
    $this->withSession(['errors' => (new ViewErrorBag)->put('default', new MessageBag(['login' => 'Usuário ou senha incorretos.']))])
        ->get('/login?force=1')
        ->assertOk()
        ->assertSee('<p class="error" role="alert">Usuário ou senha incorretos.</p>', false);

    $this->withSession(['status' => 'Enviamos o link para o seu e-mail.'])
        ->get('/login?force=1')
        ->assertOk()
        ->assertSee('<output class="success">Enviamos o link para o seu e-mail.</output>', false);
});
