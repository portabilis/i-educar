<?php

namespace Tests;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use Database\Factories\LegacyUserFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Server;

#[Info('API i-EDUCAR', '1.0.0', 'Documentação de acesso a nossa API', 'API para Integração ao i-EDUCAR')]
#[Server('https://ieducar.com.br', 'Endereço do servidor da Aplicação')]
class ResourceTestCase extends TestCase
{
    use DatabaseTransactions;

    protected string $uri = '/';

    protected string $model;

    protected string $factory;

    protected int $indexFactoryCount = 2;

    protected int $indexJsonCount = 1;

    protected int $showFactoryCount = 1;

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

        $response = $this->get($this->getUri(
            query: [
                'show' => $this->showFactoryCount,
            ]
        ));

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

        if (in_array(SoftDeletes::class, class_uses($model), true) || in_array(LegacySoftDeletes::class, class_uses($model), true)) {
            $this->assertSoftDeleted($this->model, deletedAtColumn: $model->getDeletedAtColumn());
        } else {
            $this->assertDatabaseMissing($this->model, $model->getAttributes());
        }

        return $response;
    }
}
