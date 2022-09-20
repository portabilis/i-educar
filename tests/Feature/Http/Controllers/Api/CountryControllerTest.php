<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Country;
use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Tests\ResourceTestCase;

class CountryControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/country';
    protected string $model = Country::class;
    protected string $factory = CountryFactory::class;

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

    public function testFailUpdateCountry()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->createCountryIntoBrasil();
        $updatedModel = $this->newFactory()->make();
        $response = $this->patch(
            $this->getUri([$model->getKey()]), $updatedModel->toArray()
        );
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testFailCreateCountry()
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());
        $model = $this->makeCountryIntoBrasil();
        $response = $this->post(
            $this->getUri(), $model->toArray()
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
        $response->assertJson(['message' => 'Não é permitido exclusão de países brasileiros, pois já estão previamente cadastrados.']);
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
