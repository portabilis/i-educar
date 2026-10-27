<?php

use App\Models\LegacyTransferRequest;
use App\Services\RegistrationService;
use App\User;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Busca de matrículas e troca de situação pelo serviço de matrícula.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());
    // O serviço de enturmação é resolvido do container com o usuário autenticado.
    $this->actingAs($user);
    $this->service = new RegistrationService($user);
});

test('find all', function () {
    $registrations = LegacyRegistrationFactory::new()->count(2)->create();

    $this->assertEqualsCanonicalizing($registrations->modelKeys(), $this->service->findAll($registrations->modelKeys())->modelKeys());
});

test('registrations not enrolled in a multigrade class consider every grade of the class', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['multiseriada' => true]);
    $schoolGrade = LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $schoolClass->ref_ref_cod_escola]);
    $secondGrade = LegacySchoolClassGradeFactory::new()->create([
        'turma_id' => $schoolClass,
        'escola_id' => $schoolGrade->ref_cod_escola,
        'serie_id' => $schoolGrade->ref_cod_serie,
    ]);
    $registration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $secondGrade->serie_id,
        'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ano' => $schoolClass->ano,
        'ativo' => 1,
        'ultima_matricula' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ]);

    $this->assertContains($registration->getKey(), $this->service->getRegistrationsNotEnrolled($schoolClass)->modelKeys());
});

test('transfer marks the enrollments registers the request and the exit date', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);
    $type = LegacyTransferTypeFactory::new()->create();

    $this->service->updateStatus($registration, [
        'nova_situacao' => App_Model_MatriculaSituacao::TRANSFERIDO,
        'transferencia_data' => '15/03/2030',
        'transferencia_tipo' => $type->getKey(),
        'transferencia_observacoes' => 'Mudou de cidade',
    ]);

    $registration->refresh();

    $this->assertSame(App_Model_MatriculaSituacao::TRANSFERIDO, $registration->aprovado);
    $this->assertSame('2030-03-15', $registration->data_cancel->format('Y-m-d'));
    $this->assertTrue($enrollment->refresh()->transferido);

    $request = LegacyTransferRequest::query()->where('ref_cod_matricula_saida', $registration->getKey())->first();

    $this->assertSame($type->getKey(), $request->ref_cod_transferencia_tipo);
    $this->assertSame('Mudou de cidade', $request->observacao);
    $this->assertSame('2030-03-15', $request->data_transferencia->format('Y-m-d'));
});

test('reclassification abandonment and death mark the enrollments', function () {
    foreach ([
        App_Model_MatriculaSituacao::RECLASSIFICADO => 'reclassificado',
        App_Model_MatriculaSituacao::ABANDONO => 'abandono',
        App_Model_MatriculaSituacao::FALECIDO => 'falecido',
    ] as $status => $flag) {
        $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);
        $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);

        $this->service->updateStatus($registration, ['nova_situacao' => $status]);

        $this->assertSame($status, $registration->refresh()->aprovado);
        $this->assertTrue($enrollment->refresh()->{$flag}, "não marcou {$flag}");
    }
});

test('abandonment also updates the situation of the score averages', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);
    LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]);

    $this->service->updateStatus($registration, ['nova_situacao' => App_Model_MatriculaSituacao::ABANDONO]);

    $this->assertSame(App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
});
