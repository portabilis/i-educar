<?php

namespace Tests\Feature\Api\Resource\SchoolClass;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceSchoolClassTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacyInstitution
     */
    private LegacyInstitution $institution;
    /**
     * @var LegacySchool
     */
    private LegacySchool $school;

    /**
     * @var LegacyCourse
     */
    private LegacyCourse $course;

    /**
     * @var LegacyGrade
     */
    private LegacyGrade $grade;

    /**
     * @var int|HigherOrderBuilderProxy|mixed
     */
    private int $year;

    protected function setUp(): void
    {
        parent::setUp();

        //instituição
        $this->institution = LegacyInstitution::factory()->create();

        //escolas / ano academico
        $schools = LegacySchool::factory(2)->hasAcademicYears()->create([
            'ref_cod_instituicao' => $this->institution->id
        ]);

        //escolas
        $this->school = $schools->first();

        //ano academico
        $this->year = LegacySchoolAcademicYear::where('ref_cod_escola', $this->school->id)->first()->year;

        $schools->each(function ($school) {
            //cursos
            LegacyCourse::factory(2)->create(['ref_cod_instituicao' => $this->institution->id])->each(function ($course) use ($school) {
                //escola_curso
                LegacySchoolCourse::factory()->create([
                    'ref_cod_curso' => $course->id,
                    'ref_cod_escola' => $school->id
                ]);

                //series
                LegacyGrade::factory(2)->create(['ref_cod_curso' => $course->id])->each(function ($grade) use ($school, $course) {
                    //escol_serie
                    LegacySchoolGrade::factory()->create([
                        'ref_cod_serie' => $grade->id,
                        'ref_cod_escola' => $school->id
                    ]);

                    //turmas
                    LegacySchoolClass::factory(2)->create([
                        'ref_ref_cod_serie' => $grade->id,
                        'ref_ref_cod_escola' => $school->id,
                        'ref_cod_curso' => $course->id,
                        'ref_cod_instituicao' => $this->institution->id,
                        'ano' => $this->year
                    ]);
                });

            });
        });

        //curso
        $this->course = LegacyCourse::where('ref_cod_instituicao', $this->institution->id)->first();
        //serie
        $this->grade = LegacyGrade::where('ref_cod_curso', $this->course->id)->first();
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route('resource::api.school-class', ['institution' => $this->institution, 'school' => $this->school, 'course' => $this->course, 'grade' => $this->grade, 'year' => $this->year]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name'
            ]
        ]);

        $school_classes = LegacySchoolClass::select(['cod_turma as id'])->selectName()
            ->whereInstitution($this->institution->id)->whereSchool($this->school->id)->whereCourse($this->course->id)->whereGrade($this->grade->id)->whereInProgress($this->year)
            ->active()->orderByName()
            ->get();

        $response->assertJson(function (AssertableJson $json) use ($school_classes) {
            $json->has(2);

            foreach ($school_classes as $key => $school_class) {
                $json->has($key, function ($json) use ($key, $school_class) {
                    $json->where('id', $school_class->id);
                    $json->where('name', $school_class->name);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.school-class', ['institution' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.school-class'));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.school-class', ['institution' => 'Instituição', 'school' => 'Escola', 'course' => 'Curso', 'grade' => 'Serie', 'year' => '202']));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }
}
