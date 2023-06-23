<?php

namespace Database\Factories;

use App\Models\EmployeeInep;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeInepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeInep::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'cod_servidor' => EmployeeFactory::new()->create(),
            'cod_docente_inep' => $this->faker->randomNumber(3),
            'nome_inep' => $this->faker->name(),
            'fonte' => 'U',
        ];
    }
}
