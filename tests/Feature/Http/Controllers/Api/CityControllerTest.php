<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\City;
use App\Models\Country;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use Tests\ResourceTestCase;

class CityControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/city';
    protected string $model = City::class;
    protected string $factory = CityFactory::class;

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

    public function testFailUpdateCity()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $model = $this->createCityIntoBrasil();

        $updatedModel = $this->newFactory()->make();

        $response = $this->patch(
            $this->getUri([$model->getKey()]), $updatedModel->toArray()
        );

        $response->assertStatus(403);

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailCreateCity()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $model = $this->makeCityIntoBrasil();

        $response = $this->post(
            $this->getUri(), $model->toArray()
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
            'message' => 'Não é permitido edição de municípios brasileiros, pois já estão previamente cadastrados.'
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
