<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Person;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $person = LegacyPersonFactory::new()->create();
        LegacyIndividualFactory::new()->create([
            'idpes' => $person->idpes,
            'ativo' => 1,
        ]);

        $instance = new $this->model();

        return $instance->query()->find($person->id)->getAttributes();
    }
}
