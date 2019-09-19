<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyExemptionStage;
use App_Model_MatriculaSituacao;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExemptionTest extends TestCase
{
    use DiarioApiFakeDataTestTrait, DiarioApiRequestTestTrait;

    /**
     * @var LegacyEnrollment
     */
    private $enrollment;

    public function setUp(): void
    {
        parent::setUp();
        $this->enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    }

    /**
     * O alun deverá ser aprovado sem lançamentos nas etapas dispensadas
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
        $dispensa = factory(LegacyDisciplineExemption::class)->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_disciplina' => $disciplines[0]->id,
            'ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_cod_serie' => $registration->ref_ref_cod_serie,
        ]);

        factory(LegacyExemptionStage::class)->create([
            'ref_cod_dispensa' => $dispensa->cod_dispensa,
            'etapa' => 1,
        ]);

        // Sem lançamentos para a etapa dispensada
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
     * O alun deverá ser aprovado sem lançamentos em disciplinas dispensadas
     * em todas as etapas
     */
    public function testApproveWithoutPostGradeInExemption()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $school = $schoolClass->school;

        $this->createStages($school, 2);
        $this->createDisciplines($schoolClass, 2);

        $registration = $this->enrollment->registration;
        $disciplines = $schoolClass->disciplines;

        /** @var LegacyDisciplineExemption $dispensa */
        $dispensa = factory(LegacyDisciplineExemption::class)->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_disciplina' => $disciplines[0]->id,
            'ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_cod_serie' => $registration->ref_ref_cod_serie,
        ]);

        // Dispensa a duas etapa da segunda disciplina
        factory(LegacyExemptionStage::class)->create([
            'ref_cod_dispensa' => $dispensa->cod_dispensa,
            'etapa' => 1,
        ]);
        factory(LegacyExemptionStage::class)->create([
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

}
