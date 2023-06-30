<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Religion;
use Database\Factories\ReligionFactory;
use Tests\ResourceTestCase;

class ReligionControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/religion';

    protected string $model = Religion::class;

    protected string $factory = ReligionFactory::class;

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
