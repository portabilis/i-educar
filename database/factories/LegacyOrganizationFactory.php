<?php

namespace Database\Factories;

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
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
        return [
            'idpes' => fn () => LegacyPersonFactory::new()->create([
                'nome' => $this->faker->company(),
            ]),
            'cnpj' => $this->faker->numerify('##############'),
            'insc_estadual' => $this->faker->numerify('########'),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
            'idpes_cad' => fn () => LegacyUserFactory::new()->current(),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
            'fantasia' => fn (array $attributes) => LegacyPerson::query()->find($attributes['idpes'])->nome,
        ];
    }
}
