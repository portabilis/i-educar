<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('lista as turmas da instituição como recurso de id e nome', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Alfa', 'ano' => 2030]);

    $this->getJson(route('api.resource.school-class', ['institution' => $schoolClass->ref_cod_instituicao]))
        ->assertOk()
        ->assertJsonFragment(['id' => $schoolClass->getKey(), 'name' => 'Alfa (2030)']);
});

// Os recursos respondem a validação com a lista vazia, não com erro.
test('sem instituição, escola, série ou curso válidos a lista vem vazia', function () {
    LegacySchoolClassFactory::new()->create();

    $this->getJson(route('api.resource.school-class'))
        ->assertOk()
        ->assertExactJson(['data' => []]);

    $this->getJson(route('api.resource.school-class', ['school' => 0]))
        ->assertOk()
        ->assertExactJson(['data' => []]);
});
