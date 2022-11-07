<?php

namespace Tests;

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;

class ResourceTestCase extends TestCase
{
    use DatabaseTransactions;

    protected string $uri = '/';
    protected string $model;
    protected string $factory;

    protected int $indexFactoryCount = 20;
    protected int $indexJsonCount = 15;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(LegacyUserFactory::new()->admin()->create());
    }

    protected function getUri(array $params = [], array $query = []): string
    {
        $uri = rtrim($this->uri, '/') . '/' . implode('/', $params);
        $query = count($query) ? '?' . http_build_query($query) : '';

        return $uri . $query;
    }

    protected function newFactory()
    {
        return $this->app->get($this->factory);
    }

    public function index(): TestResponse
    {
        $response = $this->get($this->getUri());

        $response->assertOk();
        $response->assertJsonCount(0, 'data');

        $this->newFactory()->count($this->indexFactoryCount)->create();

        $response = $this->get($this->getUri());

        $response->assertOk();
        $response->assertJsonCount($this->indexJsonCount, 'data');

        $this->assertDatabaseCount($this->model, $this->indexFactoryCount);

        return $response;
    }

    public function store(): TestResponse
    {
        $model = $this->newFactory()->make();

        $response = $this->post(
            $this->getUri(),
            $model->toArray()
        );

        $response->assertCreated();
        $response->assertJson([
            'data' => $model->toArray(),
        ]);

        $this->assertDatabaseHas($this->model, $model->getAttributes());

        return $response;
    }

    public function show(): TestResponse
    {
        $model = $this->newFactory()->create();

        $response = $this->get(
            $this->getUri([$model->getKey()])
        );

        $response->assertOk();
        $response->assertJson([
            'data' => $model->toArray(),
        ]);

        return $response;
    }

    public function update(): TestResponse
    {
        $model = $this->newFactory()->create();
        $updatedModel = $this->newFactory()->make();

        $response = $this->patch(
            $this->getUri([$model->getKey()]),
            $updatedModel->toArray()
        );

        $response->assertOk();
        $response->assertJson([
            'data' => $updatedModel->toArray(),
        ]);

        $this->assertDatabaseMissing($this->model, $model->getAttributes());
        $this->assertDatabaseHas($this->model, $updatedModel->getAttributes());

        return $response;
    }

    public function destroy(): TestResponse
    {
        $model = $this->newFactory()->create();

        $response = $this->delete(
            $this->getUri([$model->getKey()])
        );

        $response->assertOk();
        $response->assertJson([
            'data' => $model->toArray(),
        ]);

        $this->assertDatabaseMissing($this->model, $model->getAttributes());

        return $response;
    }
}
