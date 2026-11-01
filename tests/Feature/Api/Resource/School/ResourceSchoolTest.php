<?php

use App\Models\LegacySchool;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->route = 'api.resource.school';

    // instituição
    $this->institution = LegacyInstitutionFactory::new()->create();

    // escolas
    $schools = LegacySchoolFactory::new()->count(2)->create([
        'ref_cod_instituicao' => $this->institution->id,
    ]);

    // escola
    $this->school = $schools->first();
});

test('exact json match', function () {
    $response = $this->getJson(route($this->route, ['institution' => $this->institution]));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
            ],
        ],
    ]);

    $schools = LegacySchool::getResource([
        'institution' => $this->institution->id,
    ]);

    $response->assertJson(function (AssertableJson $json) use ($schools) {
        $json->has('data', 2);

        foreach ($schools as $key => $school) {
            $json->has('data.'.$key, function ($json) use ($school) {
                $json->where('id', $school['id']);
                $json->where('name', $school['name']);
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
    $response = $this->getJson(route($this->route, ['institution' => 'Instituição']));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});
