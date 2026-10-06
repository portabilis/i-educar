<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('recusa a rematrícula automática quando o ano de destino não está aberto na escola', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['ano' => now()->year - 1]);

    $payload = LegacyFields::from($this->get('/intranet/educar_avancar_mod_cad.php')->assertOk()->getContent(), [
        'ano' => $schoolClass->ano,
        'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
        'ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_cod_turma' => $schoolClass->getKey(),
        'ano_letivo' => now()->year,
        'data_matricula' => now()->format('d/m/Y'),
    ]);

    $this->post('/intranet/educar_avancar_mod_cad.php', $payload)
        ->assertOk()
        ->assertSee('letivo aberto para ' . now()->year);
});
