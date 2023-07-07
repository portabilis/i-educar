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

    /** @test */
    public function getNameAttribute(): void
    {
        $this->assertEquals($this->model->name, $this->model->fantasia);
    }
}
