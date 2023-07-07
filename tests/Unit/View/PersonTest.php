<?php

namespace Tests\Unit\View;

use App\Models\Individual;
use App\Models\Person;
use App\Models\PersonType;
use App\Models\Place;
use App\Models\RegistryOrigin;
use Tests\ViewTestCase;

class PersonTest extends ViewTestCase
{
    protected $relations = [
        'individual' => Individual::class,
        'createdBy' => Individual::class,
        'updatedBy' => Individual::class,
        'place' => Place::class,
    ];

    protected function getViewModelName(): string
    {
        return Person::class;
    }

    public function testTypeDescription(): void
    {
        $expected = (new PersonType())->getDescriptiveValues()[(int) $this->model->type];
        $this->assertEquals($expected, $this->model->typeDescription);
    }

    public function testRegistryOriginDescription(): void
    {
        $expected = (new RegistryOrigin())->getDescriptiveValues()[(int) $this->model->registry_origin];
        $this->assertEquals($expected, $this->model->registryOriginDescription);
    }
}
