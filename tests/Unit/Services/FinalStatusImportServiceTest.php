<?php

namespace Tests\Unit\Services;

use App\Models\RegistrationStatus;
use App\Services\FinalStatusImportService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FinalStatusImportServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected FinalStatusImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FinalStatusImportService;
    }

    public function test_returns_correct_required_columns()
    {
        $requiredColumns = $this->service->getRequiredColumns();

        $this->assertEquals(['registration_id', 'final_status', 'exit_date'], $requiredColumns);
    }

    public function test_returns_correct_situations_requiring_exit_date()
    {
        $statusRequiringExitDate = $this->service->getStatusRequiringExitDate();

        $expected = [RegistrationStatus::TRANSFERRED, RegistrationStatus::ABANDONED, RegistrationStatus::DECEASED, RegistrationStatus::RECLASSIFIED];
        $this->assertEqualsCanonicalizing($expected, $statusRequiringExitDate);
    }

    public function test_returns_correct_status_mapping()
    {
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
    }

    public function test_auto_mapping_works_for_standard_headers()
    {
        $headers = ['ID Matrícula', 'Situação Final', 'Data de Saída'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_auto_mapping_works_for_alternative_headers()
    {
        $headers = ['Código', 'Status', 'Data Saída'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_auto_mapping_works_for_portuguese_headers()
    {
        $headers = ['Matrícula', 'Situação da Matrícula', 'Data de saída da matrícula'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_auto_mapping_handles_case_insensitive_headers()
    {
        $headers = ['id matrícula', 'SITUAÇÃO FINAL', 'Data De Saída'];

        $mapping = $this->service->autoMapColumns($headers);

        $this->assertEquals(0, $mapping['registration_id']);
        $this->assertEquals(1, $mapping['final_status']);
        $this->assertEquals(2, $mapping['exit_date']);
    }

    public function test_validates_empty_registration_id()
    {
        $data = [
            ['registration_id' => '', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('ID da matrícula é obrigatório', $result['errors'][0]['error']);
    }

    public function test_validates_non_numeric_registration_id()
    {
        $data = [
            ['registration_id' => 'abc123', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula ID tem valor inválido para matrícula', $result['errors'][0]['error']);
        $this->assertStringContainsString('abc123', $result['errors'][0]['error']);
    }

    public function test_validates_negative_registration_id()
    {
        $data = [
            ['registration_id' => '-123', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula ID tem valor inválido para matrícula', $result['errors'][0]['error']);
    }

    public function test_validates_zero_registration_id()
    {
        $data = [
            ['registration_id' => '0', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula ID tem valor inválido para matrícula', $result['errors'][0]['error']);
    }

    public function test_validates_empty_final_status()
    {
        $data = [
            ['registration_id' => '12345', 'final_status' => '', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Situação final é obrigatória', $result['errors'][0]['error']);
    }

    public function test_validates_invalid_final_status()
    {
        $data = [
            ['registration_id' => '12345', 'final_status' => 'Situação Inexistente', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula 12345 com situação final inválida', $result['errors'][0]['error']);
        $this->assertStringContainsString('Situação Inexistente', $result['errors'][0]['error']);
    }

    public function test_validates_case_insensitive_final_status()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'APROVADO', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(0, $result['errors']);
    }

    public function test_validates_invalid_date_format()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '2023-12-15'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('data de saída inválida', $result['errors'][0]['error']);
        $this->assertStringContainsString('2023-12-15', $result['errors'][0]['error']);
    }

    public function test_validates_impossible_date()
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

    public function test_validates_valid_date_format()
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

    public function test_requires_exit_date_for_transferido()
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

    public function test_requires_exit_date_for_deixou_de_frequentar()
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

    public function test_does_not_require_exit_date_for_aprovado()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(0, $result['errors']);
    }

    public function test_abandono_is_invalid_from_csv()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Abandono', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('situação final inválida', $result['errors'][0]['error']);
        $this->assertStringContainsString('Abandono', $result['errors'][0]['error']);
    }

    public function test_detects_nonexistent_registration()
    {
        $data = [
            ['registration_id' => '99999', 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Matrícula não encontrada', $result['errors'][0]['error']);
    }

    public function test_warns_about_inactive_registration()
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

    public function test_warns_about_multiple_active_enrollments()
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
        $this->assertStringContainsString('enquanto houver mais de uma enturmação ativa', $result['errors'][0]['error']);
    }

    public function test_does_not_warn_about_single_active_enrollment()
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
        $this->assertCount(0, $result['warnings']);
    }

    public function test_processes_multiple_valid_rows()
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

    public function test_deixou_de_frequentar_maps_to_abandono_code()
    {
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
    }

    public function test_stops_validation_on_first_error()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $data = [
            ['registration_id' => '', 'final_status' => 'Aprovado', 'exit_date' => ''],
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertCount(0, $result['validated_data']);
    }

    public function test_analyzes_uploaded_file_correctly()
    {
        $csvContent = "ID Matrícula,Situação Final,Data de Saída\n12345,Aprovado,\n67890,Transferido,15/12/2023";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $analysis = $this->service->analyzeUploadedFile($file);

        $this->assertEquals(2, $analysis['total_rows']);
        $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
        $this->assertCount(2, $analysis['sample_data']);
    }

    public function test_handles_csv_with_quotes()
    {
        $csvContent = '"ID Matrícula","Situação Final","Data de Saída"' . "\n" . '"12345","Aprovado",""';
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $analysis = $this->service->analyzeUploadedFile($file);

        $this->assertEquals(1, $analysis['total_rows']);
        $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
    }

    public function test_handles_csv_with_semicolon_separator()
    {
        $csvContent = "ID Matrícula;Situação Final;Data de Saída\n12345;Aprovado;\n67890;Transferido;15/12/2023";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $analysis = $this->service->analyzeUploadedFile($file);

        $this->assertEquals(2, $analysis['total_rows']);
        $this->assertEquals(['ID Matrícula', 'Situação Final', 'Data de Saída'], $analysis['headers']);
    }
}
