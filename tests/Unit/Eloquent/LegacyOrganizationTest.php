<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyOrganization;
use Tests\EloquentTestCase;

class LegacyOrganizationTest extends EloquentTestCase
{
    private LegacyOrganization $organization;

    protected function getEloquentModelName()
    {
        return LegacyOrganization::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->organization = $this->createNewModel();
    }

    /** @test */
    public function getNameAttribute()
    {
        $this->assertEquals($this->organization->name, $this->organization->fantasia);
    }
}
