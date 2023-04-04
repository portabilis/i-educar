<?php

namespace Database\Factories;

use App\Models\LegacyIndividual;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyIndividualFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyIndividual::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'idpes' => static fn () => LegacyPersonFactory::new()->create(),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
            'idpes_mae' => static fn () => LegacyPersonFactory::new()->create(),
            'idpes_pai' => static fn () => LegacyPersonFactory::new()->create(),
            'idpes_responsavel' => static fn () =>LegacyPersonFactory::new()->create(),
            'zona_localizacao_censo' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function father(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'idpes_pai' => LegacyIndividualFactory::new()->create(),
            ];
        });
    }

    public function mother(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'idpes_mae' => LegacyIndividualFactory::new()->create(),
            ];
        });
    }
    public function guardian(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'idpes_responsavel' => LegacyIndividualFactory::new()->create(),
            ];
        });
    }
}
