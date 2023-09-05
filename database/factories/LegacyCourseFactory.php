<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Models\LegacyKnowledgeArea;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCourseFactory extends Factory
{
    protected $model = LegacyCourse::class;

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
            'data_cadastro' => now(),
        ];
    }

    /**
     * Altera o nome do curso.
     */
    public function withName(string $name): static
    {
        return $this->state([
            'nm_curso' => $name,
            'descricao' => $name,
        ]);
    }

    /**
     * Adiciona as séries padrão do curso "Ensino Fundamental".
     */
    public function withElementarySchool(): static
    {
        $age = 6;
        $total = 9;

        return $this->afterCreating(function (LegacyCourse $course) use ($age, $total) {
            for ($year = 1; $year <= $total; $year++) {
                $to = LegacyGradeFactory::new()->withEvaluationRule()->create([
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

    /**
     * Adiciona as séries padrão do curso "Educação Infantil".
     */
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

            $bercario = LegacyGradeFactory::new()->withEvaluationRule()->create(array_merge($default, [
                'nm_serie' => 'Berçário',
                'etapa_curso' => 1,
                'idade_inicial' => 0,
                'idade_final' => 3,
            ]));

            $maternal = LegacyGradeFactory::new()->withEvaluationRule()->create(array_merge($default, [
                'nm_serie' => 'Maternal',
                'etapa_curso' => 2,
                'idade_inicial' => 3,
                'idade_final' => 4,
            ]));

            LegacyGradeSequenceFactory::new()->create([
                'ref_serie_origem' => $bercario,
                'ref_serie_destino' => $maternal,
            ]);

            $preescolar = LegacyGradeFactory::new()->withEvaluationRule()->create(array_merge($default, [
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

    /**
     * Vincula às séries do curso as disciplinas da área de conhecimento.
     */
    public function withKnowledgeArea(LegacyKnowledgeArea $knowledgeArea): static
    {
        return $this->afterCreating(function (LegacyCourse $course) use ($knowledgeArea) {
            $course->grades->each(function (LegacyGrade $grade) use ($knowledgeArea) {
                $knowledgeArea->disciplines->each(fn (LegacyDiscipline $discipline) => LegacyDisciplineAcademicYearFactory::new()->create([
                    'componente_curricular_id' => $discipline,
                    'ano_escolar_id' => $grade,
                    'hora_falta' => null,
                ]));
            });
        });
    }

    /**
     * Adiciona a série padrão.
     */
    public function withOneGrade(): static
    {
        return $this->afterCreating(function (LegacyCourse $course) {
            $to = LegacyGradeFactory::new()->withEvaluationRule()->create([
                'ref_cod_curso' => $course,
                'nm_serie' => 'Série Padrão',
                'descricao' => null,
                'etapa_curso' => 1,
                'idade_ideal' => 10,
                'idade_inicial' => 9,
                'idade_final' => 10,
                'concluinte' => 2,
                'dias_letivos' => 200,
                'carga_horaria' => 800,
            ]);
        })->state([
            'qtd_etapas' => 1,
        ]);
    }

    /**
     * Define o curso como padrão ano escolar.
     */
    public function standardAcademicYear(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'padrao_ano_escolar' => 1,
            ]);
        });
    }
}
