<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacyDeficiency;
use Database\Factories\LegacyDeficiencyFactory;
use Tests\ResourceTestCase;

class DeficiencyControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/person/deficiency';

    protected string $model = LegacyDeficiency::class;

    protected string $factory = LegacyDeficiencyFactory::class;

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
