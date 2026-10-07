<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('edita os dados da instituição', function () {
    $url = '/intranet/educar_instituicao_cad.php?cod_instituicao=' . $this->user->ref_cod_instituicao;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'nm_responsavel' => 'Responsável Alfa',
        // A tela monta o SQL sem escapar os textos; um apóstrofo vindo do faker quebraria a gravação.
        'nm_instituicao' => 'Instituição Alfa',
        'cidade' => 'Cidade Alfa',
        'bairro' => 'Bairro Alfa',
        'logradouro' => 'Rua Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    $this->assertDatabaseHas('pmieducar.instituicao', [
        'cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_responsavel' => 'Responsável Alfa',
    ]);
});
