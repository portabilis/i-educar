<?php

namespace Tests\Feature;

use App\Services\FinalStatusImportService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinalStatusImportDatabaseTest extends TestCase
{
    use DatabaseTransactions;

    protected FinalStatusImportService $service;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new FinalStatusImportService;
        $this->user = LegacyUserFactory::new()->create();
    }

    public function test_aprovado_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['validated_data']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(1, $registration->aprovado);
        $this->assertNull($registration->data_cancel);
    }

    public function test_reprovado_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Reprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(2, $registration->aprovado);
        $this->assertNull($registration->data_cancel);
    }

    public function test_cursando_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 1]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Cursando', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(3, $registration->aprovado);
        $this->assertNull($registration->data_cancel);
    }

    public function test_transferido_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);
        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'ativo' => 1,
            'transferido' => false,
            'abandono' => false,
            'falecido' => false,
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(4, $registration->aprovado);
        $this->assertEquals('2023-12-15', $registration->data_cancel->format('Y-m-d'));

        $enrollment->refresh();
        $this->assertTrue($enrollment->transferido);
        $this->assertFalse($enrollment->abandono);
        $this->assertFalse($enrollment->falecido);
        $this->assertEquals(0, $enrollment->ativo);
        $this->assertEquals('2023-12-15', $enrollment->data_exclusao->format('Y-m-d'));
        $this->assertEquals($this->user->id, $enrollment->ref_usuario_exc);
    }

    public function test_reclassificado_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);
        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'ativo' => 1,
            'transferido' => false,
            'abandono' => false,
            'falecido' => false,
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Reclassificado', 'exit_date' => '20/11/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(5, $registration->aprovado);
        $this->assertEquals('2023-11-20', $registration->data_cancel->format('Y-m-d'));

        $enrollment->refresh();
        $this->assertTrue($enrollment->reclassificado);
        $this->assertFalse($enrollment->transferido);
        $this->assertFalse($enrollment->falecido);
        $this->assertFalse($enrollment->abandono);
        $this->assertEquals(0, $enrollment->ativo);
        $this->assertEquals('2023-11-20', $enrollment->data_exclusao->format('Y-m-d'));
        $this->assertEquals($this->user->id, $enrollment->ref_usuario_exc);
    }

    public function test_deixou_de_frequentar_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);
        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'ativo' => 1,
            'transferido' => false,
            'abandono' => false,
            'falecido' => false,
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => '20/11/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(6, $registration->aprovado);
        $this->assertEquals('2023-11-20', $registration->data_cancel->format('Y-m-d'));

        $enrollment->refresh();
        $this->assertTrue($enrollment->abandono);
        $this->assertFalse($enrollment->transferido);
        $this->assertFalse($enrollment->falecido);
        $this->assertEquals(0, $enrollment->ativo);
        $this->assertEquals('2023-11-20', $enrollment->data_exclusao->format('Y-m-d'));
        $this->assertEquals($this->user->id, $enrollment->ref_usuario_exc);
    }

    public function test_aprovado_com_dependencia_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado com dependência', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(12, $registration->aprovado);
        $this->assertNull($registration->data_cancel);
    }

    public function test_aprovado_pelo_conselho_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado pelo conselho', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(13, $registration->aprovado);
        $this->assertNull($registration->data_cancel);
    }

    public function test_reprovado_por_faltas_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Reprovado por faltas', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(14, $registration->aprovado);
        $this->assertNull($registration->data_cancel);
    }

    public function test_falecido_saves_correctly_in_database()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);
        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'ativo' => 1,
            'transferido' => false,
            'abandono' => false,
            'falecido' => false,
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Falecido', 'exit_date' => '10/10/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(15, $registration->aprovado);
        $this->assertEquals('2023-10-10', $registration->data_cancel->format('Y-m-d'));

        $enrollment->refresh();
        $this->assertTrue($enrollment->falecido);
        $this->assertFalse($enrollment->transferido);
        $this->assertFalse($enrollment->abandono);
        $this->assertEquals(0, $enrollment->ativo);
        $this->assertEquals('2023-10-10', $enrollment->data_exclusao->format('Y-m-d'));
        $this->assertEquals($this->user->id, $enrollment->ref_usuario_exc);
    }

    public function test_situation_without_enrollment_saves_only_registration()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Transferido', 'exit_date' => '15/12/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('não possui enturmação', $result['errors'][0]['error']);
    }

    public function test_situation_that_does_not_update_enrollment()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);
        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'ativo' => 1,
            'transferido' => false,
            'abandono' => false,
            'falecido' => false,
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Aprovado', 'exit_date' => ''],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(1, $registration->aprovado);
        $this->assertNull($registration->data_cancel);

        $enrollment->refresh();
        $this->assertEquals(1, $enrollment->ativo);
        $this->assertFalse($enrollment->transferido);
        $this->assertFalse($enrollment->abandono);
        $this->assertFalse($enrollment->falecido);
        $this->assertNull($enrollment->data_exclusao);
    }

    public function test_deixou_de_frequentar_with_old_active_enrollment_and_existing_abandoned()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        // Enturmação antiga ativa (não deve ser mexida)
        $oldEnrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'sequencial' => 1,
            'ativo' => 1,
            'transferido' => false,
            'abandono' => false,
            'falecido' => false,
        ]);

        // Enturmação mais recente já com abandono (deve ser atualizada)
        $recentEnrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'sequencial' => 2,
            'ativo' => 0,
            'transferido' => false,
            'abandono' => true,
            'falecido' => false,
            'data_exclusao' => '2023-10-15',
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => '20/11/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(6, $registration->aprovado);
        $this->assertEquals('2023-11-20', $registration->data_cancel->format('Y-m-d'));

        // Enturmação antiga não deve ser alterada
        $oldEnrollment->refresh();
        $this->assertEquals(1, $oldEnrollment->ativo);
        $this->assertFalse($oldEnrollment->abandono);
        $this->assertFalse($oldEnrollment->transferido);
        $this->assertFalse($oldEnrollment->falecido);
        $this->assertNull($oldEnrollment->data_exclusao);

        // Enturmação recente deve ter apenas a data atualizada
        $recentEnrollment->refresh();
        $this->assertTrue($recentEnrollment->abandono);
        $this->assertFalse($recentEnrollment->transferido);
        $this->assertFalse($recentEnrollment->falecido);
        $this->assertEquals(0, $recentEnrollment->ativo);
        $this->assertEquals('2023-11-20', $recentEnrollment->data_exclusao->format('Y-m-d'));
        $this->assertEquals($this->user->id, $recentEnrollment->ref_usuario_exc);
    }

    public function test_deixou_de_frequentar_with_two_existing_abandoned_enrollments()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        // Enturmação antiga já com abandono (não deve ser mexida)
        $oldEnrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'sequencial' => 1,
            'ativo' => 0,
            'transferido' => false,
            'abandono' => true,
            'falecido' => false,
            'data_exclusao' => '2023-09-15',
        ]);

        // Enturmação mais recente já com abandono (deve ter data atualizada)
        $recentEnrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'sequencial' => 2,
            'ativo' => 0,
            'transferido' => false,
            'abandono' => true,
            'falecido' => false,
            'data_exclusao' => '2023-10-15',
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => '20/11/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        $this->assertTrue($result['success']);

        $validatedRow = $result['validated_data'][0];
        $this->callUpdateDatabase($validatedRow);

        $registration->refresh();
        $this->assertEquals(6, $registration->aprovado);
        $this->assertEquals('2023-11-20', $registration->data_cancel->format('Y-m-d'));

        // Enturmação antiga não deve ser alterada
        $oldEnrollment->refresh();
        $this->assertEquals(0, $oldEnrollment->ativo);
        $this->assertTrue($oldEnrollment->abandono);
        $this->assertFalse($oldEnrollment->transferido);
        $this->assertFalse($oldEnrollment->falecido);
        $this->assertEquals('2023-09-15', $oldEnrollment->data_exclusao->format('Y-m-d'));

        // Enturmação recente deve ter apenas a data atualizada
        $recentEnrollment->refresh();
        $this->assertTrue($recentEnrollment->abandono);
        $this->assertFalse($recentEnrollment->transferido);
        $this->assertFalse($recentEnrollment->falecido);
        $this->assertEquals(0, $recentEnrollment->ativo);
        $this->assertEquals('2023-11-20', $recentEnrollment->data_exclusao->format('Y-m-d'));
        $this->assertEquals($this->user->id, $recentEnrollment->ref_usuario_exc);
    }

    public function test_deixou_de_frequentar_with_transferred_enrollment()
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

        // Enturmação com transferido=true (não deve ser permitido converter para abandono)
        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration->cod_matricula,
            'ativo' => 0,
            'transferido' => true,
            'abandono' => false,
            'falecido' => false,
            'data_exclusao' => '2023-10-15',
        ]);

        $data = [
            ['registration_id' => (string) $registration->cod_matricula, 'final_status' => 'Deixou de frequentar', 'exit_date' => '20/11/2023'],
        ];

        $result = $this->service->validateData($data, ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);

        // Deve falhar porque não é permitido converter transferido para abandono
        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('status incompatível', $result['errors'][0]['error']);
    }

    private function callUpdateDatabase(array $validatedRow): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('updateDatabase');
        $method->setAccessible(true);
        $method->invoke($this->service, $validatedRow, $this->user);
    }
}
