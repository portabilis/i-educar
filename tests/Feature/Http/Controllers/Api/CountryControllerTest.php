<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Country;
use Database\Factories\CountryFactory;
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
}
