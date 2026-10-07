<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

// A gravação com sucesso termina em exit(), que encerraria o processo de teste;
// só as recusas anteriores a ele são alcançáveis por requisição.
test('recusa a troca de senha quando a confirmação não confere', function () {
    $payload = LegacyFields::from($this->get('/intranet/meusdados.php')->assertOk()->getContent(), [
        'senha' => 'Senha@Nova123',
        'senha_confirma' => 'Outra@Senha123',
    ]);

    $this->post('/intranet/meusdados.php', $payload)
        ->assertOk()
        ->assertSee('confirmação de senha');
});
