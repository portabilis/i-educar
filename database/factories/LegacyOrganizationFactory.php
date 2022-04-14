<?php

namespace Database\Factories;

use App\Models\LegacyOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyOrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyOrganization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $person = LegacyPersonFactory::new()->create([
            'nome' => $this->faker->company,
        ]);

        return [
            'idpes' => $person,
            'cnpj' => $this->faker->numerify('##############'),
            'insc_estadual' => $this->faker->numerify('########'),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
            'idpes_cad' => LegacyUserFactory::new()->unique()->make(),
            'data_cad' => now(),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
            'fantasia' => $person->name,
        ];
    }
}
