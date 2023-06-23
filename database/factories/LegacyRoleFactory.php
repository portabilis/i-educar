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
     */
    public function definition(): array
    {
        return [
            'nm_funcao' => $this->faker->colorName,
            'abreviatura' => $this->faker->hexColor,
            'professor' => 1,
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }

    public function current(): LegacyRole
    {
        return LegacyRole::query()->first() ?? $this->create([
            'nm_funcao' => 'Professor',
            'abreviatura' => 'Prof',
        ]);
    }
}
