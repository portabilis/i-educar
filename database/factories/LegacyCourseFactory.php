<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
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
            'modalidade_curso' => ModalidadeCurso::ENSINO_REGULAR,
        ];
    }

    public function withName(string $name): static
    {
        return $this->state([
            'nm_curso' => $name,
            'descricao' => $name,
        ]);
    }

    public function withElementarySchool(): static
    {
        $age = 6;
        $total = 9;

        return $this->afterCreating(function (LegacyCourse $course) use ($age, $total) {
            for ($year = 1; $year <= $total; $year++) {
                LegacyGradeFactory::new()->create([
                    'ref_cod_curso' => $course,
                    'nm_serie' => $year . 'º ano',
                    'descricao' => $year . 'º ano',
                    'etapa_curso' => $year,
                    'idade_inicial' => $age++,
                    'idade_final' => $age,
                    'concluinte' => $total === $year ? 2 : 1,
                    'dias_letivos' => 200,
                    'carga_horaria' => 800,
                ]);
            }
        })->state([
            'qtd_etapas' => $total,
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
