<?php

namespace Tests\Api;

use App\Models\LegacyEvaluationRule;
use App_Model_MatriculaSituacao;
use Database\Factories\LegacyEvaluationRuleFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WithoutScoreDiarioApiTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;
    use DiarioApiFakeDataTestTrait;

    /**
     * @var LegacyEvaluationRule
     */
    private $evaluationRule;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationRule = LegacyEvaluationRuleFactory::new()->withoutScore()->create();
    }

    /**
     * O aluno deve ser aprovado ao lançar todas as faltas
     */
    public function testPostAbsenceShouldReturnsApproved()
    {
        $enrollment = $this->getCommonFakeData($this->evaluationRule);
        $schoolClass = $enrollment->schoolClass;
        $school = $schoolClass->school;
        $registration = $enrollment->registration;

        $this->createStages($school, 1);
        $this->createDisciplines($schoolClass, 1);

        $discipline = $schoolClass->refresh()->disciplines()->first();

        $response = $this->postAbsence($enrollment, $discipline->id, 1, 10);

        $this->assertEquals('Aprovado', $response->situacao);
        $this->assertEquals(App_Model_MatriculaSituacao::APROVADO, $registration->refresh()->aprovado);
    }

    /**
     * O aluno deve continuar cursando quando não forem lançadas as faltas de todas as etapas
     */
    public function testPostAPartOfAbsenceShouldReturnsStudying()
    {
        $enrollment = $this->getCommonFakeData($this->evaluationRule);
        $schoolClass = $enrollment->schoolClass;
        $school = $schoolClass->school;
        $registration = $enrollment->registration;

        $this->createStages($school, 2);
        $this->createDisciplines($schoolClass, 1);

        $discipline = $schoolClass->refresh()->disciplines()->first();

        $response = $this->postAbsence($enrollment, $discipline->id, 1, 10);

        $this->assertEquals('Cursando', $response->situacao);
        $this->assertEquals(App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
    }
}
