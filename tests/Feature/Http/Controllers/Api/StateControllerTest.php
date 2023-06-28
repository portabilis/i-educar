<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Country;
use App\Models\State;
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
class StateControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/state';

    protected string $model = State::class;

    protected string $factory = StateFactory::class;

    #[
        GET('/api/state', ['State'], 'Get all states'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'State')
    ]
    public function testIndex(): void
    {
        $this->index();
    }

    #[
        POST('/api/state', ['State'], 'Create a state'),
        Property(Type::INT, 'country_id', 'ID of the country', 1),
        Property(Type::STRING, 'name', 'Name of the State', 'Paraná'),
        Property(Type::STRING, 'abbreviation', 'Abbreviation of the State', 'PR'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the state', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'State'),
    ]
    public function testStore(): void
    {
        $this->store();
    }

    #[
        GET('/api/state/{id}', ['State'], 'Get state with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: 'State')
    ]
    public function testShow(): void
    {
        $this->show();
    }

    #[
        PUT('/api/state/{id}', ['State'], 'Update state with ID'),
        Property(Type::INT, 'country_id', 'ID of the country', 1),
        Property(Type::STRING, 'name', 'Name of the State', 'Paraná'),
        Property(Type::STRING, 'abbreviation', 'Abbreviation of the State', 'PR'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the state', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'State'),
    ]
    public function testUpdate(): void
    {
        $this->update();
    }

    #[
        DELETE('/api/state/{id}', ['State'], 'Delete state with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'State'),
    ]
    public function testDelete(): void
    {
        $this->destroy();
    }

    public function testFailUpdateState()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->createStateIntoBrasil();
        $updatedModel = $this->newFactory()->make();
        $response = $this->patch(
            $this->getUri([$model->getKey()]),
            $updatedModel->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailCreateState()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->makeStateIntoBrasil();
        $response = $this->post(
            $this->getUri(),
            $model->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailDestroyState()
    {
        $user = LegacyUserFactory::new()->institutional()->withAccess(754)->create();
        $this->actingAs($user);
        $model = $this->createStateIntoBrasil();
        $response = $this->delete(
            $this->getUri([$model->getKey()])
        );
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Não é permitido exclusão de estados brasileiros, pois já estão previamente cadastrados.']);
        $this->assertCount(1, $response->json('errors'));
    }

    private function createStateIntoBrasil(): State
    {
        $country = (new CountryFactory())->create(['id' => Country::BRASIL]);

        return (new StateFactory())->create(['country_id' => $country]);
    }

    private function makeStateIntoBrasil(): State
    {
        $country = (new CountryFactory())->create(['id' => Country::BRASIL]);

        return (new StateFactory())->make(['country_id' => $country]);
    }
}
