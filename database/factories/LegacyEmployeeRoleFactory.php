<?php

namespace Database\Factories;

use App\Models\LegacyEmployeeRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEmployeeRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEmployeeRole::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'matricula' => $this->faker->randomDigitNotNull(),
            'ref_cod_funcao' => fn () => LegacyRoleFactory::new()->create(),
            'ref_cod_servidor' => EmployeeFactory::new()->create(),
            'ref_ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }
}
