<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineExemption;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineExemptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineExemption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(),
            'ref_cod_disciplina' => LegacyDisciplineFactory::new()->create(),
            'ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'ref_cod_serie' => LegacyLevelFactory::new()->create(),
            'ref_cod_tipo_dispensa' => LegacyExemptionTypeFactory::new()->create(),
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
        ];
    }
}
