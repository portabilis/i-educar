<?php

use App\Models\Country;
use App\Models\District;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\DistrictFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/district';
    $this->model = District::class;
    $this->factory = DistrictFactory::class;

    $this->createDistrictIntoBrasil = function (): District {
        $country = (new CountryFactory)->create(['id' => Country::BRASIL]);
        $state = (new StateFactory)->create(['country_id' => $country]);
        $city = (new CityFactory)->create(['state_id' => $state]);

        return (new DistrictFactory)->createOne(['city_id' => $city]);
    };

    $this->makeDistrictIntoBrasil = function (): District {
        $country = (new CountryFactory)->create(['id' => Country::BRASIL]);
        $state = (new StateFactory)->create(['country_id' => $country]);
        $city = (new CityFactory)->create(['state_id' => $state]);

        return (new DistrictFactory)->makeOne(['city_id' => $city]);
    };
});

test('index', #[
    GET('/api/district', ['District'], 'Get all districts'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'District')
] function () {
    $this->index();
});

test('store', #[
    POST('/api/district', ['District'], 'Create a District'),
    Property(Type::INT, 'city_id', 'ID of the city', 1),
    Property(Type::STRING, 'name', 'Name of the District', 'São Miguel'),
    Property(Type::INT, 'ibge_code', 'IBGE code of the district', 12345),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'District')
] function () {
    $this->store();
});

test('show', #[
    GET('/api/district/{id}', ['District'], 'Get district with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, ref: 'District')
] function () {
    $this->show();
});

test('update', #[
    PUT('/api/district/{id}', ['District'], 'Update district with ID'),
    Property(Type::INT, 'city_id', 'ID of the city', 1),
    Property(Type::STRING, 'name', 'Name of the District', 'São Miguel'),
    Property(Type::INT, 'ibge_code', 'IBGE code of the district', 12345),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'District')
] function () {
    $this->update();
});

test('delete', #[
    DELETE('/api/district/{id}', ['District'], 'Delete district with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'District')
] function () {
    $this->destroy();
});

test('fail update district', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());

    $model = ($this->createDistrictIntoBrasil)();

    $updatedModel = $this->newFactory()->make();

    $response = $this->patch(
        $this->getUri([$model->getKey()]),
        $updatedModel->toArray()
    );

    $response->assertStatus(403);

    $response->assertJson(['message' => 'This action is unauthorized.']);
});

test('fail create district', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());

    $model = ($this->makeDistrictIntoBrasil)();

    $response = $this->post(
        $this->getUri(),
        $model->toArray()
    );

    $response->assertStatus(403);

    $response->assertJson(['message' => 'This action is unauthorized.']);
});

test('fail destroy district', function () {
    $user = LegacyUserFactory::new()->institutional()->withAccess(759)->create();
    $this->actingAs($user);

    $model = ($this->createDistrictIntoBrasil)();

    $response = $this->delete(
        $this->getUri([$model->getKey()])
    );

    $response->assertStatus(422);

    $response->assertJson(['message' => 'Não é permitido exclusão de distritos brasileiros, pois já estão previamente cadastrados.']);

    $this->assertCount(1, $response->json('errors'));
});
