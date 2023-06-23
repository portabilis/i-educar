<?php

namespace Database\Factories;

use App\Models\LegacyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDocument::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'idpes' => fn () => LegacyUserFactory::new()->current(),
            'rg' => $this->faker->randomNumber(8),
            'certidao_nascimento' => $this->faker->randomNumber(8),
            'origem_gravacao' => 'M',
        ];
    }
}
