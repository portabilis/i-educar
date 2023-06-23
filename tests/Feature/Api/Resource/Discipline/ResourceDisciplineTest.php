<?php

namespace Tests\Feature\Api\Resource\Discipline;

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGradeDiscipline;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceDisciplineTest extends TestCase
{
    use DatabaseTransactions;

    private LegacyCourse $course;

    private LegacyGrade $grade;

    private LegacySchool $school;

    private string $route = 'api.resource.discipline';

    protected function setUp(): void
    {
        parent::setUp();

        //cursos
        $courses = LegacyCourseFactory::new()->count(2)->create();
        //curso
        $this->course = $courses->first();

        //escolas
        $schools = LegacySchoolFactory::new()->count(2)->create();
        //escola
        $this->school = $schools->first();

        $schools->each(function ($school) use ($courses) {
            $courses->each(function ($course) use ($school) {
                //series
                $grade = LegacyGradeFactory::new()->create([
                    'ref_cod_curso' => $course->id,
                ]);

                //escol_serie
                LegacySchoolGradeFactory::new()->create([
                    'ref_cod_serie' => $grade->id,
                    'ref_cod_escola' => $school->id,
                ]);

                //componente curricular ano escolar
                $discipline_academic_years = LegacyDisciplineAcademicYearFactory::new()->count(2)->create([
                    'ano_escolar_id' => $grade->id,
                    'hora_falta' => null,
                ]);

                $discipline_academic_years->each(function ($discipline_academic_year) use ($school, $grade) {
                    //escola serie disciplina
                    LegacySchoolGradeDisciplineFactory::new()->create([
                        'ref_ref_cod_serie' => $grade->id,
                        'ref_ref_cod_escola' => $school->id,
                        'ref_cod_disciplina' => $discipline_academic_year->id,
                    ]);
                });
            });
        });

        $this->grade = $this->course->grades()->first();
    }

    public function test_discipline_academic_year_exact_json_match()
    {
        $response = $this->getJson(route($this->route, ['grade' => $this->grade, 'course' => $this->course]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'workload',
                ],
            ],
        ]);

        $disciplines = LegacyDisciplineAcademicYear::getResource([
            'course' => $this->course->id,
            'grade' => $this->grade->id,
        ]);

        $response->assertJson(function (AssertableJson $json) use ($disciplines) {
            $json->has('data', 2);

            foreach ($disciplines as $key => $discipline) {
                $json->has('data.' . $key, function ($json) use ($discipline) {
                    $json->where('id', $discipline['id']);
                    $json->where('name', $discipline['name']);
                    $json->where('workload', $discipline['workload']);
                });
            }
        });
    }

    public function test_discipline_school_grade_exact_json_match()
    {
        $response = $this->getJson(route($this->route, ['school' => $this->school, 'grade' => $this->grade]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'workload',
                ],
            ],
        ]);

        $disciplines = LegacySchoolGradeDiscipline::getResource([
            'school' => $this->school->id,
            'grade' => $this->grade->id,
        ]);

        $response->assertJson(function (AssertableJson $json) use ($disciplines) {
            $json->has('data', 2);

            foreach ($disciplines as $key => $discipline) {
                $json->has('data.' . $key, function ($json) use ($discipline) {
                    $json->where('id', $discipline['id']);
                    $json->where('name', $discipline['name']);
                    $json->where('workload', $discipline['workload']);
                });
            }
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route($this->route));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route($this->route, ['school' => 'Escola', 'course' => 'Curso', 'grade' => 'Série']));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
