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

test('consulta os alunos do movimento mensal por tipo', function (string $type) {
    $this->get('/module/Api/ConsultaMovimentoMensal?' . http_build_query($this->filters + ['oper' => 'get', 'resource' => 'alunos', 'tipo' => $type]))
        ->assertOk()
        ->assertJsonPath('resource', 'alunos');
})->with([
    'mat_ini_m',
    'mat_ini_f',
    'mat_transf_m',
    'mat_transf_f',
    'mat_aband_m',
    'mat_aband_f',
    'mat_admit_m',
    'mat_admit_f',
    'mat_falecido_m',
    'mat_falecido_f',
]);
