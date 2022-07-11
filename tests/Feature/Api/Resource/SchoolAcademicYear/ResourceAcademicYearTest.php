<?php

namespace Tests\Feature\Api\Resource\SchoolAcademicYear;

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceAcademicYearTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacySchool
     */
    private LegacySchool $school;

    /**
     * @var int
     */
    private int $year;

    protected function setUp(): void
    {
        parent::setUp();
        //escolas
        $schools = LegacySchool::factory(2)->hasAcademicYears(4)->create();
        //escola
        $this->school = $schools->first();
        //ano
        $this->year = $this->school->academicYears()->skip(1)->orderByYear()->first()->year;
    }

    public function test_exact_json_match(): void
    {
        $limit = 1;

        $response = $this->getJson(route('resource::api.school-academic-year', ['school' => $this->school, 'year' => $this->year, 'limit' => 2]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'year'
            ]
        ]);

        $academic_years = LegacySchoolAcademicYear::select('ano as year')
            ->whereSchool($this->school->id)->whereGteYear($this->year)
            ->active()
            ->orderByYear()
            ->limit($limit)
            ->get();

        $response->assertJson(function (AssertableJson $json) use ($academic_years) {
            $json->has(2);

            foreach ($academic_years as $key => $academic_year) {
                $json->has($key, function ($json) use ($academic_year) {
                    $json->where('year', $academic_year->year);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.school-academic-year', ['school' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.school-academic-year'));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.school-academic-year', ['school' => 'Escola', 'year' => '202', 'limit' => 'Limite']));
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

}
