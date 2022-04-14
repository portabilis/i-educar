<?php

namespace Database\Factories;

use App\Models\LegacyStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStudent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_idpes' => LegacyIndividualFactory::new()->create(),
            'data_cadastro' => now(),
        ];
    }
}
