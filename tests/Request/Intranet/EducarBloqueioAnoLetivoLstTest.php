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

test('lista os bloqueios de ano letivo', function () {
    $this->get('/intranet/educar_bloqueio_ano_letivo_lst.php?busca=S&ref_cod_instituicao=' . $this->user->ref_cod_instituicao . '&ano=1999')
        ->assertOk()
        ->assertSee('1999');
});
