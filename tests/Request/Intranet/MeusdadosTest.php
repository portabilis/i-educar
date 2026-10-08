<?php

use Database\Factories\LegacyIndividualPictureFactory;
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

test('mostra a foto atual e deixa trocá-la ou excluí-la', function () {
    LegacyIndividualPictureFactory::new()->create(['idpes' => $this->user->getKey(), 'caminho' => 'https://exemplo.test/foto.png']);

    $this->get('/intranet/meusdados.php')
        ->assertOk()
        ->assertSee('Foto atual')
        ->assertSee('https://exemplo.test/foto.png')
        ->assertSee('Excluir a foto');
});

test('recusa a matrícula que já é de outro usuário e a senha fraca', function () {
    $other = LegacyUserFactory::new()->create();
    $form = fn (array $overrides) => LegacyFields::from($this->get('/intranet/meusdados.php')->assertOk()->getContent(), $overrides);

    $this->post('/intranet/meusdados.php', $form(['matricula' => $other->employee->matricula]))
        ->assertOk()
        ->assertSee('A matrícula informada já pertence a outro usuário.');

    $this->post('/intranet/meusdados.php', $form(['senha' => 'fraca', 'senha_confirma' => 'fraca']))
        ->assertOk()
        ->assertSee('A senha deve conter pelo menos 8 caracteres');
});
