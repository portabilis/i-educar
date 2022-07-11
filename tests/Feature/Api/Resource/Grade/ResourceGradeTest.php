<?php

namespace Tests\Feature\Api\Resource\Grade;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\Model;
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


    protected function setUp(): void
    {
        parent::setUp();

        //curso
        $this->course = LegacyCourse::factory()->create();

        //escolas
        $this->schools = LegacySchool::factory(2)->create();
        //escola
        $this->school = $this->schools->first();

        $this->schools->each(function ($school) {
            LegacyGrade::factory(2)->create(['ref_cod_curso' => $this->course->id])->each(function ($grade) use ($school) {
                LegacySchoolGrade::factory()->create([
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
        $response = $this->getJson(route('resource::api.grade', ['course' => $this->course->id, 'school' => $this->school]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name'
            ]
        ]);

        //compara quantidade e valores
        $grades = LegacyGrade::select('cod_serie as id')->selectName()
            ->whereCourse($this->course->id)->whereSchool($this->school->id)
            ->active()->orderByNameAndCourse()->get();

        $response->assertJson(function (AssertableJson $json) use ($grades) {
            $json->has(2);

            foreach ($grades as $key => $grade) {
                $json->has($key, function ($json) use ($grade) {
                    $json->where('id', $grade->id);
                    $json->where('name', $grade->name);
                });
            }
        });
    }

    public function test_parametes_exclude_exact_json_match(): void
    {
        $grade_exclude_id = $this->grade->id;
        $school_exclude_id = $this->school->id;

        $response = $this->getJson(route('resource::api.grade', ['course' => $this->course->id, 'grade_exclude' => $grade_exclude_id, 'school_exclude' => $school_exclude_id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name'
            ]
        ]);

        //compara quantidade e valores
        $grades = LegacyGrade::select('cod_serie as id')->selectName()
            ->whereCourse($this->course->id)->whereNotGrade($grade_exclude_id)->whereNotSchool($school_exclude_id)
            ->active()->orderByNameAndCourse()->get();

        $response->assertJson(function (AssertableJson $json) use ($grades) {
            $json->has(2);

            foreach ($grades as $key => $grade) {
                $json->has($key, function ($json) use ($grade) {
                    $json->where('id', $grade->id);
                    $json->where('name', $grade->name);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.grade', ['course' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.grade'));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.grade', ['course' => 'Curso', 'school' => 'Escola', 'grade_exclude' => 'Serie', 'school_exclude' => 'Escola']));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }
}
