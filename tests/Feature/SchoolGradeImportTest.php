<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolGradeBatchUpdateController;
use App\Services\SchoolGradeImportService;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class SchoolGradeImportTest extends TestCase
{
    use DatabaseTransactions;

    protected SchoolGradeImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SchoolGradeImportService;
    }

    public function test_import_school_grade_success()
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

    public function test_import_school_grade_with_invalid_school()
    {
        $user = LegacyUserFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $params = [
            'schools' => [999999],
            'grades' => [$grade->cod_serie],
            'year' => $year,
            'user' => $user,
            'escola_serie_data' => [
                1 => [
                    'escolas' => [999999],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não encontrada ou inativa', $result['errors'][0]['error']);
    }

    public function test_import_school_grade_with_academic_year_not_started()
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
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não iniciado', $result['errors'][0]['error']);
    }

    public function test_import_school_grade_with_academic_year_finalized()
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
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('finalizado', $result['errors'][0]['error']);
    }

    public function test_import_school_grade_with_school_without_course()
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
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('não possui o ano', $result['errors'][0]['error']);
    }

    public function test_import_with_multiple_lines_escola_serie_format()
    {
        $user = LegacyUserFactory::new()->create();
        $school1 = LegacySchoolFactory::new()->create();
        $school2 = LegacySchoolFactory::new()->create();
        $grade1 = LegacyGradeFactory::new()->create();
        $grade2 = LegacyGradeFactory::new()->create();
        $grade3 = LegacyGradeFactory::new()->create();
        $year = now()->year;

        foreach ([$school1, $school2] as $school) {
            LegacySchoolAcademicYearFactory::new()->create([
                'ref_cod_escola' => $school->cod_escola,
                'ano' => $year,
                'ativo' => 1,
                'andamento' => 1,
            ]);
            foreach ([$grade1, $grade2, $grade3] as $grade) {
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

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade3->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school1->cod_escola, $school2->cod_escola],
            'grades' => [$grade1->cod_serie, $grade2->cod_serie, $grade3->cod_serie],
            'year' => $year,
            'user' => $user,
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade1->cod_serie],
                ],
                2 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade2->cod_serie, $grade3->cod_serie],
                ],
                3 => [
                    'escolas' => [$school2->cod_escola],
                    'series' => [$grade1->cod_serie, $grade2->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(5, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade3->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
            'ativo' => 1,
        ]);
    }

    public function test_line_specific_correspondence()
    {
        $user = LegacyUserFactory::new()->create();
        $school1 = LegacySchoolFactory::new()->create();
        $school2 = LegacySchoolFactory::new()->create();
        $grade1 = LegacyGradeFactory::new()->create();
        $grade2 = LegacyGradeFactory::new()->create();
        $grade3 = LegacyGradeFactory::new()->create();
        $year = now()->year;

        foreach ([$school1, $school2] as $school) {
            LegacySchoolAcademicYearFactory::new()->create([
                'ref_cod_escola' => $school->cod_escola,
                'ano' => $year,
                'ativo' => 1,
                'andamento' => 1,
            ]);
            foreach ([$grade1, $grade2, $grade3] as $grade) {
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

        LegacyDisciplineAcademicYearFactory::new()->create([
            'ano_escolar_id' => $grade3->cod_serie,
            'anos_letivos' => '{' . $year . '}',
        ]);

        $params = [
            'schools' => [$school1->cod_escola, $school2->cod_escola],
            'grades' => [$grade1->cod_serie, $grade2->cod_serie, $grade3->cod_serie],
            'year' => $year,
            'user' => $user,
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade1->cod_serie],
                ],
                2 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade2->cod_serie, $grade3->cod_serie],
                ],
                3 => [
                    'escolas' => [$school2->cod_escola],
                    'series' => [$grade1->cod_serie, $grade2->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(5, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade3->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
            'ativo' => 1,
        ]);

        $this->assertDatabaseMissing('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade3->cod_serie,
            'ativo' => 1,
        ]);
    }

    public function test_clean_incomplete_lines()
    {
        $user = LegacyUserFactory::new()->create();
        $school1 = LegacySchoolFactory::new()->create();
        $school2 = LegacySchoolFactory::new()->create();
        $grade1 = LegacyGradeFactory::new()->create();
        $grade2 = LegacyGradeFactory::new()->create();
        $year = now()->year;

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school1->cod_escola,
            'ano' => $year,
            'ativo' => 1,
            'andamento' => 1,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_curso' => $grade1->ref_cod_curso,
            'anos_letivos' => '{' . $year . '}',
            'ativo' => 1,
        ]);

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
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade1->cod_serie],
                ],
                2 => [
                    'escolas' => [$school2->cod_escola],
                    'series' => [],
                ],
                3 => [
                    'escolas' => [],
                    'series' => [$grade2->cod_serie],
                ],
                4 => [
                    'escolas' => [],
                    'series' => [],
                ],
                5 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade1->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(2, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);

        $this->assertDatabaseMissing('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
        ]);
    }

    public function test_import_with_school_without_academic_year()
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
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);

        $hasSchoolError = false;
        foreach ($result['errors'] as $error) {
            if (str_contains($error['error'], 'não possui ano letivo')) {
                $hasSchoolError = true;
                break;
            }
        }
        $this->assertTrue($hasSchoolError, 'Should have error related to school without academic year');
    }

    public function test_lines_are_isolated_from_each_other()
    {
        $user = LegacyUserFactory::new()->create();
        $school1 = LegacySchoolFactory::new()->create();
        $school2 = LegacySchoolFactory::new()->create();
        $school3 = LegacySchoolFactory::new()->create();
        $grade1 = LegacyGradeFactory::new()->create();
        $grade2 = LegacyGradeFactory::new()->create();
        $grade3 = LegacyGradeFactory::new()->create();
        $year = now()->year;

        foreach ([$school1, $school2] as $school) {
            LegacySchoolAcademicYearFactory::new()->create([
                'ref_cod_escola' => $school->cod_escola,
                'ano' => $year,
                'ativo' => 1,
                'andamento' => 1,
            ]);
            foreach ([$grade1, $grade2, $grade3] as $grade) {
                LegacySchoolCourseFactory::new()->create([
                    'ref_cod_escola' => $school->cod_escola,
                    'ref_cod_curso' => $grade->ref_cod_curso,
                    'anos_letivos' => '{' . $year . '}',
                    'ativo' => 1,
                ]);
            }
        }

        $params = [
            'schools' => [$school1->cod_escola, $school2->cod_escola, $school3->cod_escola],
            'grades' => [$grade1->cod_serie, $grade2->cod_serie, $grade3->cod_serie],
            'year' => $year,
            'user' => $user,
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade1->cod_serie, $grade2->cod_serie],
                ],
                2 => [
                    'escolas' => [$school2->cod_escola],
                    'series' => [$grade2->cod_serie, $grade3->cod_serie],
                ],
                3 => [
                    'escolas' => [$school3->cod_escola],
                    'series' => [$grade1->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('failed', $result['status']);
        $this->assertNotEmpty($result['errors']);

        $this->assertDatabaseMissing('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
        ]);
        $this->assertDatabaseMissing('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
        ]);

        $hasSchool3Error = false;
        foreach ($result['errors'] as $error) {
            if (str_contains($error['error'], 'não possui ano letivo')) {
                $hasSchool3Error = true;
                break;
            }
        }
        $this->assertTrue($hasSchool3Error, 'Should have error related to school3 without academic year');
    }

    public function test_multiple_schools_in_single_line()
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
            'escola_serie_data' => [
                1 => [
                    'escolas' => [$school1->cod_escola, $school2->cod_escola],
                    'series' => [$grade1->cod_serie, $grade2->cod_serie],
                ],
            ],
        ];

        $result = $this->service->processSchoolGradeUpdate($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(4, $result['processed']);
        $this->assertEmpty($result['errors']);

        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school1->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade1->cod_serie,
            'ativo' => 1,
        ]);
        $this->assertDatabaseHas('pmieducar.escola_serie', [
            'ref_cod_escola' => $school2->cod_escola,
            'ref_cod_serie' => $grade2->cod_serie,
            'ativo' => 1,
        ]);
    }

    public function test_controller_sends_correct_data_to_service()
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

        $requestData = [
            'ano' => $year,
            'escola_serie' => [
                1 => [
                    'escolas' => [$school1->cod_escola],
                    'series' => [$grade1->cod_serie],
                ],
                2 => [
                    'escolas' => [$school2->cod_escola],
                    'series' => [$grade2->cod_serie],
                ],
            ],
            'bloquear_enturmacao_sem_vagas' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ];

        $request = new Request($requestData);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $controller = new SchoolGradeBatchUpdateController($this->service);

        $reflection = new \ReflectionClass($controller);
        $cleanRequestDataMethod = $reflection->getMethod('cleanRequestData');
        $cleanRequestDataMethod->setAccessible(true);

        $cleanRequestDataMethod->invoke($controller, $request);

        $this->assertEquals([
            $school1->cod_escola,
            $school2->cod_escola,
        ], $request->get('escola'));

        $this->assertEquals([
            $grade1->cod_serie,
            $grade2->cod_serie,
        ], $request->get('series'));

        $this->assertEquals($requestData['escola_serie'], $request->get('escola_serie'));
    }

    public function test_controller_rejects_incomplete_lines()
    {
        $user = LegacyUserFactory::new()->create();
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $requestData1 = [
            'ano' => $year,
            'escola_serie' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [],
                ],
            ],
        ];

        $request1 = new Request($requestData1);
        $request1->setUserResolver(function () use ($user) {
            return $user;
        });

        $controller = new SchoolGradeBatchUpdateController($this->service);

        $reflection = new \ReflectionClass($controller);
        $cleanRequestDataMethod = $reflection->getMethod('cleanRequestData');
        $cleanRequestDataMethod->setAccessible(true);

        $cleanRequestDataMethod->invoke($controller, $request1);

        $this->assertEmpty($request1->get('escola'));
        $this->assertEmpty($request1->get('series'));

        $requestData2 = [
            'ano' => $year,
            'escola_serie' => [
                1 => [
                    'escolas' => [],
                    'series' => [$grade->cod_serie],
                ],
            ],
        ];

        $request2 = new Request($requestData2);
        $request2->setUserResolver(function () use ($user) {
            return $user;
        });

        $cleanRequestDataMethod->invoke($controller, $request2);

        $this->assertEmpty($request2->get('escola'));
        $this->assertEmpty($request2->get('series'));

        $requestData3 = [
            'ano' => $year,
            'escola_serie' => [
                1 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [$grade->cod_serie],
                ],
                2 => [
                    'escolas' => [$school->cod_escola],
                    'series' => [],
                ],
                3 => [
                    'escolas' => [],
                    'series' => [$grade->cod_serie],
                ],
                4 => [
                    'escolas' => [],
                    'series' => [],
                ],
            ],
        ];

        $request3 = new Request($requestData3);
        $request3->setUserResolver(function () use ($user) {
            return $user;
        });

        $cleanRequestDataMethod->invoke($controller, $request3);

        $this->assertEquals([$school->cod_escola], $request3->get('escola'));
        $this->assertEquals([$grade->cod_serie], $request3->get('series'));
    }
}
