<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre a consulta de movimento geral e envia os filtros', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_consulta_movimento_geral.php')->assertOk()->getContent(), [
        'ano' => now()->year,
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'data_inicial' => now()->startOfYear()->format('d/m/Y'),
        'data_final' => now()->format('d/m/Y'),
    ]);

    $this->post('/intranet/educar_consulta_movimento_geral.php', $payload)->assertRedirect();
});
