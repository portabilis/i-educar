<?php

namespace Database\Factories;

use App\Models\LegacySchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolClass::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $schoolGrade = LegacySchoolGradeFactory::new()->create();

        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $schoolGrade->grade,
            'ano_letivo' => now()->year,
        ]);

        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->unique()->make(),
            'nm_turma' => $name = $this->faker->colorName,
            'sgl_turma' => mb_substr($name, 0, 3),
            'max_aluno' => $this->faker->numberBetween(10, 25),
            'data_cadastro' => now(),
            'ref_cod_turma_tipo' => fn () => LegacySchoolClassTypeFactory::new()->unique()->make(),
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $schoolGrade->grade->course_id,
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->unique()->make(),
            'dias_semana' => [2, 3, 4, 5, 6],
            'ano' => now()->year,
            'visivel' => true,
        ];
    }

    public function morning(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'hora_inicial' => '07:45',
                'hora_final' => '11:45',
            ]);
        });
    }

    public function afternoon(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'hora_inicial' => '13:15',
                'hora_final' => '17:15',
            ]);
        });
    }
}
