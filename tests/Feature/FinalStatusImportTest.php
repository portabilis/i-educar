<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Services\FinalStatusImportService;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->analysisFor = function (array $rows): array {
        return [
            'headers' => ['ID Matrícula', 'Situação Final', 'Data de Saída'],
            'file_data' => $rows,
        ];
    };

    $this->mapping = function (): array {
        return ['registration_id' => 'ID Matrícula', 'final_status' => 'Situação Final', 'exit_date' => 'Data de Saída', 'inexistente' => 'Coluna que não há'];
    };

    $this->service = new FinalStatusImportService;

    Storage::fake('local');
});

test('service can analyze uploaded file', function () {
    $csvContent = "ID Matrícula,Situação Final,Data de Saída\n12345,Aprovado,\n67890,Transferido,15/12/2023";
    $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

    $analysis = $this->service->analyzeUploadedFile($file);

    $this->assertArrayHasKey('total_rows', $analysis);
    $this->assertArrayHasKey('headers', $analysis);
    $this->assertArrayHasKey('sample_data', $analysis);
    $this->assertArrayHasKey('file_data', $analysis);
    $this->assertEquals(2, $analysis['total_rows']);
    $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
});

test('service can analyze semicolon separated file', function () {
    $csvContent = "ID Matrícula;Situação Final;Data de Saída\n12345;Aprovado;\n67890;Transferido;15/12/2023";
    $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

    $analysis = $this->service->analyzeUploadedFile($file);

    $this->assertEquals(2, $analysis['total_rows']);
    $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
});

test('service validates required columns', function () {
    $requiredColumns = $this->service->getRequiredColumns();

    $this->assertContains('registration_id', $requiredColumns);
    $this->assertContains('final_status', $requiredColumns);
    $this->assertContains('exit_date', $requiredColumns);
    $this->assertCount(3, $requiredColumns);
});

test('service knows which situations require exit date', function () {
    $statusRequiringExitDate = $this->service->getStatusRequiringExitDate();

    $this->assertContains(App_Model_MatriculaSituacao::ABANDONO, $statusRequiringExitDate);
    $this->assertContains(App_Model_MatriculaSituacao::TRANSFERIDO, $statusRequiringExitDate);
    $this->assertContains(App_Model_MatriculaSituacao::FALECIDO, $statusRequiringExitDate);
    $this->assertContains(App_Model_MatriculaSituacao::RECLASSIFICADO, $statusRequiringExitDate);
    $this->assertCount(4, $statusRequiringExitDate);
});

test('service validates situation names', function () {
    $statusMapping = $this->service->getStatusMapping();

    $this->assertArrayHasKey('aprovado', $statusMapping);
    $this->assertArrayHasKey('reprovado', $statusMapping);
    $this->assertArrayHasKey('transferido', $statusMapping);
    $this->assertArrayHasKey('deixou de frequentar', $statusMapping);
    $this->assertArrayHasKey('falecido', $statusMapping);

    $this->assertEquals(1, $statusMapping['aprovado']);
    $this->assertEquals(2, $statusMapping['reprovado']);
    $this->assertEquals(4, $statusMapping['transferido']);
    $this->assertEquals(6, $statusMapping['deixou de frequentar']);
    $this->assertEquals(15, $statusMapping['falecido']);
});

