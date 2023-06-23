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
use Tests\TestCase;

class ResourceGradeTest extends TestCase
{
    use DatabaseTransactions;

    private LegacyCourse $course;

    private Collection $schools;

    private LegacyGrade $grade;

    private LegacySchool $school;

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
                    'ref_cod_escola' => $school->id,
                ]);
            });
        });

        //serie
        $this->grade = $this->course->grades()->first();
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(
            route(
                $this->route,
                [
                    'course' => $this->course->id,
                    'school' => $this->school,
                ]
            )
        );

        $grades = LegacyGrade::getResource([
            'course' => $this->course->id,
            'school' => $this->school->id,
        ]);

        $response->assertSuccessful()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        foreach ($grades as $key => $grade) {
            $response->assertJsonFragment([
                'id' => $grade['id'],
                'name' => $grade['name'],
            ]);
        }
    }

    public function test_parametes_exclude_exact_json_match(): void
    {
        $grade_exclude_id = $this->grade->id;
        $school_exclude_id = $this->school->id;

        $grades = LegacyGrade::getResource([
            'course' => $this->course->id,
            'gradeExclude' => $grade_exclude_id,
            'schoolExclude' => $school_exclude_id,
        ]);

        $response = $this->getJson(
            route(
                $this->route,
                [
                    'course' => $this->course->id,
                    'grade_exclude' => $grade_exclude_id,
                    'school_exclude' => $school_exclude_id,
                ]
            )
        );

        $response->assertSuccessful()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        foreach ($grades as $key => $grade) {
            $response->assertJsonFragment([
                'id' => $grade['id'],
                'name' => $grade['name'],
            ]);
        }
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route($this->route));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('api.resource.grade', [
            'course' => 'Curso',
            'school' => 'Escola',
            'grade_exclude' => 'Serie',
            'school_exclude' => 'Escola',
        ]));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
