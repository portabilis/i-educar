<?php

use App\Models\LegacySchoolAcademicYear;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->route = 'api.resource.school-academic-year';

    // escolas
    $schools = LegacySchoolFactory::new()->count(2)->create();
    $schools->each(function ($school) {
        LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->year]);
        LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->subYear()->year]);
        LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->subYears(2)->year]);
        LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school->cod_escola, 'ano' => now()->subYears(3)->year]);
    });
    // escola
    $this->school = $schools->first();
    // ano
    $this->year = $this->school->academicYears()->skip(1)->orderByYear()->first()->year;
});

test('exact json match', function () {
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
});

test('required parameters', function () {
    $response = $this->getJson(route($this->route));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

test('invalid parameters', function () {
    $response = $this->getJson(route($this->route, ['school' => 'Escola', 'year_gte' => '202', 'limit' => 'Limite']));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});
