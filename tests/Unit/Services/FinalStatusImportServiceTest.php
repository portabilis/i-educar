<?php

use App\Models\RegistrationStatus;
use App\Services\FinalStatusImportService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = new FinalStatusImportService;
});

test('returns correct required columns', function () {
    $requiredColumns = $this->service->getRequiredColumns();

    $this->assertEquals(['registration_id', 'final_status', 'exit_date'], $requiredColumns);
});

test('returns correct situations requiring exit date', function () {
    $statusRequiringExitDate = $this->service->getStatusRequiringExitDate();

    $expected = [RegistrationStatus::TRANSFERRED, RegistrationStatus::ABANDONED, RegistrationStatus::DECEASED, RegistrationStatus::RECLASSIFIED];
    $this->assertEqualsCanonicalizing($expected, $statusRequiringExitDate);
});

test('returns correct status mapping', function () {
    $mapping = $this->service->getStatusMapping();

    $expectedMappings = [
        'aprovado' => RegistrationStatus::APPROVED,
        'reprovado' => RegistrationStatus::REPROVED,
        'cursando' => RegistrationStatus::ONGOING,
        'transferido' => RegistrationStatus::TRANSFERRED,
        'reclassificado' => RegistrationStatus::RECLASSIFIED,
        'deixou de frequentar' => RegistrationStatus::ABANDONED,
        'aprovado com dependência' => RegistrationStatus::APPROVED_WITH_DEPENDENCY,
        'aprovado pelo conselho' => RegistrationStatus::APPROVED_BY_BOARD,
        'reprovado por faltas' => RegistrationStatus::REPROVED_BY_ABSENCE,
        'falecido' => RegistrationStatus::DECEASED,
    ];

    foreach ($expectedMappings as $status => $code) {
        $this->assertArrayHasKey($status, $mapping);
        $this->assertEquals($code, $mapping[$status]);
    }
});

test('auto mapping works for standard headers', function () {
    $headers = ['ID Matrícula', 'Situação Final', 'Data de Saída'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('auto mapping works for alternative headers', function () {
    $headers = ['Código', 'Status', 'Data Saída'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('auto mapping works for portuguese headers', function () {
    $headers = ['Matrícula', 'Situação da Matrícula', 'Data de saída da matrícula'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('auto mapping handles case insensitive headers', function () {
    $headers = ['id matrícula', 'SITUAÇÃO FINAL', 'Data De Saída'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('validates empty registration id', function () {
    $data = [
        ['registration_id' => '', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('ID da matrícula é obrigatório', $result['errors'][0]['error']);
});

test('validates non numeric registration id', function () {
    $data = [
        ['registration_id' => 'abc123', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula ID tem valor inválido para matrícula', $result['errors'][0]['error']);
    $this->assertStringContainsString('abc123', $result['errors'][0]['error']);
});

test('validates negative registration id', function () {
    $data = [
        ['registration_id' => '-123', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula ID tem valor inválido para matrícula', $result['errors'][0]['error']);
});

test('validates zero registration id', function () {
    $data = [
        ['registration_id' => '0', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula ID tem valor inválido para matrícula', $result['errors'][0]['error']);
});

test('validates empty final status', function () {
    $data = [
        ['registration_id' => '12345', 'final_status' => '', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Situação final é obrigatória', $result['errors'][0]['error']);
});

test('validates invalid final status', function () {
    $data = [
        ['registration_id' => '12345', 'final_status' => 'Situação Inexistente', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula 12345 com situação final inválida', $result['errors'][0]['error']);
    $this->assertStringContainsString('Situação Inexistente', $result['errors'][0]['error']);
});

test('validates case insensitive final status', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'APROVADO', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['errors']);
});

test('validates invalid date format', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '2023-12-15'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
    $this->assertStringContainsString('2023-12-15', $result['errors'][0]['error']);
});

test('validates impossible date', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '40/20/2025'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
    $this->assertStringContainsString('40/20/2025', $result['errors'][0]['error']);
});

test('validates valid date format', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['errors']);
});

test('requires exit date for transferido', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
});

test('requires exit date for deixou de frequentar', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
});

test('does not require exit date for aprovado', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['errors']);
});

test('abandono is invalid from csv', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Abandono', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('situação final inválida', $result['errors'][0]['error']);
    $this->assertStringContainsString('Abandono', $result['errors'][0]['error']);
});

test('detects nonexistent registration', function () {
    $data = [
        ['registration_id' => '99999', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula não encontrada', $result['errors'][0]['error']);
});

test('warns about inactive registration', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 0]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(1, $result['warnings']);
    $this->assertStringContainsString('está inativa, mas será atualizada', $result['warnings'][0]['warning']);
});

test('warns about multiple active enrollments', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('possui 2 enturmações ativas', $result['errors'][0]['error']);
    $this->assertStringContainsString('enquanto houver mais de uma enturmação ativa', $result['errors'][0]['error']);
});

test('does not warn about single active enrollment', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['warnings']);
});

test('processes multiple valid rows', function () {
    $registration1 = LegacyRegistrationFactory::new()->create();
    $registration2 = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration1->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ['registration_id' => (string) $registration2->cod_matricula, 'final_status' => 'Reprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(2, $result['validated_data']);
});

test('deixou de frequentar maps to abandono code', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => '15/12/2023'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertTrue($result['success']);
    $this->assertCount(1, $result['validated_data']);

    $validatedRow = $result['validated_data'][0];
    $this->assertEquals(RegistrationStatus::ABANDONED, $validatedRow['status_code']);
    $this->assertEquals('deixou de frequentar', $validatedRow['normalized_final_status']);
});

test('stops validation on first error', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => '', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertCount(0, $result['validated_data']);
});

test('analyzes uploaded file correctly', function () {
    $csvContent = "ID Matrícula,Situação Final,Data de Saída\n12345,Aprovado,\n67890,Transferido,15/12/2023";
    $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

    $analysis = $this->service->analyzeUploadedFile($file);

    $this->assertEquals(2, $analysis['total_rows']);
    $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
    $this->assertCount(2, $analysis['sample_data']);
});

test('handles csv with quotes', function () {
    $csvContent = '"ID Matrícula","Situação Final","Data de Saída"' . "\n" . '"12345","Aprovado",""';
    $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

    $analysis = $this->service->analyzeUploadedFile($file);

    $this->assertEquals(1, $analysis['total_rows']);
    $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
});

test('handles csv with semicolon separator', function () {
    $csvContent = "ID Matrícula;Situação Final;Data de Saída\n12345;Aprovado;\n67890;Transferido;15/12/2023";
    $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

    $analysis = $this->service->analyzeUploadedFile($file);

    $this->assertEquals(2, $analysis['total_rows']);
    $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
});
