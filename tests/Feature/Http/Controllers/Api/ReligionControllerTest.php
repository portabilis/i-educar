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
