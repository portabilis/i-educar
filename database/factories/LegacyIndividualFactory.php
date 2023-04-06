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
            'idpes' => fn () => LegacyPersonFactory::new()->create(),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
            'idpes_mae' => fn () => LegacyPersonFactory::new()->create(),
            'idpes_pai' => fn () => LegacyPersonFactory::new()->create(),
            'idpes_responsavel' => fn () =>LegacyPersonFactory::new()->create(),
            'zona_localizacao_censo' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function current(): LegacyIndividual
    {
        return LegacyIndividual::query()->first() ?? $this->create([
            'idpes' => fn () => LegacyPersonFactory::new()->current(),
        ]);
    }

    public function father(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'idpes_pai' => fn () => LegacyIndividualFactory::new()->create(),
            ];
        });
    }

    public function mother(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'idpes_mae' => fn () => LegacyIndividualFactory::new()->create(),
            ];
        });
    }
    public function guardian(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'idpes_responsavel' => fn () => LegacyIndividualFactory::new()->create(),
            ];
        });
    }
}
