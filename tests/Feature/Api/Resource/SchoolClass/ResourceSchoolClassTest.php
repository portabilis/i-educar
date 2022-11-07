<?php

namespace Tests\Feature\Api\Resource\SchoolClass;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
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

    /**
     * @var string
     */
    private string $route = 'api.resource.school-class';

    protected function setUp(): void
    {
        parent::setUp();

        return;

        //instituição
        $this->institution = LegacyInstitutionFactory::new()->create();

        //escolas / ano academico
        $schools = LegacySchoolFactory::new()->count(2)->hasAcademicYears()->create([
            'ref_cod_instituicao' => $this->institution->id
        ]);

        //escolas
        $this->school = $schools->first();

        //ano academico
        $this->year = LegacySchoolAcademicYear::where('ref_cod_escola', $this->school->id)->first()->year;

        $schools->each(function ($school) {
            //cursos
            LegacyCourseFactory::new()->count(2)->create(['ref_cod_instituicao' => $this->institution->id])->each(function ($course) use ($school) {
                //escola_curso
                LegacySchoolCourseFactory::new()->create([
                    'ref_cod_curso' => $course->id,
                    'ref_cod_escola' => $school->id
                ]);

                //series
                LegacyGradeFactory::new()->count(2)->create(['ref_cod_curso' => $course->id])->each(function ($grade) use ($school, $course) {
                    //escol_serie
                    LegacySchoolGradeFactory::new()->create([
                        'ref_cod_serie' => $grade->id,
                        'ref_cod_escola' => $school->id
                    ]);

                    //turmas
                    LegacySchoolClassFactory::new()->count(2)->create([
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
        $this->markTestSkipped();

        return;

        $response = $this->getJson(route($this->route, ['institution' => $this->institution, 'school' => $this->school, 'course' => $this->course, 'grade' => $this->grade, 'in_progress_year' => $this->year]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);

        $school_classes =  LegacySchoolClass::getResource([
            'institution' => $this->institution->id,
            'school' => $this->school->id,
            'course' => $this->course->id,
            'grade' => $this->grade->id,
            'in_progress_year' => $this->year
        ]);

        $response->assertJson(function (AssertableJson $json) use ($school_classes) {
            $json->has('data', 2);

            foreach ($school_classes as $key => $school_class) {
                $json->has('data.'.$key, function ($json) use ($school_class) {
                    $json->where('id', $school_class['id']);
                    $json->where('name', $school_class['name']);
                });
            }
        });
    }

    public function test_required_parameters(): void
    {
        $this->markTestSkipped();

        return;

        $response = $this->getJson(route($this->route));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_invalid_parameters(): void
    {
        $this->markTestSkipped();

        return;

        $response = $this->getJson(route($this->route, ['institution' => 'Instituição', 'school' => 'Escola', 'course' => 'Curso', 'grade' => 'Serie', 'in_progress_year' => '202']));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
