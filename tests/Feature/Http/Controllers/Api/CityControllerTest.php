<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\City;
use Database\Factories\CityFactory;
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
}
