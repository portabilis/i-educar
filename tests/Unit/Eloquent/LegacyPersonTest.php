<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Tests\EloquentTestCase;

class LegacyPersonTest extends EloquentTestCase
{
    private LegacyPerson $person;

    protected $relations = [
        'phone' => LegacyPhone::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyPerson::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->person = $this->createNewModel();
    }

    /** @test */
    public function getIdAttribute()
    {
        $this->assertEquals($this->person->id, $this->person->idpes);
    }
}
