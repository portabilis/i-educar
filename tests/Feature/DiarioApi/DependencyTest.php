<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEnrollment;
use App_Model_MatriculaSituacao;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DependencyTest extends TestCase
{
    use DiarioApiFakeDataTestTrait, DiarioApiRequestTestTrait, DatabaseTransactions;

    /** @var LegacyEnrollment */
    private $enrollment;

    public function setUp(): void
    {
        parent::setUp();
        $this->enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    }

    public function testWithDependencyShouldReturnsApprovedWithDependency()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $school = $schoolClass->school;

        $this->createStages($school, 4);
        $this->createDisciplines($schoolClass, 2);

        $evaluationRule = $schoolClass->grade->evaluationRules()->first();
        $evaluationRule->qtd_disciplinas_dependencia = 2;
        $evaluationRule->save();

        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 4.3,
            2 => 5.4,
            3 => 6.7,
            4 => 3,
        ];

        $absence = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Retido', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA, $registration->refresh()->aprovado);
    }
}
