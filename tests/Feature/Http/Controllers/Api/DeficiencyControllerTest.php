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

    public function test_index(): void
    {
        $this->index();
    }

    public function test_store(): void
    {
        $this->store();
    }

    public function test_show(): void
    {
        $this->show();
    }

    public function test_update(): void
    {
        $this->update();
    }

    public function test_delete(): void
    {
        $this->destroy();
    }
}
