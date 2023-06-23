<?php

namespace Database\Factories;

use App\Models\LegacyEducationLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEducationLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEducationLevel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_nivel' => $this->faker->firstName(),
            'descricao' => $this->faker->paragraph(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }

    public function current(): LegacyEducationLevel
    {
        return LegacyEducationLevel::query()->first() ?? $this->create([
            'nm_nivel' => 'Nível de Ensino Padrão',
            'descricao' => 'Nível de Ensino Padrão',
        ]);
    }
}
