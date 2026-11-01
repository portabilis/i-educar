<?php

use App\Models\Country;
use App\Models\State;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\POST;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/state';
    $this->model = State::class;
    $this->factory = StateFactory::class;

    $this->createStateIntoBrasil = function (): State {
        $country = (new CountryFactory)->create(['id' => Country::BRASIL]);

        return (new StateFactory)->create(['country_id' => $country]);
    };

    $this->makeStateIntoBrasil = function (): State {
        $country = (new CountryFactory)->create(['id' => Country::BRASIL]);

        return (new StateFactory)->make(['country_id' => $country]);
    };
});

test('index', #[
    GET('/api/state', ['State'], 'Get all states'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'State')
] function () {
    $this->index();
});

test('store', #[
    POST('/api/state', ['State'], 'Create a state'),
    Property(Type::INT, 'country_id', 'ID of the country', 1),
    Property(Type::STRING, 'name', 'Name of the State', 'Paraná'),
    Property(Type::STRING, 'abbreviation', 'Abbreviation of the State', 'PR'),
    Property(Type::INT, 'ibge_code', 'IBGE code of the state', 12345),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'State')
] function () {
    $this->store();
});

test('show', #[
    GET('/api/state/{id}', ['State'], 'Get state with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, ref: 'State')
] function () {
    $this->show();
});

test('update', #[
    PUT('/api/state/{id}', ['State'], 'Update state with ID'),
    Property(Type::INT, 'country_id', 'ID of the country', 1),
    Property(Type::STRING, 'name', 'Name of the State', 'Paraná'),
    Property(Type::STRING, 'abbreviation', 'Abbreviation of the State', 'PR'),
    Property(Type::INT, 'ibge_code', 'IBGE code of the state', 12345),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'State')
] function () {
    $this->update();
});

test('delete', #[
    DELETE('/api/state/{id}', ['State'], 'Delete state with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'State')
] function () {
    $this->destroy();
});

test('fail update state', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());
    $model = ($this->createStateIntoBrasil)();
    $updatedModel = $this->newFactory()->make();
    $response = $this->patch(
        $this->getUri([$model->getKey()]),
        $updatedModel->toArray()
    );
    $response->assertStatus(403);
    $response->assertJson(['message' => 'This action is unauthorized.']);
});

test('fail create state', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());
    $model = ($this->makeStateIntoBrasil)();
    $response = $this->post(
        $this->getUri(),
        $model->toArray()
    );
    $response->assertStatus(403);
    $response->assertJson(['message' => 'This action is unauthorized.']);
});

test('fail destroy state', function () {
    $user = LegacyUserFactory::new()->institutional()->withAccess(754)->create();
    $this->actingAs($user);
    $model = ($this->createStateIntoBrasil)();
    $response = $this->delete(
        $this->getUri([$model->getKey()])
    );
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Não é permitido exclusão de estados brasileiros, pois já estão previamente cadastrados.']);
    $this->assertCount(1, $response->json('errors'));
});
