<?php

namespace Database\Factories;

use App\Models\LegacyRegimeType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRegimeTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyRegimeType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_tipo' => $this->faker->firstName(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }

    public function current(): LegacyRegimeType
    {
        return LegacyRegimeType::query()->first() ?? $this->create([
            'nm_tipo' => 'Tipo de Regime Padrão',
        ]);
    }
}
