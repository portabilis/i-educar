<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyCourse::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => 1,
            'ref_cod_tipo_regime' => LegacyRegimeTypeFactory::new()->create(),
            'ref_cod_nivel_ensino' => LegacyEducationLevelFactory::new()->create(),
            'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->create(),
            'nm_curso' => $this->faker->words(3, true),
            'sgl_curso' => $this->faker->word,
            'qtd_etapas' => $this->faker->randomElement([2, 3, 4]),
            'carga_horaria' => 800,
            'data_cadastro' => now(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'hora_falta' => 0.75,
        ];
    }

    public function standardAcademicYear(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'padrao_ano_escolar' => 1,
            ]);
        });
    }
}
