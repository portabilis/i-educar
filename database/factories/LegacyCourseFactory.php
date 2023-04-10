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
            'ref_cod_tipo_regime' => fn () => LegacyRegimeTypeFactory::new()->current(),
            'ref_cod_nivel_ensino' => fn () => LegacyEducationLevelFactory::new()->current(),
            'ref_cod_tipo_ensino' => fn () => LegacyEducationTypeFactory::new()->current(),
            'nm_curso' => $this->faker->words(3, true),
            'descricao' => $this->faker->words(3, true),
            'sgl_curso' => $this->faker->word,
            'qtd_etapas' => $this->faker->randomElement([2, 3, 4]),
            'carga_horaria' => 800,
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'hora_falta' => 0.75,
            'ativo' => 1,
        ];
    }

    public function withName(string $name): static
    {
        return $this->state([
            'nm_curso' => $name,
            'descricao' => $name,
        ]);
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
