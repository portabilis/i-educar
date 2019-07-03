<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyDiscipline;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacySchoolClass;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WithoutScoreDiarioApiTest extends TestCase
{
    use DatabaseTransactions, DiarioApiTestTrait;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
    }

    public function testRegraAvaliacaoSemNota()
    {
        $evaluationRule = factory(LegacyEvaluationRule::class, 'without-score')->create();
        /** @var LegacySchoolClass $schoolClass */
        $schoolClass = factory(LegacySchoolClass::class)->create();
        $level = $schoolClass->grade;

        $level->evaluationRules()->attach($evaluationRule->id, ['ano_letivo' => 2019]);

        $disciplines = factory(LegacyDiscipline::class, 2)->create();

        $school = $schoolClass->school;

        $schoolClass->disciplines()->attach($disciplines[0]->id, ['ano_escolar_id' => 1, 'escola_id' => $school->id]);

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        $response = $this->postScore($enrollment, $disciplines[0]->id, 1, 10);
    }
}
