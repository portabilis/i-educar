<?php

namespace Tests\Unit\Services;

use App\Services\DiarioControllerService;
use App_Model_MatriculaSituacao;
use Avaliacao_Service_Boletim;
use Database\Factories\LegacyScoreExamFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use stdClass;
use Tests\TestCase;

class DiarioControllerServiceExamScoreTest extends TestCase
{
    use DatabaseTransactions;

    private function serviceWithSituations(array $situations, string $examScore): DiarioControllerService
    {
        $components = new stdClass;
        $components->componentesCurriculares = $situations;

        $boletim = $this->createPartialMock(Avaliacao_Service_Boletim::class, ['preverNotaRecuperacao', 'getSituacaoComponentesCurriculares']);
        $boletim->method('preverNotaRecuperacao')->willReturn($examScore);
        $boletim->method('getSituacaoComponentesCurriculares')->willReturn($components);

        $service = $this->createPartialMock(DiarioControllerService::class, ['getServiceBoletim']);
        $service->method('getServiceBoletim')->willReturn($boletim);

        return $service;
    }

    public function test_updates_exam_score_of_a_component_in_exam()
    {
        $scoreExam = LegacyScoreExamFactory::new()->create(['nota_exame' => 1]);
        $registration = $scoreExam->registration;
        $componentId = $scoreExam->ref_cod_componente_curricular;

        $service = $this->serviceWithSituations([
            $componentId => (object) ['situacao' => App_Model_MatriculaSituacao::EM_EXAME],
        ], '6.5');

        $service->atualizaNotaNecessariaExame(1, $registration->ref_cod_aluno, $componentId, $registration);

        $this->assertDatabaseHas('modules.nota_exame', [
            'ref_cod_matricula' => $registration->getKey(),
            'ref_cod_componente_curricular' => $componentId,
            'nota_exame' => 6.5,
        ]);
    }

    public function test_removes_exam_score_of_a_component_outside_the_registration()
    {
        $scoreExam = LegacyScoreExamFactory::new()->create();
        $registration = $scoreExam->registration;
        $componentId = $scoreExam->ref_cod_componente_curricular;

        // Componente dispensado não aparece na lista de situações da matrícula.
        $service = $this->serviceWithSituations([], '6.5');

        $service->atualizaNotaNecessariaExame(1, $registration->ref_cod_aluno, $componentId, $registration);

        $this->assertDatabaseMissing('modules.nota_exame', [
            'ref_cod_matricula' => $registration->getKey(),
            'ref_cod_componente_curricular' => $componentId,
        ]);
    }
}
