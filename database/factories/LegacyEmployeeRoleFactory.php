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
     */
    public function definition(): array
    {
        return [
            'matricula' => (string) $this->faker->randomDigitNotNull(),
            'ref_cod_funcao' => fn () => LegacyRoleFactory::new()->current(),
            'ref_cod_servidor' => EmployeeFactory::new()->current(),
            'ref_ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }
}
