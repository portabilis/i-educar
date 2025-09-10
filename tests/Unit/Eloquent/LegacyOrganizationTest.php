<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use Tests\EloquentTestCase;

class LegacyOrganizationTest extends EloquentTestCase
{
    protected $relations = [
        'person' => LegacyPerson::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyOrganization::class;
    }

    public function test_get_name_attribute(): void
    {
        $this->assertEquals($this->model->name, $this->model->fantasia);
    }
}
