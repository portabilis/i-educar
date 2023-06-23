<?php

namespace Tests\Feature\Api\Resource\SchoolAcademicYear;

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceAcademicYearTest extends TestCase
{
    use DatabaseTransactions;

    private LegacySchool $school;

    private int $year;

    private string $route = 'api.resource.school-academic-year';

    protected function setUp(): void
    {
        parent::setUp();
        //escolas
        $schools = LegacySchoolFactory::new()->count(2)->create();
        $schools->each(function ($school) {
            LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->year]);
            LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->subYear()->year]);
            LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->subYears(2)->year]);
            LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->subYears(3)->year]);
        });
        //escola
        $this->school = $schools->first();
        //ano
        $this->year = $this->school->academicYears()->skip(1)->orderByYear()->first()->year;
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route($this->route, ['school' => $this->school, 'year_gte' => $this->year, 'limit' => 2]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'year',
                ],
            ],
        ]);

        $academic_years = LegacySchoolAcademicYear::getResource([
            'school' => $this->school->id,
            'year_gte' => $this->year,
        ]);

        $response->assertJson(function (AssertableJson $json) use ($academic_years) {
            $json->has('data', 2);

            foreach ($academic_years as $key => $academic_year) {
                $json->has('data.'.$key, function ($json) use ($academic_year) {
                    $json->where('year', $academic_year['year']);
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
        $response = $this->getJson(route($this->route, ['school' => 'Escola', 'year_gte' => '202', 'limit' => 'Limite']));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
