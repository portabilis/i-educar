<?php

namespace Tests\Feature\Commands;

use App\Models\LegacySchoolGradeDiscipline;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportSchoolGradeDisciplineCommandTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('schoolGradeDisciplineProvider')]
    public function test_import_school_grade_discipline_command_assigns_school_and_stage_configuration(
        bool $usesSchoolFromFile,
        string $stage,
        int $expectedSpecificStages,
        string $expectedUsedStages
    ): void {
        $schoolFromFile = LegacySchoolFactory::new()->create();
        $fallbackSchool = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $discipline = LegacyDisciplineFactory::new()->create();
        $year = (string) now()->year;

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $schoolFromFile->getKey(),
            'ref_cod_serie' => $grade->getKey(),
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $fallbackSchool->getKey(),
            'ref_cod_serie' => $grade->getKey(),
        ]);

        $row = [
            'grade_id' => $grade->getKey(),
            'discipline_id' => $discipline->getKey(),
            'academic_year' => $year,
            'stage' => $stage,
        ];

        if ($usesSchoolFromFile) {
            $row['school_id'] = $schoolFromFile->getKey();
        }

        $filePath = $this->createImportFile([$row]);

        try {
            $this->artisan('import:school-grade-discipline', [
                'filename' => $filePath,
                'school' => $fallbackSchool->getKey(),
            ])->assertExitCode(0);

            $expectedSchoolId = $usesSchoolFromFile
                ? $schoolFromFile->getKey()
                : $fallbackSchool->getKey();

            $schoolGradeDiscipline = LegacySchoolGradeDiscipline::query()
                ->where('ref_ref_cod_serie', $grade->getKey())
                ->where('ref_ref_cod_escola', $expectedSchoolId)
                ->where('ref_cod_disciplina', $discipline->getKey())
                ->first();

            $this->assertNotNull($schoolGradeDiscipline);
            $this->assertSame($expectedSpecificStages, $schoolGradeDiscipline->etapas_especificas);
            $this->assertSame($expectedUsedStages, $schoolGradeDiscipline->etapas_utilizadas ?? '');
            $this->assertContains($year, transformStringFromDBInArray($schoolGradeDiscipline->anos_letivos));
        } finally {
            @unlink($filePath);
        }
    }

    public static function schoolGradeDisciplineProvider(): array
    {
        return [
            'school defined in file without specific stages' => [true, '', 0, ''],
            'school falls back to command argument and formats decimal stages' => [false, '1.2', 1, '1,2'],
        ];
    }

    public function test_import_school_grade_discipline_command_updates_existing_link_instead_of_creating_duplicates(): void
    {
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $discipline = LegacyDisciplineFactory::new()->create();
        $year = (string) now()->year;

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_serie' => $grade->getKey(),
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $school->getKey(),
            'ref_ref_cod_serie' => $grade->getKey(),
            'ref_cod_disciplina' => $discipline->getKey(),
            'etapas_especificas' => 0,
            'etapas_utilizadas' => '',
            'anos_letivos' => '{2020}',
        ]);

        $filePath = $this->createImportFile([[
            'school_id' => $school->getKey(),
            'grade_id' => $grade->getKey(),
            'discipline_id' => $discipline->getKey(),
            'academic_year' => $year,
            'stage' => '2.4',
        ]]);

        try {
            $this->artisan('import:school-grade-discipline', [
                'filename' => $filePath,
                'school' => $school->getKey(),
            ])->assertExitCode(0);

            $schoolGradeDiscipline = LegacySchoolGradeDiscipline::query()
                ->where('ref_ref_cod_serie', $grade->getKey())
                ->where('ref_ref_cod_escola', $school->getKey())
                ->where('ref_cod_disciplina', $discipline->getKey())
                ->first();

            $this->assertNotNull($schoolGradeDiscipline);
            $this->assertSame(1, LegacySchoolGradeDiscipline::query()
                ->where('ref_ref_cod_serie', $grade->getKey())
                ->where('ref_ref_cod_escola', $school->getKey())
                ->where('ref_cod_disciplina', $discipline->getKey())
                ->count());
            $this->assertSame(1, $schoolGradeDiscipline->etapas_especificas);
            $this->assertSame('2,4', $schoolGradeDiscipline->etapas_utilizadas);
            $this->assertContains($year, transformStringFromDBInArray($schoolGradeDiscipline->anos_letivos));
        } finally {
            @unlink($filePath);
        }
    }

    private function createImportFile(array $rows): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'school-grade-discipline-import-');

        $this->assertNotFalse($filePath);

        unlink($filePath);
        $filePath .= '.csv';

        $handle = fopen($filePath, 'wb');

        $this->assertNotFalse($handle);

        fputcsv($handle, array_keys($rows[0]));

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $filePath;
    }
}
