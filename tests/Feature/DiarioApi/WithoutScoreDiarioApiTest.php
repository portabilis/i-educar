<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEvaluationRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class WithoutScoreDiarioApiTest extends TestCase
{
    use DatabaseTransactions, DiarioApiRequestTestTrait, DiarioApiFakeDataTestTrait;

    /**
     * @var LegacyEvaluationRule
     */
    private $evaluationRule;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->evaluationRule = factory(LegacyEvaluationRule::class, 'without-score')->create();
    }

    public function testPostAbsenceShouldReturnsApproved()
    {
        $enrollment = $this->getCommonFakeData($this->evaluationRule);

        $registration = $enrollment->registration;
        $discipline = $enrollment->schoolClass->disciplines()->first();

        $response = $this->postAbsence($enrollment, $discipline->id, 1, 10);

        $this->assertEquals('Aprovado', $response->situacao);
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }
}
