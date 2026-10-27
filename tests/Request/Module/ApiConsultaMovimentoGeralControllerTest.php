<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create();
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
    $this->filters = [
        'ano' => $this->schoolClass->ano,
        'instituicao' => $this->schoolClass->ref_cod_instituicao,
        'escola' => $this->schoolClass->ref_ref_cod_escola,
        'curso' => (string) $this->schoolClass->ref_cod_curso,
        'serie' => $this->schoolClass->ref_ref_cod_serie,
        'turma' => $this->schoolClass->getKey(),
        'data_inicial' => now()->startOfYear()->format('d/m/Y'),
        'data_final' => now()->format('d/m/Y'),
    ];
});

test('consulta os alunos do movimento geral por tipo', function (string $type) {
    $this->get('/module/Api/ConsultaMovimentoGeral?' . http_build_query($this->filters + ['oper' => 'get', 'resource' => 'alunos', 'tipo' => $type]))
        ->assertOk()
        ->assertJsonPath('resource', 'alunos');
})->with([
    'obito',
    'recla',
    'rem',
    'transf',
    'aband',
    'admitidos',
    'ed_inf_int',
    'ed_inf_parc',
]);
