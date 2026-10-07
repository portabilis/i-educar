<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
});

test('abre a consulta de movimento mensal e envia os filtros', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_consulta_movimento_mensal.php')->assertOk()->getContent(), [
        'ano' => $this->schoolClass->ano,
        'ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao,
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'data_inicial' => now()->startOfYear()->format('d/m/Y'),
        'data_final' => now()->format('d/m/Y'),
    ]);

    $this->post('/intranet/educar_consulta_movimento_mensal.php', $payload)->assertRedirect();
});
