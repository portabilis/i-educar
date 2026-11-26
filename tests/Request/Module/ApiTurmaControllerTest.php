<?php

use App\Models\LegacySchoolAcademicYear;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->schoolClass->getKey();
});

LegacyApiScenario::registerResourceTests('/module/Api/Turma', [
    'turma',
    'ordena-matriculas-data-base',
    'ordena-turma-alfabetica',
    'turmas-por-escola',
    'alunos-exame-turma',
    'series-da-turma',
]);

test('informa se o ano letivo da turma está encerrado', function (int $status, bool $finalized) {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ano' => $this->schoolClass->ano,
        'andamento' => $status,
        'ativo' => 1,
    ]);

    $this->get('/module/Api/Turma?' . http_build_query([
        'oper' => 'get',
        'resource' => 'turma',
    ] + $this->params))->assertOk()->assertJsonPath('ano_letivo_encerrado', $finalized);
})->with([
    'encerrado' => [LegacySchoolAcademicYear::FINALIZED, true],
    'em andamento' => [LegacySchoolAcademicYear::IN_PROGRESS, false],
]);
