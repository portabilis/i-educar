<?php

namespace Tests\Feature\Api\Resource\Grade;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceGradeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacyCourse
     */
    private LegacyCourse $course;
    /**
     * @var Collection
     */
    private Collection $schools;

    /**
     * @var LegacyGrade
     */
    private LegacyGrade $grade;

    /**
     * @var LegacySchool
     */
    private LegacySchool $school;

    /**
     * @var string
     */
    private string $route = 'api.resource.grade';

    protected function setUp(): void
    {
        parent::setUp();

        //curso
        $this->course = LegacyCourseFactory::new()->create();

        //escolas
        $this->schools = LegacySchoolFactory::new()->count(2)->create();
        //escola
        $this->school = $this->schools->first();

        $this->schools->each(function ($school) {
            LegacyGradeFactory::new()->count(3)->create(['ref_cod_curso' => $this->course->id])->each(function ($grade) use ($school) {
                LegacySchoolGradeFactory::new()->create([
                    'ref_cod_serie' => $grade->id,
                    'ref_cod_escola' => $school->id
                ]);
            });
        });

        //serie
        $this->grade = $this->course->grades()->first();
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route($this->route, ['course' => $this->course->id, 'school' => $this->school]));

        $response->assertStatus(200);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);

        $grades = LegacyGrade::getResource([
            'course' => $this->course->id,
            'school' => $this->school->id
        ]);

        $response->assertJson(function (AssertableJson $json) use ($grades) {
            $json->has('data', 3);

            foreach ($grades as $key => $grade) {
                $json->has('data.'.$key, function ($json) use ($grade) {
                    $json->where('id', $grade['id']);
                    $json->where('name', $grade['name']);
                });
            }
        });
    }

    public function test_parametes_exclude_exact_json_match(): void
    {
        $grade_exclude_id = $this->grade->id;
        $school_exclude_id = $this->school->id;

        $response = $this->getJson(route($this->route, ['course' => $this->course->id, 'grade_exclude' => $grade_exclude_id, 'school_exclude' => $school_exclude_id]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);

        $grades = LegacyGrade::getResource([
            'course' => $this->course->id,
            'gradeExclude' => $grade_exclude_id,
            'schoolExclude' => $school_exclude_id
        ]);

        $response->assertJson(function (AssertableJson $json) use ($grades) {
            $json->has('data', 3);

            foreach ($grades as $key => $grade) {
                $json->has('data.'.$key, function ($json) use ($grade) {
                    $json->where('id', $grade['id']);
                    $json->where('name', $grade['name']);
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
        $response = $this->getJson(route('api.resource.grade', ['course' => 'Curso', 'school' => 'Escola', 'grade_exclude' => 'Serie', 'school_exclude' => 'Escola']));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
