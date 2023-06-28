<?php

namespace Database\Factories;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolClassFactory extends Factory
{
    protected $model = LegacySchoolClass::class;

    protected LegacySchoolGrade $schoolGrade;

    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_turma' => $name = $this->faker->colorName,
            'sgl_turma' => mb_substr($name, 0, 3),
            'max_aluno' => 10,
            'data_cadastro' => now(),
            'turma_turno_id' => fn () => LegacyPeriodFactory::new()->create(),
            'ref_cod_turma_tipo' => fn () => LegacySchoolClassTypeFactory::new()->current(),
            'ref_ref_cod_escola' => fn () => $this->getSchoolGrade()->school_id,
            'ref_ref_cod_serie' => fn () => $this->getSchoolGrade()->grade_id,
            'ref_cod_curso' => fn () => $this->getSchoolGrade()->grade->course_id,
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'dias_semana' => [2, 3, 4, 5, 6],
            'multiseriada' => false,
            'ano' => now()->year,
            'visivel' => true,
            'ativo' => 1,
        ];
    }

    public function multiplesGrades(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'multiseriada' => true,
            ]);
        });
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

    public function inGrades(array $grades): static
    {
        return $this->afterCreating(function (LegacySchoolClass $schoolClass) use ($grades) {
            $schoolClass->update([
                'multiseriada' => true,
            ]);

            foreach ($grades as $grade) {
                LegacySchoolClassGradeFactory::new()->create([
                    'escola_id' => $schoolClass->school,
                    'serie_id' => $grade,
                    'turma_id' => $schoolClass,
                ]);
            }
        });
    }

    public function isMulti(): static
    {
        return $this->afterCreating(function (LegacySchoolClass $schoolClass) {
            $schoolClass->update([
                'multiseriada' => true,
            ]);

            LegacySchoolClassGradeFactory::new()->create([
                'escola_id' => $schoolClass->ref_ref_cod_escola,
                'serie_id' => $schoolClass->ref_ref_cod_serie,
                'turma_id' => $schoolClass,
            ]);
        });
    }

    public function addGrade(): static
    {
        return $this->afterCreating(function (LegacySchoolClass $schoolClass) {
            $schoolClass->update([
                'multiseriada' => true,
            ]);

            // TODO works only 1 year
            $schoolCourse = LegacySchoolCourse::query()
                ->where('ref_cod_escola', $schoolClass->ref_ref_cod_escola)
                ->where('ref_cod_curso', $schoolClass->ref_cod_curso)
                ->first();

            // TODO
            $schoolGrade = LegacySchoolGradeFactory::new()->useSchoolCourse($schoolCourse)->create();

            LegacySchoolClassGradeFactory::new()->create([
                'escola_id' => $schoolClass->ref_ref_cod_escola,
                'serie_id' => $schoolGrade->ref_cod_serie,
                'turma_id' => $schoolClass,
            ]);
        });
    }

    public function getSchoolGrade(): LegacySchoolGrade
    {
        if (empty($this->schoolGrade)) {
            $schoolGrade = LegacySchoolGradeFactory::new()->create();

            LegacyEvaluationRuleGradeYearFactory::new()->create([
                'serie_id' => $schoolGrade->grade,
                'ano_letivo' => now()->year,
            ]);

            $this->schoolGrade = $schoolGrade;
        }

        return $this->schoolGrade;
    }
}
