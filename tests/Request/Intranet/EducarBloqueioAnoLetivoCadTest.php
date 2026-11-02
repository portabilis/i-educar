<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    DB::table('pmieducar.bloqueio_ano_letivo')->insert([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_ano' => 1999,
        'data_inicio' => '1999-01-01',
        'data_fim' => '1999-12-31',
    ]);
    $this->keys = 'ref_cod_instituicao=' . $this->user->ref_cod_instituicao . '&ref_ano=1999';
});

test('cadastra um bloqueio de ano letivo', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_bloqueio_ano_letivo_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ano' => 1998,
        'data_inicio' => '01/01/1998',
        'data_fim' => '31/12/1998',
    ]);

    $this->post('/intranet/educar_bloqueio_ano_letivo_cad.php', $payload)->assertRedirect();

    $this->assertDatabaseHas('pmieducar.bloqueio_ano_letivo', ['ref_ano' => 1998]);
});

test('edita e exclui o bloqueio de ano letivo', function () {
    $url = '/intranet/educar_bloqueio_ano_letivo_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['data_fim' => '30/11/1999']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();

    $this->assertDatabaseMissing('pmieducar.bloqueio_ano_letivo', ['ref_ano' => 1999]);
});
