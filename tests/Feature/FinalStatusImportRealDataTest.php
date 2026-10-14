<?php

use App\Services\FinalStatusImportService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class);

beforeEach(function () {
    $this->service = new FinalStatusImportService;

    Storage::fake('local');
});

test('can analyze real csv file', function () {
    $realCsvPath = base_path('tests/Feature/final_status.csv');

    if (!file_exists($realCsvPath)) {
        $this->markTestSkipped('Arquivo CSV real não encontrado em tests/Feature/final_status.csv');
    }

    $csvContent = file_get_contents($realCsvPath);
    $file = UploadedFile::fake()->createWithContent('final_status.csv', $csvContent);

    $analysis = $this->service->analyzeUploadedFile($file);

    $this->assertArrayHasKey('total_rows', $analysis);
    $this->assertArrayHasKey('headers', $analysis);
    $this->assertArrayHasKey('sample_data', $analysis);

    $this->assertGreaterThan(0, $analysis['total_rows']);

    $this->assertContains('ID Matrícula', $analysis['headers']);
    $this->assertContains('Situação da Matrícula', $analysis['headers']);
    $this->assertContains('Data de saída da matrícula', $analysis['headers']);
});

test('auto mapping works with real csv headers', function () {
    $realHeaders = [
        'ID Matrícula',
        'NOME',
        'Escola',
        'Turma',
        'Série',
        'Curso',
        'Situação da Matrícula',
        'Data de saída da matrícula',
    ];

    $mapping = $this->service->autoMapColumns($realHeaders);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(6, $mapping['final_status']);
    $this->assertEquals(7, $mapping['exit_date']);
});

test('validates real csv data with errors', function () {
    $registration1 = LegacyRegistrationFactory::new()->create();
    $registration2 = LegacyRegistrationFactory::new()->create();
    $registration3 = LegacyRegistrationFactory::new()->create();
    $registration4 = LegacyRegistrationFactory::new()->create();

    $testData = [
        ['registration_id' => (string) $registration1->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ['registration_id' => (string) $registration2->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ['registration_id' => (string) $registration3->cod_matricula, 'final_status' => 'Aprovadoa', 'exit_date' => ''],
        ['registration_id' => (string) $registration4->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '40/20/2025'],
        ['registration_id' => 'asd', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertFalse($result['success']);
    $this->assertGreaterThan(0, count($result['errors']));

    $errorMessages = collect($result['errors'])->pluck('error')->implode(' ');

    $this->assertTrue(
        str_contains($errorMessages, 'situação final inválida') ||
        str_contains($errorMessages, 'data de saída inválida') ||
        str_contains($errorMessages, 'Matrícula ID tem valor inválido'),
        'Deveria detectar pelo menos um dos erros conhecidos'
    );
});

test('validates transferido requires exit date', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $testData = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
});

test('validates transferido with valid exit date', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $testData = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
    ];

    $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['errors']);
});

test('detects invalid date formats', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $invalidDates = [
        '40/20/2025',
        '2025-12-15',
        '32/12/2023',
        '15/13/2023',
        'abc/def/ghij',
    ];

    foreach ($invalidDates as $invalidDate) {
        $testData = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => $invalidDate],
        ];

        $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertFalse($result['success'], "Data '{$invalidDate}' deveria ser inválida");
        $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
    }
});

test('accepts valid date formats', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $validDates = [
        '15/12/2023',
        '01/01/2024',
        '29/02/2024',
        '31/12/2023',
    ];

    foreach ($validDates as $validDate) {
        $testData = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => $validDate],
        ];

        $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success'], "Data '{$validDate}' deveria ser válida");
    }
});

test('detects all invalid situation types', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $invalidSituations = [
        'Aprovadoa',
        'Reprovadoo',
        'Transferidoo',
        'Situação Inexistente',
        'Aprovado123',
        'APROVADO!',
    ];

    foreach ($invalidSituations as $invalidSituation) {
        $testData = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => $invalidSituation, 'exit_date' => ''],
        ];

        $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success'], "Situação '{$invalidSituation}' deveria ser inválida");
        $this->assertStringContainsString('situação final inválida', $result['errors'][0]['error']);
    }
});

test('accepts all valid situation types', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $validSituations = [
        'Aprovado',
        'Reprovado',
        'Cursando',
        'Transferido',
        'Reclassificado',
        'Deixou de frequentar',
        'Aprovado com dependência',
        'Aprovado pelo conselho',
        'Reprovado por faltas',
        'Falecido',
    ];

    foreach ($validSituations as $validSituation) {
        $testData = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => $validSituation, 'exit_date' => ''],
        ];

        $columnMapping = ['registration_id' => 0, 'final_status' => 1];

        if (in_array($validSituation, ['Transferido', 'Deixou de frequentar', 'Falecido', 'Reclassificado'])) {
            $testData[0]['exit_date'] = '15/12/2023';
            $columnMapping['exit_date'] = 2;
        }

        $result = $this->service->validateData($testData, $columnMapping);

        $this->assertTrue($result['success'], "Situação '{$validSituation}' deveria ser válida");
    }
});

test('detects all invalid registration id types', function () {
    $invalidRegistrationIds = [
        'abc123',
        'matricula123',
        '123abc',
        '',
        '0',
        '-123',
        'null',
        'undefined',
    ];

    foreach ($invalidRegistrationIds as $invalidRegistrationId) {
        $testData = [
            ['registration_id' => $invalidRegistrationId, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success'], "ID '{$invalidRegistrationId}' deveria ser inválido");
        $this->assertCount(1, $result['errors']);
    }
});

test('processes mixed valid and invalid data', function () {
    $registration1 = LegacyRegistrationFactory::new()->create();
    $registration2 = LegacyRegistrationFactory::new()->create();

    $testData = [
        ['registration_id' => (string) $registration1->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ['registration_id' => 'invalid_id', 'final_status' => 'Reprovado', 'exit_date' => ''],
        ['registration_id' => (string) $registration2->cod_matricula, 'final_status' => 'Cursando', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($testData, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertCount(1, $result['validated_data']);
});
