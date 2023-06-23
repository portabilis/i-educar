<?php

namespace Database\Factories;

use App\Models\EducacensoInstitution;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducacensoInstitutionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EducacensoInstitution::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ies_id' => $this->faker->numberBetween(10000, 99999),
            'nome' => $this->faker->name(),
            'dependencia_administrativa_id' => 1,
            'tipo_instituicao_id' => $this->faker->numberBetween(10000, 99999),
            'uf' => 'SC',
            'user_id' => fn () => LegacyUserFactory::new()->current(),
            'created_at' => now(),
        ];
    }
}
