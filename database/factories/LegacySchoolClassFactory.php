<?php

use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassType;
use App\Models\LegacySchoolGrade;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacySchoolClass::class, function (Faker $faker) {

    $schoolGrade = factory(LegacySchoolGrade::class)->create();
    $evaluationRule = factory(LegacyEvaluationRuleGradeYear::class)->create([
        'serie_id' => $schoolGrade->grade,
        'ano_letivo' => now()->year,
    ]);

    return [
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'nm_turma' => $name = $faker->colorName,
        'sgl_turma' => mb_substr($name, 0, 3),
        'max_aluno' => $faker->numberBetween(10, 25),
        'data_cadastro' => now(),
        'ref_cod_turma_tipo' => factory(LegacySchoolClassType::class)->state('unique')->make(),
        'ref_ref_cod_escola' => $schoolGrade->school_id,
        'ref_ref_cod_serie' => $schoolGrade->grade_id,
        'ref_cod_curso' => $schoolGrade->grade->course_id,
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->state('unique')->make(),
        'dias_semana' => [2, 3, 4, 5, 6],
        'ano' => now()->year,
        'visivel' => true,
    ];
});

$factory->defineAs(LegacySchoolClass::class, 'morning', function (Faker $faker) use ($factory) {
    $schollClass = $factory->raw(LegacySchoolClass::class);

    return array_merge($schollClass, [
        'hora_inicial' => '07:45',
        'hora_final' => '11:45',
    ]);
});

$factory->defineAs(LegacySchoolClass::class, 'afternoon', function (Faker $faker) use ($factory) {
    $schollClass = $factory->raw(LegacySchoolClass::class);

    return array_merge($schollClass, [
        'hora_inicial' => '13:15',
        'hora_final' => '17:15',
    ]);
});
