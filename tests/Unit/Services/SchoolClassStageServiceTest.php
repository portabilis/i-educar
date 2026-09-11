<?php

namespace Tests\Unit\Services;

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
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

class SchoolClassStageServiceTest extends TestCase
{
    use DatabaseTransactions;

    private SchoolClassStageService $service;

    private LegacySchool $school;

    private LegacyCourse $course;

    private LegacyCourse $otherCourse;

    private LegacyStageType $stageType;

    private int $year;

    /**
     * Turma do curso que será alterado
     */
    private LegacySchoolClass $schoolClass;

    /**
     * Turma do curso que será alterado, do ano anterior
     */
    private LegacySchoolClass $previousYearSchoolClass;

    /**
     * Turma multisseriada de OUTRO curso, que tem uma série do curso alterado
     */
    private LegacySchoolClass $otherCourseSchoolClass;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_curso_que_passa_a_seguir_o_calendario_da_escola_perde_so_as_etapas_das_turmas_dele_no_ano(): void
    {
        $this->createStages($this->schoolClass);
        $this->createStages($this->previousYearSchoolClass);
        $this->createStages($this->otherCourseSchoolClass);

        $this->service->updateByCourse(
            course: $this->course->getKey(),
            year: $this->year,
            isStandardCalendar: true
        );

        $this->assertSame(0, $this->countStages($this->schoolClass));
        $this->assertSame(4, $this->countStages($this->previousYearSchoolClass));
        $this->assertSame(4, $this->countStages($this->otherCourseSchoolClass));
    }

    public function test_curso_que_deixa_de_seguir_o_calendario_da_escola_recebe_as_etapas_da_escola_so_nas_turmas_dele_no_ano(): void
    {
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

        $this->createStages($this->schoolClass);
        $this->createStages($this->otherCourseSchoolClass);

        $this->service->updateByCourse(
            course: $this->course->getKey(),
            year: $this->year,
            isStandardCalendar: false
        );

        $this->assertSame($this->schoolStages(), $this->stages($this->schoolClass));
        $this->assertSame(0, $this->countStages($this->previousYearSchoolClass));
        $this->assertSame(4, $this->countStages($this->otherCourseSchoolClass));
    }

    private function createStages(LegacySchoolClass $schoolClass): void
    {
        foreach (range(1, $this->stageType->num_etapas) as $sequencial) {
            LegacySchoolClassStageFactory::new()->create([
                'ref_cod_turma' => $schoolClass,
                'ref_cod_modulo' => $this->stageType,
                'sequencial' => $sequencial,
            ]);
        }
    }

    private function countStages(LegacySchoolClass $schoolClass): int
    {
        return LegacySchoolClassStage::query()
            ->whereSchoolClass($schoolClass->getKey())
            ->count();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function stages(LegacySchoolClass $schoolClass): array
    {
        return DB::table('pmieducar.turma_modulo')
            ->where('ref_cod_turma', $schoolClass->getKey())
            ->orderBy('sequencial')
            ->get(['ref_cod_modulo', 'sequencial', 'data_inicio', 'data_fim', 'dias_letivos'])
            ->map(fn ($stage) => (array) $stage)
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function schoolStages(): array
    {
        return DB::table('pmieducar.ano_letivo_modulo')
            ->where('ref_ref_cod_escola', $this->school->getKey())
            ->where('ref_ano', $this->year)
            ->orderBy('sequencial')
            ->get(['ref_cod_modulo', 'sequencial', 'data_inicio', 'data_fim', 'dias_letivos'])
            ->map(fn ($stage) => (array) $stage)
            ->all();
    }
}
