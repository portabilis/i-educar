<?php

namespace Tests\Feature\Api\Resource\Course;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceCourseTest extends TestCase
{
    use DatabaseTransactions;

    private LegacyInstitution $institution;

    private LegacySchool $school;

    private string $route = 'api.resource.course';

    private LegacyCourse $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitutionFactory::new()->create();

        $schools = LegacySchoolFactory::new()->count(2)->create([
            'ref_cod_instituicao' => $this->institution->id,
        ]);
        $this->school = $schools->first();

        $schools->each(function ($school) {
            $courses = LegacyCourseFactory::new()->count(2)->create(['ref_cod_instituicao' => $this->institution->id]);

            $courses->each(function ($course) use ($school) {
                LegacySchoolCourseFactory::new()->create([
                    'ref_cod_curso' => $course->id,
                    'ref_cod_escola' => $school->id,
                ]);
            });
        });

        $this->course = LegacyCourse::whereHas('schools', function ($q) {
            $q->where('cod_escola', $this->school->id);
        })->first();
    }

    public function test_exact_json_match()
    {
        $response = $this->getJson(route($this->route, ['institution' => $this->institution, 'school' => $this->school, 'course' => $this->course]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'is_standard_calendar',
                    'steps',
                ],
            ],
        ]);

        $courses = LegacyCourse::getResource([
            'institution' => $this->institution->id,
            'school' => $this->school->id,
            'course' => $this->course->id,
        ]);

        $response->assertJson(function (AssertableJson $json) use ($courses) {
            $json->has('data', 1);

            foreach ($courses as $key => $course) {
                $json->has('data.'.$key, function ($json) use ($course) {
                    $json->where('id', $course['id']);
                    $json->where('name', $course['name']);
                    $json->where('is_standard_calendar', $course['is_standard_calendar']);
                    $json->where('steps', $course['steps']);
                });
            }
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route($this->route, ['institution' => 'Instituição', 'school' => 'Escola', 'standard_calendar' => '2', 'course' => 'Curso']));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
