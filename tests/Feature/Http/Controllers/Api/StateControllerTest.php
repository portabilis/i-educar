<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\State;
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
}
