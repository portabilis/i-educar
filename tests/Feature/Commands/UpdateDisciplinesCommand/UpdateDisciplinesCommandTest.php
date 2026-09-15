<?php

namespace Tests\Feature\Commands\UpdateDisciplinesCommand;

use App\Models\MigratedDiscipline;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UpdateDisciplinesCommandTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('movementRowsProvider')]
    public function test_update_disciplines_command_records_each_valid_movement_and_ignores_non_numeric_rows(bool $includesIgnoredRows): void
    {
        \Tests\Feature\Commands\UpdateDisciplinesCommand\RecordingMoveDisciplineDataFake::reset();

        $disciplineFrom = LegacyDisciplineFactory::new()->create();
        $disciplineTo = LegacyDisciplineFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        $year = now()->year;

        $rows = [[$disciplineFrom->getKey(), $disciplineTo->getKey(), $grade->getKey(), $year]];

        if ($includesIgnoredRows) {
            array_unshift($rows, ['disciplina_origem', 'disciplina_destino', 'serie', 'ano']);
            $rows[] = ['invalido', $disciplineTo->getKey(), $grade->getKey(), $year];
        }

        $filePath = $this->createImportFile($rows);

        try {
            $this->artisan('update:disciplines', [
                'filename' => $filePath,
                '--copier' => [\Tests\Feature\Commands\UpdateDisciplinesCommand\RecordingMoveDisciplineDataFake::class],
            ])->assertExitCode(0);

            $migration = MigratedDiscipline::query()
                ->where('old_discipline_id', $disciplineFrom->getKey())
                ->where('new_discipline_id', $disciplineTo->getKey())
                ->where('grade_id', $grade->getKey())
                ->where('year', $year)
                ->first();

            $this->assertNotNull($migration);
            $this->assertCount(1, \Tests\Feature\Commands\UpdateDisciplinesCommand\RecordingMoveDisciplineDataFake::$calls);
            $this->assertSame([
                'disciplineFrom' => $disciplineFrom->getKey(),
                'disciplineTo' => $disciplineTo->getKey(),
                'year' => $year,
                'gradeId' => $grade->getKey(),
            ], \Tests\Feature\Commands\UpdateDisciplinesCommand\RecordingMoveDisciplineDataFake::$calls[0]);
            $this->assertSame(1, MigratedDiscipline::query()
                ->where('old_discipline_id', $disciplineFrom->getKey())
                ->where('new_discipline_id', $disciplineTo->getKey())
                ->count());
        } finally {
            @unlink($filePath);
        }
    }

    public static function movementRowsProvider(): array
    {
        return [
            'single valid movement row' => [false],
            'header-like and invalid rows are ignored' => [true],
        ];
    }

    private function createImportFile(array $rows): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'update-disciplines-import-');

        $this->assertNotFalse($filePath);

        unlink($filePath);
        $filePath .= '.csv';

        $handle = fopen($filePath, 'wb');

        $this->assertNotFalse($handle);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $filePath;
    }
}
