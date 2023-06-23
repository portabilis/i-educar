<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\LegacyIndividual;
use App\Models\LegacySchoolingDegree;
use Tests\EloquentTestCase;

class LegacySchoolingDegreeTest extends EloquentTestCase
{
    protected $relations = [
        'employees' => Employee::class,
        'individuals' => LegacyIndividual::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolingDegree::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'idesco',
            'description' => 'descricao',
            'schooling' => 'escolaridade',
        ];
    }
}
