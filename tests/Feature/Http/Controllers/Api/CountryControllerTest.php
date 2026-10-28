<?php

use App\Models\Country;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
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
    $this->uri = '/api/country';
    $this->model = Country::class;
    $this->factory = CountryFactory::class;

    $this->createCountryIntoBrasil = function (): Country {
        return (new CountryFactory)->create(['id' => Country::BRASIL]);
    };

    $this->makeCountryIntoBrasil = function (): Country {
        return (new CountryFactory)->make(['id' => Country::BRASIL]);
    };
});

test('index', #[
    GET('/api/country', ['Country'], 'Get all countries'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Country')
] function () {
    $this->index();
});

test('store', #[
    POST('/api/country', ['Country'], 'Create a country'),
    Property(Type::STRING, 'name', 'Name of the country', 'Brasil'),
    Property(Type::INT, 'ibge_code', 'IBGE code of the country', 12345),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Country')
] function () {
    $this->store();
});

test('show', #[
    GET('/api/country/{id}', ['Country'], 'Get country with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, ref: 'Country')
] function () {
    $this->show();
});

test('update', #[
    PUT('/api/country/{id}', ['Country'], 'Update country with ID'),
    Property(Type::STRING, 'name', 'Name of the country', 'Brasil'),
    Property(Type::INT, 'ibge_code', 'IBGE code of the country', 12345),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Country')
] function () {
    $this->update();
});

test('delete', #[
    DELETE('/api/country/{id}', ['Country'], 'Delete country with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Country')
] function () {
    $this->destroy();
});

test('fail update country', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());
    $model = ($this->createCountryIntoBrasil)();
    $updatedModel = $this->newFactory()->make();
    $response = $this->patch(
        $this->getUri([$model->getKey()]),
        $updatedModel->toArray()
    );
    $response->assertStatus(403);
    $response->assertJson(['message' => 'This action is unauthorized.']);
});

test('fail create country', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());
    $model = ($this->makeCountryIntoBrasil)();
    $response = $this->post(
        $this->getUri(),
        $model->toArray()
    );
    $response->assertStatus(403);
    $response->assertJson(['message' => 'This action is unauthorized.']);
});

test('fail destroy country', function () {
    $user = LegacyUserFactory::new()->institutional()->withAccess(753)->create();
    $this->actingAs($user);
    $model = ($this->createCountryIntoBrasil)();
    $response = $this->delete(
        $this->getUri([$model->getKey()])
    );
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Não é permitido exclusão do Brasil, pois já está previamente cadastrado.']);
    $this->assertCount(1, $response->json('errors'));
});
