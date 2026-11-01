<?php

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Services\SchoolClassStageService;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->createStages = function (LegacySchoolClass $schoolClass): void {
        foreach (range(1, $this->stageType->num_etapas) as $sequencial) {
            LegacySchoolClassStageFactory::new()->create([
                'ref_cod_turma' => $schoolClass,
                'ref_cod_modulo' => $this->stageType,
                'sequencial' => $sequencial,
            ]);
        }
    };

    $this->countStages = function (LegacySchoolClass $schoolClass): int {
        return LegacySchoolClassStage::query()
            ->whereSchoolClass($schoolClass->getKey())
            ->count();
    };

    /**
     * @return array<int, array<string, mixed>>
     */
    $this->stages = function (LegacySchoolClass $schoolClass): array {
        return DB::table('pmieducar.turma_modulo')
            ->where('ref_cod_turma', $schoolClass->getKey())
            ->orderBy('sequencial')
            ->get(['ref_cod_modulo', 'sequencial', 'data_inicio', 'data_fim', 'dias_letivos'])
            ->map(fn ($stage) => (array) $stage)
            ->all();
    };

    /**
     * @return array<int, array<string, mixed>>
     */
    $this->schoolStages = function (): array {
        return DB::table('pmieducar.ano_letivo_modulo')
            ->where('ref_ref_cod_escola', $this->school->getKey())
            ->where('ref_ano', $this->year)
            ->orderBy('sequencial')
            ->get(['ref_cod_modulo', 'sequencial', 'data_inicio', 'data_fim', 'dias_letivos'])
            ->map(fn ($stage) => (array) $stage)
            ->all();
    };

    $this->service = new SchoolClassStageService;
    $this->year = now()->year;

    $this->school = LegacySchoolFactory::new()->create();

    $this->course = LegacyCourseFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'padrao_ano_escolar' => 0,
    ]);

    $this->otherCourse = LegacyCourseFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'padrao_ano_escolar' => 0,
    ]);

    $grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course]);
    $otherGrade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->otherCourse]);

    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $grade,
    ]);

    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $otherGrade,
    ]);

    $this->stageType = LegacyStageTypeFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'nm_tipo' => 'Bimestral',
        'num_etapas' => 4,
    ]);

    $this->schoolClass = LegacySchoolClassFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'ref_ref_cod_escola' => $this->school,
        'ref_ref_cod_serie' => $grade,
        'ref_cod_curso' => $this->course,
        'ano' => $this->year,
    ]);

    $this->previousYearSchoolClass = LegacySchoolClassFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'ref_ref_cod_escola' => $this->school,
        'ref_ref_cod_serie' => $grade,
        'ref_cod_curso' => $this->course,
        'ano' => $this->year - 1,
    ]);

    $this->otherCourseSchoolClass = LegacySchoolClassFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'ref_ref_cod_escola' => $this->school,
        'ref_ref_cod_serie' => $otherGrade,
        'ref_cod_curso' => $this->otherCourse,
        'ano' => $this->year,
        'multiseriada' => true,
    ]);

    LegacySchoolClassGradeFactory::new()->create([
        'escola_id' => $this->school,
        'serie_id' => $grade,
        'turma_id' => $this->otherCourseSchoolClass,
    ]);
});

test('curso que passa a seguir o calendario da escola perde so as etapas das turmas dele no ano', function () {
    ($this->createStages)($this->schoolClass);
    ($this->createStages)($this->previousYearSchoolClass);
    ($this->createStages)($this->otherCourseSchoolClass);

    $this->service->updateByCourse(
        course: $this->course->getKey(),
        year: $this->year,
        isStandardCalendar: true
    );

    $this->assertSame(0, ($this->countStages)($this->schoolClass));
    $this->assertSame(4, ($this->countStages)($this->previousYearSchoolClass));
    $this->assertSame(4, ($this->countStages)($this->otherCourseSchoolClass));
});

test('curso que deixa de seguir o calendario da escola recebe as etapas da escola so nas turmas dele no ano', function () {
    LegacySchoolAcademicYearFactory::new()
        ->withStageType($this->stageType)
        ->create([
            'ref_cod_escola' => $this->school,
            'ano' => $this->year,
        ]);

    LegacySchoolAcademicYearFactory::new()
        ->withStageType($this->stageType)
        ->create([
            'ref_cod_escola' => $this->school,
            'ano' => $this->year - 1,
        ]);

    ($this->createStages)($this->schoolClass);
    ($this->createStages)($this->otherCourseSchoolClass);

    $this->service->updateByCourse(
        course: $this->course->getKey(),
        year: $this->year,
        isStandardCalendar: false
    );

    $this->assertSame(($this->schoolStages)(), ($this->stages)($this->schoolClass));
    $this->assertSame(0, ($this->countStages)($this->previousYearSchoolClass));
    $this->assertSame(4, ($this->countStages)($this->otherCourseSchoolClass));
});
