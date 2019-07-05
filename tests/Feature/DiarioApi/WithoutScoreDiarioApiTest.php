<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Process;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class WithoutScoreDiarioApiTest extends TestCase
{
    use DatabaseTransactions, DiarioApiTestTrait;
    const MENU_DIARIOAPI = 107;

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

        $disciplines = factory(LegacyDiscipline::class)->create();

        $school = $schoolClass->school;

        $schoolClass->disciplines()->attach($disciplines->id, ['ano_escolar_id' => 1, 'escola_id' => $school->id]);
        $school->courses()->attach($schoolClass->course_id, [
            'ativo' => 1,
            'anos_letivos' => '{'.now()->year.'}',
            'ref_usuario_cad' => factory(User::class, 'admin')->make()->id,
            'data_cadastro' => now(),
        ]);

        factory(LegacyDisciplineAcademicYear::class)->create([
            'componente_curricular_id' => $disciplines->id,
            'ano_escolar_id' => $schoolClass->grade_id,
        ]);

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => factory(LegacyRegistration::class)->create([
                'ref_ref_cod_escola' => $schoolClass->school_id,
                'ref_ref_cod_serie' => $schoolClass->grade_id,
                'ref_cod_curso' => $schoolClass->course_id,
            ]),
        ]);
        $registration = $enrollment->registration;

        factory(LegacySchoolAcademicYear::class)->create([
            'ref_cod_escola' => $school->id,
        ]);

        factory(LegacyAcademicYearStage::class)->create([
            'ref_ano' => now()->year,
            'ref_ref_cod_escola' => $school->id,
        ]);

        /** @var User $user */
        $user = factory(User::class, 'admin')->make();
        $user->processes()->attach(self::MENU_DIARIOAPI,['cadastra' => 1]);
        Auth::setUser($user);

        $response = $this->postAbsence($enrollment, $disciplines->id, 1, 10);

        $this->assertEquals('Aprovado', $response['situacao']);
        $this->assertEquals(1,$registration->refresh()->aprovado);
    }
}
