<?php

namespace Tests\Feature;

use App\Services\FinalStatusImportService;
use App_Model_MatriculaSituacao;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinalStatusImportTest extends TestCase
{
    use DatabaseTransactions;

    protected FinalStatusImportService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new FinalStatusImportService;

        Storage::fake('local');
    }

    public function test_service_can_analyze_uploaded_file()
    {
        $csvContent = "ID Matrícula,Situação Final,Data de Saída\n12345,Aprovado,\n67890,Transferido,15/12/2023";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $analysis = $this->service->analyzeUploadedFile($file);

        $this->assertArrayHasKey('total_rows', $analysis);
        $this->assertArrayHasKey('headers', $analysis);
        $this->assertArrayHasKey('sample_data', $analysis);
        $this->assertArrayHasKey('file_data', $analysis);
        $this->assertEquals(2, $analysis['total_rows']);
        $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
    }

    public function test_service_can_analyze_semicolon_separated_file()
    {
        $csvContent = "ID Matrícula;Situação Final;Data de Saída\n12345;Aprovado;\n67890;Transferido;15/12/2023";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $analysis = $this->service->analyzeUploadedFile($file);

        $this->assertEquals(2, $analysis['total_rows']);
        $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
    }

    public function test_service_validates_required_columns()
    {
        $requiredColumns = $this->service->getRequiredColumns();

        $this->assertContains('registration_id', $requiredColumns);
        $this->assertContains('final_status', $requiredColumns);
        $this->assertContains('exit_date', $requiredColumns);
        $this->assertCount(3, $requiredColumns);
    }

    public function test_service_knows_which_situations_require_exit_date()
    {
        $statusRequiringExitDate = $this->service->getStatusRequiringExitDate();

        $this->assertContains(App_Model_MatriculaSituacao::ABANDONO, $statusRequiringExitDate);
        $this->assertContains(App_Model_MatriculaSituacao::TRANSFERIDO, $statusRequiringExitDate);
        $this->assertContains(App_Model_MatriculaSituacao::FALECIDO, $statusRequiringExitDate);
        $this->assertContains(App_Model_MatriculaSituacao::RECLASSIFICADO, $statusRequiringExitDate);
        $this->assertCount(4, $statusRequiringExitDate);
    }

    public function test_service_validates_situation_names()
    {
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
    }

    public function test_validation_detects_invalid_registration_id()
    {
        $data = [
            ['registration_id' => 'abc123', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula ID tem valor inválido', $result['errors'][0]['error']);
    }

    public function test_validation_detects_empty_registration_id()
    {
        $data = [
            ['registration_id' => '', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('ID da matrícula é obrigatório', $result['errors'][0]['error']);
    }

    public function test_validation_detects_negative_registration_id()
    {
        $data = [
            ['registration_id' => '-123', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula ID tem valor inválido', $result['errors'][0]['error']);
    }

    public function test_validation_detects_invalid_situation()
    {
        $data = [
            ['registration_id' => '12345', 'final_status' => 'Aprovadoa', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('situação final inválida', $result['errors'][0]['error']);
        $this->assertStringContainsString('Aprovadoa', $result['errors'][0]['error']);
    }

    public function test_validation_detects_empty_situation()
    {
        $data = [
            ['registration_id' => '12345', 'final_status' => '', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Situação final é obrigatória', $result['errors'][0]['error']);
    }

    public function test_validation_detects_invalid_exit_date()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '40/20/2025'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
        $this->assertStringContainsString('40/20/2025', $result['errors'][0]['error']);
    }

    public function test_validation_detects_invalid_date_format()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '2023-12-15'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
        $this->assertStringContainsString('Use formato DD/MM/AAAA', $result['errors'][0]['error']);
    }

    public function test_validation_requires_exit_date_for_specific_situations()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
    }

    public function test_validation_requires_exit_date_for_deixou_de_frequentar()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
    }

    public function test_validation_detects_nonexistent_registration()
    {
        $data = [
            ['registration_id' => '99999', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula não encontrada', $result['errors'][0]['error']);
    }

    public function test_validation_detects_inactive_registration()
    {
        $registration = LegacyRegistrationFactory::new()->create(['ativo' => 0]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['warnings']);
        $this->assertStringContainsString('está inativa, mas será atualizada', $result['warnings'][0]['warning']);
    }

    public function test_validation_detects_multiple_active_enrollments()
    {
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
    }

    public function test_validation_passes_with_valid_data()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(0, $result['errors']);
    }

    public function test_validation_passes_with_valid_data_and_exit_date()
    {
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
    }

    public function test_validation_stops_on_first_error_batch()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => 'invalid', 'final_status' => 'Aprovado', 'exit_date' => ''],
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
    }

    public function test_auto_mapping_works_for_common_column_names()
    {
        $headers = ['ID Matrícula', 'Situação Final', 'Data de Saída'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_auto_mapping_handles_alternative_column_names()
    {
        $headers = ['Código', 'Status', 'Data Saída'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_auto_mapping_handles_case_insensitive_matching()
    {
        $headers = ['id matrícula', 'SITUAÇÃO FINAL', 'Data De Saída'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_auto_mapping_returns_negative_one_for_unmapped_columns()
    {
        $headers = ['Nome', 'Escola', 'Turma'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(-1, $mapping['registration_id']);
        $this->assertEquals(-1, $mapping['final_status']);
        $this->assertEquals(-1, $mapping['exit_date']);
    }

    public function test_service_handles_multiple_valid_rows()
    {
        $registration1 = LegacyRegistrationFactory::new()->create();
        $registration2 = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration1->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
            ['registration_id' => (string) $registration2->cod_matricula, 'final_status' => 'Reprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['validated_data']);
    }

    public function test_falecido_requires_exit_date()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Falecido', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Data de saída é obrigatória', $result['errors'][0]['error']);
    }

    public function test_transferido_updates_enrollment_correctly()
    {
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
    }

    public function test_abandono_does_not_require_exit_date()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(0, $result['errors']);
    }

    public function test_deixou_de_frequentar_maps_to_abandono_in_enrollment()
    {
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
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    private function analysisFor(array $rows): array
    {
        return [
            'headers' => ['ID Matrícula', 'Situação Final', 'Data de Saída'],
            'file_data' => $rows,
        ];
    }

    private function mapping(): array
    {
        return ['registration_id' => 'ID Matrícula', 'final_status' => 'Situação Final', 'exit_date' => 'Data de Saída', 'inexistente' => 'Coluna que não há'];
    }

    public function test_arquivo_vazio_nao_e_analisado()
    {
        $this->expectExceptionMessage('Arquivo vazio ou formato inválido.');

        $this->service->analyzeUploadedFile(UploadedFile::fake()->createWithContent('vazio.csv', ''));
    }

    public function test_traduz_as_colunas_obrigatorias_e_lista_as_situacoes()
    {
        $this->assertSame(['registration_id' => 'ID da Matrícula', 'final_status' => 'Situação Final', 'exit_date' => 'Data de Saída'], $this->service->getRequiredColumnsTranslations());
        $this->assertNotEmpty($this->service->getStatus());
    }

    public function test_importa_as_situacoes_e_pode_ignorar_as_aprovadas()
    {
        $approved = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
        $transferred = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
        $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $transferred, 'ativo' => 1]);
        $rows = [
            [(string) $approved->getKey(), 'Aprovado', ''],
            [(string) $transferred->getKey(), 'Transferido', '15/12/' . now()->year],
        ];

        $result = $this->service->processImport($this->analysisFor($rows), $this->mapping(), LegacyUserFactory::new()->create(), true);

        $this->assertSame('completed', $result['status']);
        $this->assertSame(1, $result['ignored']);
        $this->assertSame(1, $result['processed']);
        $this->assertSame(App_Model_MatriculaSituacao::EM_ANDAMENTO, $approved->refresh()->aprovado);
        $this->assertSame(App_Model_MatriculaSituacao::TRANSFERIDO, $transferred->refresh()->aprovado);
        $this->assertTrue((bool) $enrollment->refresh()->transferido);

        $result = $this->service->processImport($this->analysisFor($rows), $this->mapping(), LegacyUserFactory::new()->create());

        $this->assertSame('completed', $result['status']);
        $this->assertSame(0, $result['ignored']);
        $this->assertSame(2, $result['processed']);
        $this->assertSame(App_Model_MatriculaSituacao::APROVADO, $approved->refresh()->aprovado);
    }

    public function test_nao_importa_nada_quando_alguma_linha_tem_erro()
    {
        $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);

        $result = $this->service->processImport($this->analysisFor([
            [(string) $registration->getKey(), 'Aprovado', ''],
            ['abc', 'Aprovado', ''],
        ]), $this->mapping(), null);

        $this->assertSame('failed', $result['status']);
        $this->assertSame(0, $result['processed']);
        $this->assertSame(2, $result['total']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame(App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
    }

    public function test_enturmacoes_inativas_com_outra_situacao_ou_remanejadas_barram_a_importacao()
    {
        $rows = [];
        foreach (['remanejado', 'falecido', 'reclassificado'] as $flag) {
            $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
            LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 0, $flag => true]);
            $rows[] = [(string) $registration->getKey(), 'Transferido', '15/12/' . now()->year];
        }

        $result = $this->service->processImport($this->analysisFor($rows), $this->mapping(), LegacyUserFactory::new()->create());

        $this->assertSame('failed', $result['status']);
        $this->assertCount(3, $result['errors']);
        $this->assertStringContainsString('possui enturmação inativa com status incompatível', $result['errors'][0]['error']);
    }

    public function test_enturmacoes_inativas_sem_situacao_ou_com_a_mesma_situacao_sao_atualizadas()
    {
        $rows = [];
        foreach ([[], ['transferido' => true]] as $flags) {
            $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);
            LegacyEnrollmentFactory::new()->create($flags + ['ref_cod_matricula' => $registration, 'ativo' => 0]);
            $rows[] = [(string) $registration->getKey(), 'Transferido', '15/12/' . now()->year];
        }

        $result = $this->service->processImport($this->analysisFor($rows), $this->mapping(), LegacyUserFactory::new()->create());

        $this->assertSame('completed', $result['status']);
        $this->assertSame(2, $result['processed']);
    }

    public function test_valida_linhas_ja_nomeadas_e_linhas_do_csv()
    {
        $registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]);

        $named = $this->service->validateData([['registration_id' => (string) $registration->getKey(), 'final_status' => 'Aprovado', 'exit_date' => '']], []);
        $indexed = $this->service->validateData([[(string) $registration->getKey(), 'Aprovado', '']], ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertEmpty($named['errors']);
        $this->assertEmpty($indexed['errors']);
    }
}
