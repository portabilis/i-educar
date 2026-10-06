<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->other = LegacyUserFactory::new()->institutional()->create();
});

test('abre o formulário de um novo usuário', function () {
    $this->get('/intranet/educar_usuario_cad.php')->assertOk();
});

test('edita o usuário', function () {
    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $this->other->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'email' => 'usuario.alfa@example.com',
        // A matrícula precisa ser única, e a da factory é um dígito aleatório.
        'matricula' => 'u' . $this->other->getKey(),
    ]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
});
