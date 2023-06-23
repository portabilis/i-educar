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
     */
    public function definition(): array
    {
        return [
            'ref_cod_matricula' => fn () => LegacyRegistrationFactory::new()->create(),
            'ref_cod_disciplina' => fn () => LegacyDisciplineFactory::new()->create(),
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ref_cod_serie' => fn () => LegacyGradeFactory::new()->create(),
            'ref_cod_tipo_dispensa' => fn () => LegacyExemptionTypeFactory::new()->create(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'data_cadastro' => now(),
            'ativo' => 1,
        ];
    }
}
