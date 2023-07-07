<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Country;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
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
class CountryControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/country';

    protected string $model = Country::class;

    protected string $factory = CountryFactory::class;

    #[
        GET('/api/country', ['Country'], 'Get all countries'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Country')
    ]
    public function testIndex(): void
    {
        $this->index();
    }

    #[
        POST('/api/country', ['Country'], 'Create a country'),
        Property(Type::STRING, 'name', 'Name of the country', 'Brasil'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the country', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Country'),
    ]
    public function testStore(): void
    {
        $this->store();
    }

    #[
        GET('/api/country/{id}', ['Country'], 'Get country with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: 'Country')
    ]
    public function testShow(): void
    {
        $this->show();
    }

    #[
        PUT('/api/country/{id}', ['Country'], 'Update country with ID'),
        Property(Type::STRING, 'name', 'Name of the country', 'Brasil'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the country', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Country'),
    ]
    public function testUpdate(): void
    {
        $this->update();
    }

    #[
        DELETE('/api/country/{id}', ['Country'], 'Delete country with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Country'),
    ]
    public function testDelete(): void
    {
        $this->destroy();
    }

    public function testFailUpdateCountry()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->createCountryIntoBrasil();
        $updatedModel = $this->newFactory()->make();
        $response = $this->patch(
            $this->getUri([$model->getKey()]),
            $updatedModel->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailCreateCountry()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->makeCountryIntoBrasil();
        $response = $this->post(
            $this->getUri(),
            $model->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailDestroyCountry()
    {
        $user = LegacyUserFactory::new()->institutional()->withAccess(753)->create();
        $this->actingAs($user);
        $model = $this->createCountryIntoBrasil();
        $response = $this->delete(
            $this->getUri([$model->getKey()])
        );
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Não é permitido exclusão do Brasil, pois já está previamente cadastrado.']);
        $this->assertCount(1, $response->json('errors'));
    }

    private function createCountryIntoBrasil(): Country
    {
        return (new CountryFactory())->create(['id' => Country::BRASIL]);
    }

    private function makeCountryIntoBrasil(): Country
    {
        return (new CountryFactory())->make(['id' => Country::BRASIL]);
    }
}
