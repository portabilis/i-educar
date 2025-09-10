<?php

namespace Tests\Unit\Services;

use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use App\Services\SchoolGradeImportService;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolGradeImportServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected SchoolGradeImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SchoolGradeImportService;
    }

    public function test_process_batch_update_success()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
        ]);
    }

    public function test_process_batch_update_with_invalid_school()
    {
        $user = LegacyUserFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [999999],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não encontrada ou inativa', $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_invalid_grade()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [999999],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não encontrada ou inativa', $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_academic_year_not_found()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não possui ano letivo', $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_academic_year_not_started()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 0,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não iniciado', $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_academic_year_finalized()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 2,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('finalizado', $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_school_without_course()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não possui o ano', $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_school_without_grade_course()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $differentGrade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $differentGrade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não possui o curso da série', $result['errors'][0]['error']);
        $this->assertStringContainsString($grade->nm_serie, $result['errors'][0]['error']);
    }

    public function test_process_batch_update_with_grade_without_disciplines()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não possui componentes curriculares cadastrados', $result['errors'][0]['error']);
        $this->assertStringContainsString($grade->nm_serie, $result['errors'][0]['error']);
    }

    public function test_process_batch_update_multiple_schools_and_grades()
    {
        $user = LegacyUserFactory::new()->create();
        $school1 = LegacySchoolFactory::new()->create();
        $school2 = LegacySchoolFactory::new()->create();
        $grade1 = LegacyGradeFactory::new()->create();
        $grade2 = LegacyGradeFactory::new()->create();
        $year = now()->year;

        foreach ([$school1, $school2] as $school) {
            LegacySchoolAcademicYearFactory::new()->create([
                'ref_cod_escola' => $school->cod_escola,
                'ano' => $year,
                'ativo' => 1,
                'andamento' => 1,
            ]);
            foreach ([$grade1, $grade2] as $grade) {
                LegacySchoolCourseFactory::new()->create([
                    'ref_cod_escola' => $school->cod_escola,
                    'ref_cod_curso' => $grade->ref_cod_curso,
                    'anos_letivos' => '{' . $year . '}',
                    'ativo' => 1,
                ]);
            }
        }

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade1->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade2->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school1->cod_escola, $school2->cod_escola],
            'grades' => [$grade1->cod_serie, $grade2->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(4, $result['processed']);
        $this->assertEmpty($result['errors']);
    }

    public function test_process_batch_update_with_existing_school_grade()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'anos_letivos' => '{' . ($year - 1) . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $schoolGrade = LegacySchoolGrade::where('ref_cod_escola', $school->cod_escola)
            ->where('ref_cod_serie', $grade->cod_serie)
            ->where('ativo', 1)
            ->first();

        $this->assertNotNull($schoolGrade);
        $anosLetivos = array_map('intval', transformStringFromDBInArray($schoolGrade->anos_letivos));
        $this->assertContains($year, $anosLetivos);
        $this->assertContains($year - 1, $anosLetivos);
    }

    public function test_process_batch_update_with_disciplines()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        $disciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie_disciplina', [
            'ref_ref_cod_serie' => $grade->cod_serie,
            'ref_ref_cod_escola' => $school->cod_escola,
            'ref_cod_disciplina' => $disciplineAcademicYear->componente_curricular_id,
            'ativo' => 1,
        ]);
    }

    public function test_process_batch_update_with_multiple_errors()
    {
        $user = LegacyUserFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [999, 888],
            'grades' => [777, 666],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertCount(4, $result['errors']);
    }

    public function test_validate_params_with_empty_schools()
    {
        $user = LegacyUserFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('obrigatórios', $result['message']);
    }

    public function test_validate_params_with_empty_grades()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('obrigatórios', $result['message']);
    }

    public function test_validate_params_with_empty_year()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => null,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('obrigatórios', $result['message']);
    }

    public function test_validate_params_with_schools_not_array()
    {
        $user = LegacyUserFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => 'not_an_array',
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('devem ser arrays', $result['message']);
    }

    public function test_validate_params_with_grades_not_array()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => 'not_an_array',
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('devem ser arrays', $result['message']);
    }

    public function test_validate_params_with_year_as_string()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => (string) $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
    }

    public function test_validate_params_with_year_as_float()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => 2024.5,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('número inteiro', $result['message']);
    }

    public function test_validate_params_with_year_as_invalid_string()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => 'abc',
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertStringContainsString('número inteiro', $result['message']);
    }

    public function test_process_batch_update_with_blocking_parameters()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ]);
    }

    public function test_process_batch_update_with_blocking_parameters_disabled()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'bloquear_enturmacao_sem_vagas' => 0,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'bloquear_enturmacao_sem_vagas' => 0,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ]);
    }

    public function test_process_batch_update_without_blocking_parameters_should_default_to_zero()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'bloquear_enturmacao_sem_vagas' => 0,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ]);
    }

    public function test_process_batch_update_creates_school_grade_disciplines()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        $disciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade->cod_serie,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie_disciplina', [
            'ref_ref_cod_serie' => $grade->cod_serie,
            'ref_ref_cod_escola' => $school->cod_escola,
            'ref_cod_disciplina' => $disciplineAcademicYear->componente_curricular_id,
            'ativo' => 1,
        ]);
    }

    public function test_process_batch_update_updates_existing_school_grade()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $existingSchoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'anos_letivos' => '{' . ($year - 1) . '}',
            'bloquear_enturmacao_sem_vagas' => 0,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $updatedSchoolGrade = LegacySchoolGrade::where('ref_cod_escola', $existingSchoolGrade->ref_cod_escola)
            ->where('ref_cod_serie', $existingSchoolGrade->ref_cod_serie)
            ->first();
        $this->assertNotNull($updatedSchoolGrade);
        $this->assertEquals(1, $updatedSchoolGrade->bloquear_enturmacao_sem_vagas);
        $this->assertEquals(1, $updatedSchoolGrade->bloquear_cadastro_turma_para_serie_com_vagas);

        $anosLetivos = transformStringFromDBInArray($updatedSchoolGrade->anos_letivos);
        $this->assertContains((string) $year, $anosLetivos, 'Anos letivos: ' . json_encode($anosLetivos) . ', ano esperado: ' . (string) $year);
    }

    public function test_process_batch_update_updates_existing_school_grade_disciplines()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'anos_letivos' => '{' . ($year - 1) . '}',
        ]);

        $disciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade->cod_serie,
        ]);

        $existingDiscipline = LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_serie' => $grade->cod_serie,
            'ref_ref_cod_escola' => $school->cod_escola,
            'ref_cod_disciplina' => $disciplineAcademicYear->componente_curricular_id,
            'ativo' => 1,
            'anos_letivos' => '{' . ($year - 1) . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $updatedDiscipline = LegacySchoolGradeDiscipline::where('ref_ref_cod_serie', $existingDiscipline->ref_ref_cod_serie)
            ->where('ref_ref_cod_escola', $existingDiscipline->ref_ref_cod_escola)
            ->where('ref_cod_disciplina', $existingDiscipline->ref_cod_disciplina)
            ->first();
        $this->assertNotNull($updatedDiscipline);
        $this->assertContains((string) $year, transformStringFromDBInArray($updatedDiscipline->anos_letivos));
    }

    public function test_process_batch_update_with_mixed_blocking_parameters()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ]);
    }

    public function test_process_batch_update_reactivates_inactive_school_grade()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 0,
            'anos_letivos' => '{' . ($year - 1) . '}',
            'bloquear_enturmacao_sem_vagas' => 0,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_serie' => $grade->cod_serie,
            'ativo' => 1,
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ]);

        $updatedSchoolGrade = LegacySchoolGrade::where('ref_cod_escola', $school->cod_escola)
            ->where('ref_cod_serie', $grade->cod_serie)
            ->first();

        $this->assertNotNull($updatedSchoolGrade);

        $anosLetivosArray = transformStringFromDBInArray($updatedSchoolGrade->anos_letivos);
        $this->assertIsArray($anosLetivosArray);
        $this->assertContains((string) $year, $anosLetivosArray, "Ano atual {$year} não encontrado nos anos letivos: " . json_encode($anosLetivosArray));
        $this->assertContains((string) ($year - 1), $anosLetivosArray, 'Ano anterior ' . ($year - 1) . ' não encontrado nos anos letivos: ' . json_encode($anosLetivosArray));
    }

    public function test_process_batch_update_message_indicates_created_or_updated()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();

        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->cod_escola,
            'ref_cod_curso' => $grade->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([

            'ano_escolar_id' => $grade->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school->cod_escola],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertNotEmpty($result['details']);
        $this->assertStringContainsString('criada', $result['details'][0]['message']);

        $result2 = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result2['status']);
        $this->assertEquals(1, $result2['processed']);
        $this->assertNotEmpty($result2['details']);
        $this->assertStringContainsString('atualizada', $result2['details'][0]['message']);
    }
}
