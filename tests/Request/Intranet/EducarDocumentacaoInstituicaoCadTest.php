<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre o envio de documentação padrão levando a instituição e o usuário', function () {
    $response = $this->get('/intranet/educar_documentacao_instituicao_cad.php?cod_instituicao=' . $this->user->ref_cod_instituicao)
        ->assertOk()
        ->assertSee('Documentação padrão')
        ->assertSee('DocumentacaoPadrao.js', false);

    // O envio em si é feito pelo JavaScript da tela, que lê estes campos
    // ocultos para chamar a API de documentação.
    $fields = LegacyFields::from($response->getContent());

    expect($fields['cod_instituicao'])->toBe((string) $this->user->ref_cod_instituicao)
        ->and($fields['pessoa_logada'])->toBe((string) $this->user->getKey())
        ->and($fields['ref_cod_escola'])->toBe('')
        ->and($fields)->toHaveKey('titulo_documento');
});
