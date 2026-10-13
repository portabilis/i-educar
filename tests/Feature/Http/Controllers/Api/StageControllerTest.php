<?php

use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('is standard calendar', #[
    GET('/api/stage', ['Stages'], 'Get all stages'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Stage')
] function () {
    $school = LegacySchoolFactory::new()->create();
    $grade = LegacyGradeFactory::new()->create();
    $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $school,
        'ref_cod_serie' => $grade,
    ]);
    $schoolAcademicYear = LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $school,
        'ano' => now()->year,
    ]);
    $academicYearState = LegacyAcademicYearStageFactory::new()->create([
        'ref_ano' => $schoolAcademicYear->ano,
        'ref_ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
        'escola_ano_letivo_id' => $schoolAcademicYear,
    ]);
    $response = $this->get("api/stage?course={$course->getKey()}");
    $expected = [
        'data' => [
            $academicYearState->sequencial => $academicYearState->sequencial . 'º ' . mb_strtoupper($academicYearState->stageType->nm_tipo),
        ],
    ];
    $response->assertOk();
    $response->assertJson($expected);
});

test('is not standard calendar', function () {
    $school = LegacySchoolFactory::new()->create();
    $grade = LegacyGradeFactory::new()->create();
    $course = LegacyCourseFactory::new()->create();
    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $school,
        'ref_cod_serie' => $grade,
    ]);
    $schoolClass = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $school,
        'ref_ref_cod_serie' => $grade,
        'ref_cod_curso' => $course,
    ]);

    $schoolClassStage = LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    $response = $this->get("api/stage?course={$course->getKey()}");
    $expected = [
        'data' => [
            $schoolClassStage->sequencial => $schoolClassStage->sequencial . 'º ' . mb_strtoupper($schoolClassStage->stageType->nm_tipo),
        ],
    ];
    $response->assertOk();
    $response->assertJson($expected);
});
