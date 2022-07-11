<?php

namespace Tests\Feature\Api\Resource\Course;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceCourseTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitution::factory()->create();

        $schools = LegacySchool::factory(2)->create([
            'ref_cod_instituicao' => $this->institution->id
        ]);
        $this->school = $schools->first();

        $schools->each(function ($school) {
            $courses = LegacyCourse::factory(2)->create(['ref_cod_instituicao' => $this->institution->id]);

            $courses->each(function ($course) use ($school) {
                LegacySchoolCourse::factory()->create([
                    'ref_cod_curso' => $course->id,
                    'ref_cod_escola' => $school->id
                ]);
            });
        });
    }


    public function test_exact_json_match()
    {
        $response = $this->getJson(route('resource::api.course', ['institution' => $this->institution, 'school' => $this->school]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'is_standard_calendar',
                'steps'
            ]
        ]);

        $courses = LegacyCourse::select(['cod_curso as id', 'padrao_ano_escolar as is_standard_calendar', 'qtd_etapas as steps'])->selectName()
            ->whereInstitution($this->institution->id)->whereSchool($this->school->id)
            ->active()->orderByName()->get();

        $response->assertJson(function (AssertableJson $json) use ($courses) {
            $json->has(2);

            foreach ($courses as $key => $course) {
                $json->has($key, function ($json) use ($course) {
                    $json->where('id', $course->id);
                    $json->where('name', $course->name);
                    $json->where('is_standard_calendar', $course->is_standard_calendar);
                    $json->where('steps', $course->steps);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.course', ['institution' => 0]));

        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.course', ['institution' => 'Instituição', 'school' => 'Escola', 'not_pattern' => '2', 'course' => 'Curso']));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }
}
