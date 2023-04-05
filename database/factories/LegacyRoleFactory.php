<?php

namespace Database\Factories;

use App\Models\LegacyRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyRole::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nm_funcao' => $this->faker->colorName,
            'abreviatura' => $this->faker->hexColor,
            'professor' => 1,
            'ref_usuario_cad' => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => LegacyUserFactory::new()->current(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }
}
