<?php

namespace Database\Factories;

use App\Models\SchoolClassInep;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassInepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SchoolClassInep::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'cod_turma' => fn () => LegacySchoolClassFactory::new()->create(),
            'cod_turma_inep' => $this->faker->numerify('########'),
        ];
    }
}