test('validation detects invalid registration id', function () {
    $data = [
        ['registration_id' => 'abc123', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula ID tem valor inválido', $result['errors'][0]['error']);
});

test('validation detects empty registration id', function () {
    $data = [
        ['registration_id' => '', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('ID da matrícula é obrigatório', $result['errors'][0]['error']);
});

test('validation detects negative registration id', function () {
    $data = [
        ['registration_id' => '-123', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula ID tem valor inválido', $result['errors'][0]['error']);
});

test('validation detects invalid situation', function () {
    $data = [
        ['registration_id' => '12345', 'final_status' => 'Aprovadoa', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('situação final inválida', $result['errors'][0]['error']);
    $this->assertStringContainsString('Aprovadoa', $result['errors'][0]['error']);
});

test('validation detects empty situation', function () {
    $data = [
        ['registration_id' => '12345', 'final_status' => '', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Situação final é obrigatória', $result['errors'][0]['error']);
});

test('validation detects invalid exit date', function () {
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

test('validation detects invalid date format', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '2023-12-15'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
    $this->assertStringContainsString('Use formato DD/MM/AAAA', $result['errors'][0]['error']);
});

test('validation requires exit date for specific situations', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
});

test('validation requires exit date for deixou de frequentar', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
});

test('validation detects nonexistent registration', function () {
    $data = [
        ['registration_id' => '99999', 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Matrícula não encontrada', $result['errors'][0]['error']);
});

test('validation detects inactive registration', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 0]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(1, $result['warnings']);
    $this->assertStringContainsString('está inativa, mas será atualizada', $result['warnings'][0]['warning']);
});

test('validation detects multiple active enrollments', function () {
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
    $this->assertStringContainsString(' enquanto houver mais de uma enturmação ativa', $result['errors'][0]['error']);
});

test('validation passes with valid data', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['errors']);
});

test('validation passes with valid data and exit date', function () {
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

test('validation stops on first error batch', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => 'invalid', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
});

test('auto mapping works for common column names', function () {
    $headers = ['ID Matrícula', 'Situação Final', 'Data de Saída'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('auto mapping handles alternative column names', function () {
    $headers = ['Código', 'Status', 'Data Saída'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('auto mapping handles case insensitive matching', function () {
    $headers = ['id matrícula', 'SITUAÇÃO FINAL', 'Data De Saída'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(0, $mapping['registration_id']);
    $this->assertEquals(1, $mapping['final_status']);
    $this->assertEquals(2, $mapping['exit_date']);
});

test('auto mapping returns negative one for unmapped columns', function () {
    $headers = ['Nome', 'Escola', 'Turma'];

    $mapping = $this->service->autoMapColumns($headers);

    $this->assertEquals(-1, $mapping['registration_id']);
    $this->assertEquals(-1, $mapping['final_status']);
    $this->assertEquals(-1, $mapping['exit_date']);
});

test('service handles multiple valid rows', function () {
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

test('falecido requires exit date', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Falecido', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertFalse($result['success']);
    $this->assertCount(1, $result['errors']);
    $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
});

test('transferido updates enrollment correctly', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $registration->cod_matricula,
        'ativo' => 1,
    ]);

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertTrue($result['success']);
    $this->assertCount(1, $result['validated_data']);

    $validatedRow = $result['validated_data'][0];
    $this->assertNotNull($validatedRow['enrollment']);
    $this->assertEquals($enrollment->id, $validatedRow['enrollment']->id);
});

test('abandono does not require exit date', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $data = [
        ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
    ];

    $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

    $this->assertTrue($result['success']);
    $this->assertCount(0, $result['errors']);
});

test('deixou de frequentar maps to abandono in enrollment', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $enrollment = LegacyEnrollmentFactory::new()->create([
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
    $this->assertEquals(App_Model_MatriculaSituacao::ABANDONO, $validatedRow['status_code']);
});

test('arquivo vazio nao e analisado', function () {
    $this->expectExceptionMessage('Arquivo vazio ou formato inválido.');

    $this->service->analyzeUploadedFile(UploadedFile::fake()->createWithContent('vazio.csv', ''));
});

test('traduz as colunas obrigatorias e lista as situacoes', function () {
    $this->assertSame(['registration_id' => 'ID da Matrícula', 'final_status' => 'Situação Final', 'exit_date' => 'Data de Saída'], $this->service->getRequiredColumnsTranslations());
    $this->assertNotEmpty($this->service->getStatus());
});

test('importa as situacoes e pode ignorar as aprovadas', function () {
    $approved = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
    $transferred = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $transferred, 'ativo' => 1]);
    $rows = [
        [(string) $approved->getKey(), 'Aprovado', ''],
        [(string) $transferred->getKey(), 'Transferido', '15/12/' . now()->year],
    ];

    $result = $this->service->processImport(($this->analysisFor)($rows), ($this->mapping)(), LegacyUserFactory::new()->create(), true);

    $this->assertSame('completed', $result['status']);
    $this->assertSame(1, $result['ignored']);
    $this->assertSame(1, $result['processed']);
    $this->assertSame(App_Model_MatriculaSituacao::EM_ANDAMENTO, $approved->refresh()->aprovado);
    $this->assertSame(App_Model_MatriculaSituacao::TRANSFERIDO, $transferred->refresh()->aprovado);
    $this->assertTrue((bool) $enrollment->refresh()->transferido);

    $result = $this->service->processImport(($this->analysisFor)($rows), ($this->mapping)(), LegacyUserFactory::new()->create());

    $this->assertSame('completed', $result['status']);
    $this->assertSame(0, $result['ignored']);
    $this->assertSame(2, $result['processed']);
    $this->assertSame(App_Model_MatriculaSituacao::APROVADO, $approved->refresh()->aprovado);
});

test('nao importa nada quando alguma linha tem erro', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);

    $result = $this->service->processImport(($this->analysisFor)([
        [(string) $registration->getKey(), 'Aprovado', ''],
        ['abc', 'Aprovado', ''],
    ]), ($this->mapping)(), null);

    $this->assertSame('failed', $result['status']);
    $this->assertSame(0, $result['processed']);
    $this->assertSame(2, $result['total']);
    $this->assertNotEmpty($result['errors']);
    $this->assertSame(App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
});

test('enturmacoes inativas com outra situacao ou remanejadas barram a importacao', function () {
    $rows = [];
    foreach (['remanejado', 'falecido', 'reclassificado'] as $flag) {
        $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
        LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 0, $flag => true]);
        $rows[] = [(string) $registration->getKey(), 'Transferido', '15/12/' . now()->year];
    }

    $result = $this->service->processImport(($this->analysisFor)($rows), ($this->mapping)(), LegacyUserFactory::new()->create());

    $this->assertSame('failed', $result['status']);
    $this->assertCount(3, $result['errors']);
    $this->assertStringContainsString('possui enturmação inativa com status incompatível', $result['errors'][0]['error']);
});

test('enturmacoes inativas sem situacao ou com a mesma situacao sao atualizadas', function () {
    $rows = [];
    foreach ([[], ['transferido' => true]] as $flags) {
        $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
        LegacyEnrollmentFactory::new()->create($flags + ['ref_cod_matricula' => $registration, 'ativo' => 0]);
        $rows[] = [(string) $registration->getKey(), 'Transferido', '15/12/' . now()->year];
    }

    $result = $this->service->processImport(($this->analysisFor)($rows), ($this->mapping)(), LegacyUserFactory::new()->create());

    $this->assertSame('completed', $result['status']);
    $this->assertSame(2, $result['processed']);
});

test('valida linhas ja nomeadas e linhas do csv', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);

    $named = $this->service->validateData([['registration_id' => (string) $registration->getKey(), 'final_status' => 'Aprovado', 'exit_date' => '']], []);
    $indexed = $this->service->validateData([[(string) $registration->getKey(), 'Aprovado', '']], ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

    $this->assertEmpty($named['errors']);
    $this->assertEmpty($indexed['errors']);
});

test('a transferencia atualiza a situacao das medias da matricula', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
    $scores = LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]);
    $average = LegacyDisciplineScoreAverageFactory::new()->create(['nota_aluno_id' => $scores, 'situacao' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);

    $result = $this->service->processImport(($this->analysisFor)([[(string) $registration->getKey(), 'Transferido', '15/12/' . now()->year]]), ($this->mapping)(), LegacyUserFactory::new()->create());

    $this->assertSame('completed', $result['status']);
    $this->assertSame(App_Model_MatriculaSituacao::TRANSFERIDO, $average->refresh()->situacao);
});

test('desfaz o lote quando a gravacao falha', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
    LegacyRegistration::saving(fn () => throw new RuntimeException('banco indisponível'));

    $result = $this->service->processImport(($this->analysisFor)([[(string) $registration->getKey(), 'Reprovado', '']]), ($this->mapping)(), LegacyUserFactory::new()->create());

    $this->assertSame('failed', $result['status']);
    $this->assertSame('Erro crítico no processamento: banco indisponível', $result['errors'][0]['error']);
    $this->assertSame(App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
});

test('erro inesperado ao validar a linha vira erro da linha', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);
    $messages = ['sintaxe de entrada é inválida para tipo integer', 'conexão perdida'];
    LegacyEnrollment::retrieved(function () use (&$messages) {
        throw new RuntimeException(array_shift($messages));
    });
    $rows = [[(string) $registration->getKey(), 'Transferido', '15/12/' . now()->year]];

    $first = $this->service->processImport(($this->analysisFor)($rows), ($this->mapping)(), LegacyUserFactory::new()->create());
    $second = $this->service->processImport(($this->analysisFor)($rows), ($this->mapping)(), LegacyUserFactory::new()->create());

    $this->assertSame("Matrícula ID tem valor inválido para matrícula: '{$registration->getKey()}'", $first['errors'][0]['error']);
    $this->assertSame('Erro ao validar linha: conexão perdida', $second['errors'][0]['error']);
});
