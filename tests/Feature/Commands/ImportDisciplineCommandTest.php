<?php

use App\Models\LegacyDiscipline;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyKnowledgeAreaFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->createRelativeImportFile = function (array $rows): string {
        $relativePath = 'tests/.tmp/discipline-import-' . uniqid('', true) . '.csv';
        $absolutePath = base_path($relativePath);
        $directory = dirname($absolutePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $handle = fopen($absolutePath, 'wb');

        $this->assertNotFalse($handle);

        fputcsv($handle, array_keys($rows[0]));

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $relativePath;
    };

    $this->readExportedRows = function (string $relativePath): array {
        $content = Storage::disk('local')->get($relativePath);
        $lines = array_values(array_filter(
            preg_split('/\r\n|\r|\n/', trim($content)) ?: [],
            static fn (string $line): bool => $line !== ''
        ));

        $header = str_getcsv(array_shift($lines));
        $rows = [];

        foreach ($lines as $line) {
            $row = array_combine($header, str_getcsv($line));

            $this->assertNotFalse($row);

            $rows[] = $row;
        }

        return $rows;
    };
});

test('import discipline command reuses existing discipline and exports generated identifier', function (bool $disciplineAlreadyExists,
    string $expectedAbbreviation) {
    Storage::fake('local');

    $disciplineName = 'História Integrada';

    $institution = LegacyInstitutionFactory::new()->create();
    $knowledgeArea = LegacyKnowledgeAreaFactory::new()->create([
        'instituicao_id' => $institution->getKey(),
    ]);

    $existingDiscipline = null;

    if ($disciplineAlreadyExists) {
        $existingDiscipline = LegacyDisciplineFactory::new()->create([
            'institution_id' => $institution->getKey(),
            'knowledge_area_id' => $knowledgeArea->getKey(),
            'name' => $disciplineName,
            'abbreviation' => 'OLD',
            'foundation_type' => 0,
            'educacenso_code' => 21,
            'order' => 5,
        ]);
    }

    $relativePath = ($this->createRelativeImportFile)([[
        'institution_id' => $institution->getKey(),
        'knowledge_area_id' => $knowledgeArea->getKey(),
        'name' => $disciplineName,
        'abbreviation' => 'HIS',
        'curriculum_base' => 1,
        'educacenso_discipline' => 42,
        'order' => 7,
    ]]);

    try {
        $this->artisan('import:discipline', [
            'filename' => $relativePath,
        ])->assertExitCode(0);

        $discipline = LegacyDiscipline::query()
            ->where('instituicao_id', $institution->getKey())
            ->where('area_conhecimento_id', $knowledgeArea->getKey())
            ->where('nome', $disciplineName)
            ->first();

        $this->assertNotNull($discipline);
        $this->assertSame($expectedAbbreviation, $discipline->abreviatura);
        $this->assertSame(1, LegacyDiscipline::query()
            ->where('instituicao_id', $institution->getKey())
            ->where('area_conhecimento_id', $knowledgeArea->getKey())
            ->where('nome', $disciplineName)
            ->count());
        $this->assertFalse(file_exists(base_path($relativePath)));

        $this->assertTrue(Storage::disk('local')->exists($relativePath));

        $exportedRows = ($this->readExportedRows)($relativePath);
        $expectedDisciplineId = (string) ($existingDiscipline?->getKey() ?? $discipline->getKey());

        $this->assertCount(1, $exportedRows);
        $this->assertSame($expectedDisciplineId, $exportedRows[0]['discipline_id']);
        $this->assertSame($disciplineName, $exportedRows[0]['name']);
        $this->assertSame('HIS', $exportedRows[0]['abbreviation']);
    } finally {
        @unlink(base_path($relativePath));
    }
})->with([
    'creates a new discipline' => [false, 'HIS'],
    'reuses an existing discipline without overwriting it' => [true, 'OLD'],
]);
