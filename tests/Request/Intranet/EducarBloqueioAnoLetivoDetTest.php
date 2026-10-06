<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;

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

test('mostra o detalhe do bloqueio de ano letivo', function () {
    $this->get('/intranet/educar_bloqueio_ano_letivo_det.php?' . $this->keys)
        ->assertOk()
        ->assertSee('1999');
});

test('volta para a listagem quando o bloqueio não existe', function () {
    $this->get('/intranet/educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao=' . $this->user->ref_cod_instituicao . '&ref_ano=1800')
        ->assertRedirect();
});
