<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Employee;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $person = LegacyPersonFactory::new()->create();
        LegacyIndividualFactory::new()->create([
            'idpes' => $person->idpes,
            'ativo' => 1,
        ]);
        \Database\Factories\EmployeeFactory::new()->create([
            'cod_servidor' => $person,
        ]);
        $instance = new $this->model();

        return $instance->query()->find($person->id)->getAttributes();
    }
}
