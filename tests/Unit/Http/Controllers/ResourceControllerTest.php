<?php

use App\Http\Controllers\ResourceController;
use App\Models\City;
use App\Models\State;
use Database\Factories\CityFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Controller de recursos sem processo, para não depender de autorização.
function controllerDeRecursos(): ResourceController
{
    return new class extends ResourceController {};
}

test('lista só as colunas pedidas, carrega relações com colunas e ordena', function () {
    $city = CityFactory::new()->create(['name' => 'Cidade de Teste']);

    $cities = controllerDeRecursos()->all(new City, Request::create('/', 'GET', [
        'only' => 'name, state_id',
        'include' => 'state:name|state:name',
        'order' => 'name,desc|id',
        'show' => 100,
    ]))->resource;

    $found = $cities->firstWhere('id', $city->getKey());

    expect($found?->name)->toBe('Cidade de Teste')
        ->and($found->state->getAttributes())->toHaveKeys(['id', 'name'])
        ->and($found->getAttributes())->not->toHaveKey('ibge_code');

    $state = controllerDeRecursos()->get($city->state_id, Request::create('/', 'GET', ['include' => 'cities:name|country']), State::class)->resource;

    expect($state->cities->pluck('name'))->toContain('Cidade de Teste')
        ->and($state->relationLoaded('country'))->toBeTrue();
});

test('o put altera o recurso como o patch', function () {
    $city = CityFactory::new()->create();

    controllerDeRecursos()->put($city, Request::create('/', 'PUT', ['name' => 'Novo nome']));

    expect($city->refresh()->name)->toBe('Novo nome');
});
