<?php

use App\Services\DiarioControllerService;
use Database\Factories\LegacyScoreExamFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->serviceWithSituations = function (array $situations, string $examScore): DiarioControllerService {
        $components = new stdClass;
        $components->componentesCurriculares = $situations;

        $boletim = $this->createPartialMock(Avaliacao_Service_Boletim::class, ['preverNotaRecuperacao', 'getSituacaoComponentesCurriculares']);
        $boletim->method('preverNotaRecuperacao')->willReturn($examScore);
        $boletim->method('getSituacaoComponentesCurriculares')->willReturn($components);

        $service = $this->createPartialMock(DiarioControllerService::class, ['getServiceBoletim']);
        $service->method('getServiceBoletim')->willReturn($boletim);

        return $service;
    };
});

test('updates exam score of a component in exam', function () {
    $scoreExam = LegacyScoreExamFactory::new()->create(['nota_exame' => 1]);
    $registration = $scoreExam->registration;
    $componentId = $scoreExam->ref_cod_componente_curricular;

    $service = ($this->serviceWithSituations)([
        $componentId => (object) ['situacao' => App_Model_MatriculaSituacao::EM_EXAME],
    ], '6.5');

    $service->atualizaNotaNecessariaExame(1, $registration->ref_cod_aluno, $componentId, $registration);

    $this->assertDatabaseHas('modules.nota_exame', [
        'ref_cod_matricula' => $registration->getKey(),
        'ref_cod_componente_curricular' => $componentId,
        'nota_exame' => 6.5,
    ]);
});

test('removes exam score of a component outside the registration', function () {
    $scoreExam = LegacyScoreExamFactory::new()->create();
    $registration = $scoreExam->registration;
    $componentId = $scoreExam->ref_cod_componente_curricular;

    // Componente dispensado não aparece na lista de situações da matrícula.
    $service = ($this->serviceWithSituations)([], '6.5');

    $service->atualizaNotaNecessariaExame(1, $registration->ref_cod_aluno, $componentId, $registration);

    $this->assertDatabaseMissing('modules.nota_exame', [
        'ref_cod_matricula' => $registration->getKey(),
        'ref_cod_componente_curricular' => $componentId,
    ]);
});
