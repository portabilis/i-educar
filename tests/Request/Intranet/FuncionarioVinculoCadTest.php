<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->key = DB::table('portal.funcionario_vinculo')->insertGetId([
        'nm_vinculo' => 'Vínculo Alfa',
        'abreviatura' => 'VA',
    ], 'cod_funcionario_vinculo');
});

test('cadastra vínculo de funcionário', function () {
    $payload = LegacyFields::from($this->get('/intranet/funcionario_vinculo_cad.php')->assertOk()->getContent(), [
        'nm_vinculo' => 'Registro Alfa',
        'abreviatura' => 'RA',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    // A tela redireciona por script, e não por cabeçalho.
    $this->post('/intranet/funcionario_vinculo_cad.php', $payload)
        ->assertOk()
        ->assertSee("document.location='funcionario_vinculo_lst.php'", false);

    $this->assertDatabaseHas('portal.funcionario_vinculo', ['nm_vinculo' => 'Registro Alfa']);
});

test('edita e exclui vínculo de funcionário', function () {
    $url = '/intranet/funcionario_vinculo_cad.php?cod_funcionario_vinculo=' . $this->key;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_vinculo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)
        ->assertOk()
        ->assertSee("document.location='funcionario_vinculo_lst.php'", false);

    $this->assertDatabaseHas('portal.funcionario_vinculo', ['nm_vinculo' => 'Registro renomeado']);

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)
        ->assertOk()
        ->assertSee("document.location='funcionario_vinculo_lst.php'", false);

    $this->assertDatabaseMissing('portal.funcionario_vinculo', ['cod_funcionario_vinculo' => $this->key]);
});
