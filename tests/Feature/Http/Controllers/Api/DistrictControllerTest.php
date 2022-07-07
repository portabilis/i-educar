<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Country;
use App\Models\District;
use Database\Factories\LegacyUserFactory;
use Tests\ResourceTestCase;
use Database\Factories\CityFactory;
use Database\Factories\StateFactory;
use Database\Factories\CountryFactory;
use Database\Factories\DistrictFactory;

class DistrictControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/district';
    protected string $model = District::class;
    protected string $factory = DistrictFactory::class;

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

    public function testeFailUpdateDistrict()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $model = $this->createDistrictIntoBrasil();

        $updatedModel = $this->newFactory()->make();

        $response = $this->patch(
            $this->getUri([$model->getKey()]), $updatedModel->toArray()
        );

        $response->assertStatus(403);

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testeFailCreateDistrict()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $model = $this->createDistrictIntoBrasil();

        $response = $this->post(
            $this->getUri(), $model->toArray()
        );

        $response->assertStatus(403);

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testeFailDestroyDistrict()
    {
        $user = LegacyUserFactory::new()->institutional()->withAccess(759)->create();
        $this->actingAs($user);

        $model = $this->createDistrictIntoBrasil();

        $response = $this->delete(
            $this->getUri([$model->getKey()])
        );

        $response->assertStatus(422);

        $response->assertJson(['message' => 'Não é permitido exclusão de distritos brasileiros, pois já estão previamente cadastrados.']);

        $this->assertCount(1, $response->json('errors') );
    }

    private function createDistrictIntoBrasil(): District
    {
        $country = (new CountryFactory())->create(['id' => Country::BRASIL]);
        $state = (new StateFactory())->create(['country_id' => $country]);
        $city = (new CityFactory())->create(['state_id' => $state]);

        return (new DistrictFactory())->createOne(['city_id' => $city]);
    }
}
