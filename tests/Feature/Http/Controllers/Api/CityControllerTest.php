<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\City;
use App\Models\Country;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use OpenApiGenerator\Attributes\Controller;
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

#[Controller]
class CityControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/city';

    protected string $model = City::class;

    protected string $factory = CityFactory::class;

    #[
        GET('/api/city', ['City'], 'Get all cities'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'City')
    ]
    public function testIndex(): void
    {
        $this->index();
    }

    #[
        POST('/api/city', ['City'], 'Create a city'),
        Property(Type::INT, 'state_id', 'ID of the state', 1),
        Property(Type::STRING, 'name', 'Name of the City', 'Francisco Beltrão'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the city', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'City'),
    ]
    public function testStore(): void
    {
        $this->store();
    }

    #[
        GET('/api/city/{id}', ['City'], 'Get city with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: 'City')
    ]
    public function testShow(): void
    {
        $this->show();
    }

    #[
        PUT('/api/city/{id}', ['City'], 'Update city with ID'),
        Property(Type::INT, 'state_id', 'ID of the state', 1),
        Property(Type::STRING, 'name', 'Name of the City', 'Francisco Beltrão'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the city', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'City'),
    ]
    public function testUpdate(): void
    {
        $this->update();
    }

    #[
        DELETE('/api/city/{id}', ['City'], 'Delete city with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'City'),
    ]
    public function testDelete(): void
    {
        $this->destroy();
    }

    public function testFailUpdateCity()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $model = $this->createCityIntoBrasil();

        $updatedModel = $this->newFactory()->make();

        $response = $this->patch(
            $this->getUri([$model->getKey()]),
            $updatedModel->toArray()
        );

        $response->assertStatus(403);

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailCreateCity()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $model = $this->makeCityIntoBrasil();

        $response = $this->post(
            $this->getUri(),
            $model->toArray()
        );

        $response->assertStatus(403);

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailDestroyCity()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(755)->create());

        $model = $this->createCityIntoBrasil();

        $response = $this->delete(
            $this->getUri([$model->getKey()])
        );

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'Não é permitido edição de municípios brasileiros, pois já estão previamente cadastrados.',
        ]);

        $this->assertCount(1, $response->json('errors'));
    }

    private function createCityIntoBrasil(): City
    {
        $country = (new CountryFactory())->create(['id' => Country::BRASIL]);
        $state = (new StateFactory())->create(['country_id' => $country]);

        return (new CityFactory())->createOne(['state_id' => $state]);
    }

    private function makeCityIntoBrasil(): City
    {
        $country = (new CountryFactory())->create(['id' => Country::BRASIL]);
        $state = (new StateFactory())->create(['country_id' => $country]);

        return (new CityFactory())->makeOne(['state_id' => $state]);
    }
}
