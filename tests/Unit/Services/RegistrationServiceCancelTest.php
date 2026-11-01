<?php

use App\Exceptions\Registration\RegistrationException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Services\RegistrationService;
use App\User;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Cancelamento de matrícula: enturmações, sequência de séries, transferência
 * reaberta e reordenação da turma.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());
    $this->service = new RegistrationService($this->user);
});

test('refuses to cancel twice', function () {
    $this->expectException(RegistrationException::class);
    $this->expectExceptionMessage('A matrícula já está cancelada.');

    $this->service->cancelRegistration(LegacyRegistrationFactory::new()->create(['ativo' => 0]));
});

test('cancels the registration and everything tied to it', function () {
    $student = LegacyStudentFactory::new()->create();
    $origin = LegacyGradeFactory::new()->create();
    $destiny = LegacyGradeFactory::new()->create();
    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $origin, 'ref_serie_destino' => $destiny, 'ativo' => 1]);

    $previous = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_ref_cod_serie' => $origin, 'ano' => 2029, 'ativo' => 1, 'ultima_matricula' => 0]);
    $transferred = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ano' => 2029, 'ativo' => 1, 'aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);
    $transfer = LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $transferred, 'ativo' => 0]);

    $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_ref_cod_serie' => $destiny, 'ano' => 2030, 'ativo' => 1, 'ultima_matricula' => 1]);
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $first = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_turma' => $schoolClass, 'sequencial' => 1, 'sequencial_fechamento' => 1, 'ativo' => 1]);
    $second = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_turma' => $schoolClass, 'sequencial' => 2, 'sequencial_fechamento' => 2, 'ativo' => 1]);

    $this->service->cancelRegistration($registration);

    $registration->refresh();

    $this->assertSame(0, $registration->ativo);
    $this->assertSame(0, $registration->ultima_matricula);
    $this->assertSame($this->user->getKey(), $registration->ref_usuario_exc);
    $this->assertSame(0, $first->refresh()->ativo);
    $this->assertSame(0, $second->refresh()->ativo);
    $this->assertSame(9999, $first->sequencial_fechamento);
    $this->assertSame(9999, $second->sequencial_fechamento);
    $this->assertSame(1, $previous->refresh()->ultima_matricula);
    $this->assertSame(1, $transfer->refresh()->ativo);
});

test('cancels a registration without grade sequence or transfer', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1, 'ultima_matricula' => 1]);

    $this->service->cancelRegistration($registration, reorderSchoolClasses: false);

    $this->assertSame(0, $registration->refresh()->ativo);
});

test('keeps going when the grade sequence has no previous registration', function () {
    $destiny = LegacyGradeFactory::new()->create();
    LegacyGradeSequenceFactory::new()->create(['ref_serie_destino' => $destiny, 'ativo' => 1]);
    $registration = LegacyRegistrationFactory::new()->create(['ref_ref_cod_serie' => $destiny, 'ativo' => 1, 'ultima_matricula' => 1]);

    $this->service->cancelRegistration($registration);

    $this->assertSame(0, $registration->refresh()->ativo);
});

test('avisa quando nao consegue desativar as enturmacoes', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);
    LegacyEnrollment::saving(fn () => throw new RuntimeException('banco indisponível'));

    $this->expectException(RegistrationException::class);
    $this->expectExceptionMessage('Não foi possível desativar as enturmações da matrícula.');

    $this->service->cancelRegistration($registration);
});

test('avisa quando nao consegue marcar a ultima matricula da sequencia', function () {
    $student = LegacyStudentFactory::new()->create();
    $origin = LegacyGradeFactory::new()->create();
    $destiny = LegacyGradeFactory::new()->create();
    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $origin, 'ref_serie_destino' => $destiny, 'ativo' => 1]);
    $previous = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_ref_cod_serie' => $origin, 'ano' => 2029, 'ativo' => 1, 'ultima_matricula' => 0]);
    $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_ref_cod_serie' => $destiny, 'ano' => 2030, 'ativo' => 1]);
    LegacyRegistration::saving(function ($model) use ($previous) {
        if ($model->is($previous)) {
            throw new RuntimeException('banco indisponível');
        }
    });

    $this->expectException(RegistrationException::class);
    $this->expectExceptionMessage('Não foi possível editar a "Última Matrícula da Sequência".');

    $this->service->cancelRegistration($registration);
});
