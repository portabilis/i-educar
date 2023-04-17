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
                $to = LegacyGradeFactory::new()->create([
                    'ref_cod_curso' => $course,
                    'nm_serie' => $year . 'º ano',
                    'descricao' => $year . 'º ano',
                    'etapa_curso' => $year,
                    'idade_ideal' => $age,
                    'idade_inicial' => $age++,
                    'idade_final' => $age,
                    'concluinte' => $total === $year ? 2 : 1,
                    'dias_letivos' => 200,
                    'carga_horaria' => 800,
                ]);

                if (isset($from)) {
                    LegacyGradeSequenceFactory::new()->create([
                        'ref_serie_origem' => $from,
                        'ref_serie_destino' => $to,
                    ]);
                }

                $from = $to;
            }
        })->state([
            'qtd_etapas' => $total,
        ]);
    }

    public function withEarlyChildhoodEducation(): static
    {
        return $this->afterCreating(function (LegacyCourse $course) {
            $default = [
                'ref_cod_curso' => $course,
                'descricao' => null,
                'idade_ideal' => null,
                'concluinte' => 1,
                'dias_letivos' => 200,
                'carga_horaria' => 800,
            ];

            $bercario = LegacyGradeFactory::new()->create(array_merge($default, [
                'nm_serie' => 'Berçário',
                'etapa_curso' => 1,
                'idade_inicial' => 0,
                'idade_final' => 3,
            ]));

            $maternal = LegacyGradeFactory::new()->create(array_merge($default, [
                'nm_serie' => 'Maternal',
                'etapa_curso' => 2,
                'idade_inicial' => 3,
                'idade_final' => 4,
            ]));

            LegacyGradeSequenceFactory::new()->create([
                'ref_serie_origem' => $bercario,
                'ref_serie_destino' => $maternal,
            ]);

            $preescolar = LegacyGradeFactory::new()->create(array_merge($default, [
                'nm_serie' => 'Pré-Escolar',
                'etapa_curso' => 3,
                'idade_inicial' => 4,
                'idade_final' => 5,
                'concluinte' => 2,
            ]));

            LegacyGradeSequenceFactory::new()->create([
                'ref_serie_origem' => $maternal,
                'ref_serie_destino' => $preescolar,
            ]);
        })->state([
            'qtd_etapas' => 3,
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
