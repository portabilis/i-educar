<?php

namespace Tests\Api;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyGeneralScore;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentFactory;
use iEducar\Support\Exceptions\Error;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use RegraAvaliacao_Model_TipoRecuperacaoParalela;
use Tests\TestCase;

class NotasApiV2Test extends TestCase
{
    use DatabaseTransactions;

    private const TOKEN = 'token-de-teste-notas';

    public function test_salva_nota_do_componente(): void
    {
        $cenario = $this->criarCenario();

        $response = $this->postNota($cenario, ['nota' => 6, 'recuperacao' => 2]);

        $response->assertCreated()->assertJson(['message' => 'Nota salva com sucesso.']);

        $disciplineScore = LegacyDisciplineScore::first();

        $this->assertDatabaseHas($disciplineScore->registrationScore->getTable(), [
            'matricula_id' => $cenario['registration']->getKey(),
        ]);

        $this->assertDatabaseHas($disciplineScore->getTable(), [
            'nota_aluno_id' => $disciplineScore->registrationScore->getKey(),
            'componente_curricular_id' => $cenario['discipline']->getKey(),
            'nota' => 6,
            'nota_arredondada' => 6.0,
            'etapa' => 1,
            'nota_recuperacao' => 2,
            'nota_original' => 6,
            'nota_recuperacao_especifica' => null,
        ]);

        $this->assertDatabaseCount(LegacyGeneralScore::class, 0);
    }

    public function test_atualiza_nota_ja_lancada_sem_duplicar_registro(): void
    {
        $cenario = $this->criarCenario();

        $this->postNota($cenario, ['nota' => 6, 'recuperacao' => 2])->assertOk();
        $this->postNota($cenario, ['nota' => 6, 'recuperacao' => 7])->assertOk();

        $disciplineScore = LegacyDisciplineScore::first();

        $this->assertDatabaseHas($disciplineScore->getTable(), [
            'nota_aluno_id' => $disciplineScore->registrationScore->getKey(),
            'componente_curricular_id' => $cenario['discipline']->getKey(),
            'nota' => 7,
            'nota_arredondada' => 7.0,
            'etapa' => 1,
            'nota_recuperacao' => 7,
            'nota_original' => 6,
        ]);

        $this->assertDatabaseCount($disciplineScore->registrationScore->getTable(), 1);
        $this->assertDatabaseCount($disciplineScore->getTable(), 1);
    }

    public function test_rejeita_nota_acima_da_nota_maxima_da_regra(): void
    {
        $cenario = $this->criarCenario();

        $response = $this->postNota($cenario, ['nota' => 11, 'recuperacao' => null]);

        $response->assertUnprocessable()
            ->assertJsonPath('code', Error::SCORE_GREATER_THAN_MAX_ALLOWED);

        $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
    }

    public function test_rejeita_aluno_sem_matricula_na_turma(): void
    {
        $cenario = $this->criarCenario();
        $outroAluno = LegacyStudentFactory::new()->create();

        $response = $this->postNota($cenario, [
            'nota' => 6,
            'recuperacao' => null,
            'aluno_id' => $outroAluno->getKey(),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('aluno_id');
        $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
    }

    public function test_rejeita_componente_que_nao_pertence_a_turma(): void
    {
        $cenario = $this->criarCenario();
        $outroComponente = LegacyDisciplineFactory::new()->create();

        $response = $this->postNota($cenario, [
            'nota' => 6,
            'recuperacao' => null,
            'componente_id' => $outroComponente->getKey(),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('componente_id');
        $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
    }

    public function test_recusa_requisicao_sem_o_token_da_configuracao_geral(): void
    {
        $cenario = $this->criarCenario();

        $this->postJson('/api/v2/notas', $this->payload($cenario, ['nota' => 6, 'recuperacao' => null]))
            ->assertUnauthorized();

        $this->withHeader('token', 'token-invalido')
            ->postJson('/api/v2/notas', $this->payload($cenario, ['nota' => 6, 'recuperacao' => null]))
            ->assertUnauthorized();

        $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
    }

    private function postNota(array $cenario, array $overrides): TestResponse
    {
        return $this->withHeader('token', self::TOKEN)
            ->postJson('/api/v2/notas', $this->payload($cenario, $overrides));
    }

    private function payload(array $cenario, array $overrides): array
    {
        return array_merge([
            'turma_id' => $cenario['schoolClass']->getKey(),
            'aluno_id' => $cenario['student']->getKey(),
            'componente_id' => $cenario['discipline']->getKey(),
            'etapa' => 1,
        ], $overrides);
    }

    private function criarCenario(): array
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()
            ->standardAcademicYear()
            ->create(['ref_cod_instituicao' => $school->ref_cod_instituicao]);

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $year = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $stage = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'num_etapas' => 4,
        ]);

        LegacyAcademicYearStageFactory::new()->create([
            'ref_ano' => $year->ano,
            'ref_ref_cod_escola' => $school,
            'ref_cod_modulo' => $stage,
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $level,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        // A definition da factory sorteia tipo de nota e de presença; o teste
        // fixa os dois para que as notas assertadas não dependam do sorteio.
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => \RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            'tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA,
            'tipo_nota' => \RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
            'tipo_presenca' => \RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'hora_falta' => null,
        ]);

        $evaluationRuleGradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule->getKey(),
            'serie_id' => $level,
        ]);

        $student = LegacyStudentFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student,
            'ano' => $evaluationRuleGradeYear->ano_letivo,
            'ref_ref_cod_serie' => $level,
            'ref_ref_cod_escola' => $school,
            'ref_cod_curso' => $course,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        $this->configurarToken();

        return compact('school', 'schoolClass', 'student', 'discipline', 'registration');
    }

    /**
     * O middleware resolve a instituição ativa pelo container, então o token
     * precisa estar na configuração geral dessa mesma instituição.
     */
    private function configurarToken(): void
    {
        $institution = LegacyInstitution::query()->where('ativo', 1)->firstOrFail();

        LegacyGeneralConfiguration::query()->updateOrCreate(
            ['ref_cod_instituicao' => $institution->getKey()],
            ['token_novo_educacao' => self::TOKEN],
        );
    }
}
