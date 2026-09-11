<?php

namespace Tests\Feature\Commands;

use App\Models\LegacyDisciplineAcademicYear;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportDisciplineAcademicYearCommandTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('disciplineAcademicYearProvider')]
    public function test_import_discipline_academic_year_command_creates_missing_links_without_overwriting_existing_ones(
        bool $createsExistingLink,
        int $expectedWorkload,
        string $expectedAcademicYear
    ): void {
        $discipline = LegacyDisciplineFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $currentYear = (string) now()->year;

        if ($createsExistingLink) {
            LegacyDisciplineAcademicYearFactory::new()->create([
                'componente_curricular_id' => $discipline->getKey(),
                'ano_escolar_id' => $grade->getKey(),
                'carga_horaria' => $expectedWorkload,
                'anos_letivos' => '{' . $expectedAcademicYear . '}',
            ]);
        }

        $filePath = $this->createImportFile([[
            'discipline_id' => $discipline->getKey(),
            'grade_id' => $grade->getKey(),
            'class_hours' => 60,
            'academic_year' => $currentYear,
        ]]);

        try {
            $this->artisan('import:discipline-academic-year', [
                'filename' => $filePath,
            ])->assertExitCode(0);

            $disciplineAcademicYear = LegacyDisciplineAcademicYear::query()
                ->where('componente_curricular_id', $discipline->getKey())
                ->where('ano_escolar_id', $grade->getKey())
                ->first();

            $this->assertNotNull($disciplineAcademicYear);
            $this->assertEquals($expectedWorkload, (float) $disciplineAcademicYear->carga_horaria);
            $this->assertSame(1, LegacyDisciplineAcademicYear::query()
                ->where('componente_curricular_id', $discipline->getKey())
                ->where('ano_escolar_id', $grade->getKey())
                ->count());
            $this->assertContains($expectedAcademicYear, transformStringFromDBInArray($disciplineAcademicYear->anos_letivos));
        } finally {
            @unlink($filePath);
        }
    }

    public static function disciplineAcademicYearProvider(): array
    {
        return [
            'creates new discipline academic year link' => [false, 60, (string) now()->year],
            'keeps existing workload and school year set' => [true, 120, '2018'],
        ];
    }

    private function createImportFile(array $rows): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'discipline-academic-year-import-');

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
