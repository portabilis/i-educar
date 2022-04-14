<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App_Model_MatriculaSituacao;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyExemptionStageFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExemptionTest extends TestCase
{
    use DiarioApiFakeDataTestTrait;
    use DiarioApiRequestTestTrait;
    use DatabaseTransactions;

    /** @var LegacyEnrollment */
    private $enrollment;

    public function setUp(): void
    {
        parent::setUp();
        $this->enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    }

    /**
     * O aluno deverá ser aprovado sem lançamentos nas etapas dispensadas
     */
    public function testApproveWithoutPostGradeInExemptionStages()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $school = $schoolClass->school;

        $this->createStages($school, 4);
        $this->createDisciplines($schoolClass, 2);

        $registration = $this->enrollment->registration;
        $disciplines = $schoolClass->disciplines;

        // Dispensa a primeira etapa da primeira disciplina
        /** @var LegacyDisciplineExemption $dispensa */
        $dispensa = LegacyDisciplineExemptionFactory::new()->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_disciplina' => $disciplines[0]->id,
            'ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_cod_serie' => $registration->ref_ref_cod_serie,
        ]);

        LegacyExemptionStageFactory::new()->create([
            'ref_cod_dispensa' => $dispensa->cod_dispensa,
            'etapa' => 1,
        ]);

        // Sem lançamentos para a etapa dispensada na primeira disciplina
        $score = [
            2 => 5.4,
            3 => 6.7,
            4 => 10,
        ];

        $absence = [
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        $this->postAbsenceForStages($absence, $disciplines[0]);
        $response = $this->postScoreForStages($score, $disciplines[0]);
        $this->assertEquals('Aprovado', $response->situacao);

        // Lança notas e faltas para todos as etapas da segunda disciplina
        $score = [
            1 => 9.1,
            2 => 5.4,
            3 => 6.7,
            4 => 10,
        ];

        $absence = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        $this->postAbsenceForStages($absence, $disciplines[1]);
        $response = $this->postScoreForStages($score, $disciplines[1]);
        $this->assertEquals('Aprovado', $response->situacao);

        $this->assertEquals(App_Model_MatriculaSituacao::APROVADO, $registration->refresh()->aprovado);
    }

    /**
     * O aluno deverá ser aprovado sem lançamentos em disciplinas dispensadas
     * em todas as etapas
     */
    public function testApproveWithoutPostGradeInExemption()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $school = $schoolClass->school;

        $this->createStages($school, 2);
        $this->createDisciplines($schoolClass, 2);

        $registration = $this->enrollment->registration;

        /** @var LegacyDiscipline[] $disciplines */
        $disciplines = $schoolClass->disciplines;

        // Dispensa as duas etapas da primeira disciplina
        /** @var LegacyDisciplineExemption $dispensa */
        $dispensa = LegacyDisciplineExemptionFactory::new()->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_disciplina' => $disciplines[0]->id,
            'ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_cod_serie' => $registration->ref_ref_cod_serie,
        ]);

        LegacyExemptionStageFactory::new()->create([
            'ref_cod_dispensa' => $dispensa->cod_dispensa,
            'etapa' => 1,
        ]);
        LegacyExemptionStageFactory::new()->create([
            'ref_cod_dispensa' => $dispensa->cod_dispensa,
            'etapa' => 2,
        ]);

        // Lança notas e faltas somente para a segunda disciplina
        $score = [
            1 => 9.1,
            2 => 5.4,
        ];

        $absence = [
            1 => 3,
            2 => 3,
        ];

        $this->postAbsenceForStages($absence, $disciplines[1]);
        $response = $this->postScoreForStages($score, $disciplines[1]);
        $this->assertEquals('Aprovado', $response->situacao);

        $this->assertEquals(App_Model_MatriculaSituacao::APROVADO, $registration->refresh()->aprovado);
    }

    /**
     * Aluno com dispensa na ultima etapa de uma disciplina, deve ser aprovado após o lançamento
     * de nota nas etapas não dispensadas
     */
    public function testApproveWithExemptionInLastStage()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $school = $schoolClass->school;

        //Cria duas disciplinas e duas etapas
        $this->createStages($school, 2);
        $this->createDisciplines($schoolClass, 2);

        $registration = $this->enrollment->registration;
        $disciplines = $schoolClass->disciplines;

        // Lança notas e faltas para todos as etapas da segunda disciplina
        $score = [
            1 => 9.1,
            2 => 5.4,
        ];

        $absence = [
            1 => 3,
            2 => 3,
        ];

        $this->postAbsenceForStages($absence, $disciplines[1]);
        $response = $this->postScoreForStages($score, $disciplines[1]);
        $this->assertEquals('Aprovado', $response->situacao);

        // Dispensa a ultima etapa da primeira disciplina
        /** @var LegacyDisciplineExemption $dispensa */
        $dispensa = LegacyDisciplineExemptionFactory::new()->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_disciplina' => $disciplines[0]->id,
            'ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_cod_serie' => $registration->ref_ref_cod_serie,
        ]);

        LegacyExemptionStageFactory::new()->create([
            'ref_cod_dispensa' => $dispensa->cod_dispensa,
            'etapa' => 2,
        ]);

        // Sem lançamentos para a etapa dispensada na primeira disciplina
        $score = [
            1 => 7,
        ];

        $absence = [
            1 => 3,
        ];

        $this->postAbsenceForStages($absence, $disciplines[0]);
        $response = $this->postScoreForStages($score, $disciplines[0]);
        $this->assertEquals('Aprovado', $response->situacao);

        $this->assertEquals(App_Model_MatriculaSituacao::APROVADO, $registration->refresh()->aprovado);
    }
}
