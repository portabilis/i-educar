<?php

namespace Tests\Feature\Api\Resource\Discipline;

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceDisciplineTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacyCourse
     */
    private LegacyCourse $course;
    /**
     * @var LegacyGrade
     */
    private LegacyGrade $grade;
    /**
     * @var LegacySchool
     */
    private LegacySchool $school;

    protected function setUp(): void
    {
        parent::setUp();

        //cursos
        $courses = LegacyCourse::factory(2)->create();
        //curso
        $this->course = $courses->first();

        //escolas
        $schools = LegacySchool::factory(2)->create();
        //escola
        $this->school = $schools->first();

        $schools->each(function ($school) use ($courses) {
            $courses->each(function ($course) use ($school) {
                //series
                $grades = LegacyGrade::factory(1)->create([
                    'ref_cod_curso' => $course->id
                ]);

                $grades->each(function ($grade) use ($school) {
                    //escol_serie
                    LegacySchoolGrade::factory()->create([
                        'ref_cod_serie' => $grade->id,
                        'ref_cod_escola' => $school->id
                    ]);
                    //componente curricular ano escolar
                    $discipline_academic_years = LegacyDisciplineAcademicYear::factory(2)->create([
                        'ano_escolar_id' => $grade->id
                    ]);

                    $discipline_academic_years->each(function ($discipline_academic_year) use ($school, $grade) {
                        //escola serie disciplina
                        LegacySchoolGradeDiscipline::factory(1)->create([
                            'ref_ref_cod_serie' => $grade->id,
                            'ref_ref_cod_escola' => $school->id,
                            'ref_cod_disciplina' => $discipline_academic_year->id
                        ]);
                    });
                });
            });
        });


        $this->grade = $this->course->grades()->first();
    }


    public function test_discipline_academic_year_exact_json_match()
    {

        $response = $this->getJson(route('resource::api.discipline', ['grade' => $this->grade, 'course' => $this->course]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'workload'
            ]
        ]);

        $disciplines = LegacyDisciplineAcademicYear::distinctDiscipline()
            ->select(['componente_curricular_id as id', 'carga_horaria as workload'])->addSelectName()
            ->whereCourse($this->course->id)->whereGrade($this->grade->id)
            ->get();

        $response->assertJson(function (AssertableJson $json) use ($disciplines) {
            $json->has(2);

            foreach ($disciplines as $key => $discipline) {
                $json->has($key, function ($json) use ($discipline) {
                    $json->where('id', $discipline->id);
                    $json->where('name', $discipline->name);
                    $json->where('workload', $discipline->workload);
                });
            }
        });


    }

    public function test_discipline_school_grade_exact_json_match()
    {
        $response = $this->getJson(route('resource::api.discipline', ['school' => $this->school, 'grade' => $this->grade]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'workload'
            ]
        ]);

        $disciplines = LegacySchoolGradeDiscipline::distinctDiscipline()
            ->select(['ref_cod_disciplina as id', 'carga_horaria as workload'])->addSelectName()
            ->whereSchool($this->school->id)->whereGrade($this->grade->id)
            ->get();

        $response->assertJson(function (AssertableJson $json) use ($disciplines) {
            $json->has(2);

            foreach ($disciplines as $key => $discipline) {
                $json->has($key, function ($json) use ($discipline) {
                    $json->where('id', $discipline->id);
                    $json->where('name', $discipline->name);
                    $json->where('workload', $discipline->workload);
                });
            }
        });

    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.discipline', ['grade' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.discipline'));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.discipline', ['school' => 'Escola', 'course' => 'Curso', 'grade' => 'Série']));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }
}
