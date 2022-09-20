<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Country;
use App\Models\State;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use Tests\ResourceTestCase;

class StateControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/state';
    protected string $model = State::class;
    protected string $factory = StateFactory::class;

    public function testIndex(): void
    {
        $this->index();
    }

    public function testStore(): void
    {
        $this->store();
    }

    public function testShow(): void
    {
        $this->show();
    }

    public function testUpdate(): void
    {
        $this->update();
    }

    public function testDelete(): void
    {
        $this->destroy();
    }

    public function testeFailUpdateState()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->createStateIntoBrasil();
        $updatedModel = $this->newFactory()->make();
        $response = $this->patch(
            $this->getUri([$model->getKey()]), $updatedModel->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testeFailCreateState()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->makeStateIntoBrasil();
        $response = $this->post(
            $this->getUri(), $model->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testeFailDestroyState()
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
