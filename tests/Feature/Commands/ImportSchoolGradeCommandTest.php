<?php

namespace Tests\Feature\Commands;

use App\Models\LegacySchoolGrade;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportSchoolGradeCommandTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('importSchoolGradeProvider')]
    public function test_import_school_grade_command_creates_school_grade_from_file(bool $usesSchoolIdFromFile): void
    {
        $schoolFromFile = LegacySchoolFactory::new()->create();
        $fallbackSchool = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $rows = $usesSchoolIdFromFile
            ? [[
                'school_id' => $schoolFromFile->cod_escola,
                'grade_id' => $grade->cod_serie,
                'academic_year' => $year,
            ]]
            : [[
                'grade_id' => $grade->cod_serie,
                'academic_year' => $year,
            ]];

        $filePath = $this->createImportFile($rows);

        try {
            $this->artisan('import:school-grade', [
                'filename' => $filePath,
                'school' => $fallbackSchool->cod_escola,
            ])->assertExitCode(0);

            $expectedSchoolId = $usesSchoolIdFromFile
                ? $schoolFromFile->cod_escola
                : $fallbackSchool->cod_escola;

            $this->assertDatabaseHas('pmieducar.escola_serie', [
                'ref_cod_escola' => $expectedSchoolId,
                'ref_cod_serie' => $grade->cod_serie,
                'ativo' => 1,
            ]);

            $schoolGrade = LegacySchoolGrade::query()
                ->where('ref_cod_escola', $expectedSchoolId)
                ->where('ref_cod_serie', $grade->cod_serie)
                ->first();

            $this->assertNotNull($schoolGrade);
            $this->assertContains((string) $year, transformStringFromDBInArray($schoolGrade->anos_letivos));
        } finally {
            @unlink($filePath);
        }
    }

    public static function importSchoolGradeProvider(): array
    {
        return [
            'school defined in file' => [true],
            'school falls back to command argument' => [false],
        ];
    }

    private function createImportFile(array $rows): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'school-grade-import-');

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
