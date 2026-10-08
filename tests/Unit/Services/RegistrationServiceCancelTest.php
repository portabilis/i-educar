<?php

namespace Tests\Unit\Services;

use App\Exceptions\Registration\RegistrationException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Services\RegistrationService;
use App\User;
use App_Model_MatriculaSituacao;
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
class RegistrationServiceCancelTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    private RegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());
        $this->service = new RegistrationService($this->user);
    }

    public function test_refuses_to_cancel_twice(): void
    {
        $this->expectException(RegistrationException::class);
        $this->expectExceptionMessage('A matrícula já está cancelada.');

        $this->service->cancelRegistration(LegacyRegistrationFactory::new()->create(['ativo' => 0]));
    }

    public function test_cancels_the_registration_and_everything_tied_to_it(): void
    {
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
    }

    public function test_cancels_a_registration_without_grade_sequence_or_transfer(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1, 'ultima_matricula' => 1]);

        $this->service->cancelRegistration($registration, reorderSchoolClasses: false);

        $this->assertSame(0, $registration->refresh()->ativo);
    }

    public function test_keeps_going_when_the_grade_sequence_has_no_previous_registration(): void
    {
        $destiny = LegacyGradeFactory::new()->create();
        LegacyGradeSequenceFactory::new()->create(['ref_serie_destino' => $destiny, 'ativo' => 1]);
        $registration = LegacyRegistrationFactory::new()->create(['ref_ref_cod_serie' => $destiny, 'ativo' => 1, 'ultima_matricula' => 1]);

        $this->service->cancelRegistration($registration);

        $this->assertSame(0, $registration->refresh()->ativo);
    }

    public function test_avisa_quando_nao_consegue_desativar_as_enturmacoes(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);
        LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);
        LegacyEnrollment::saving(fn () => throw new \RuntimeException('banco indisponível'));

        $this->expectException(RegistrationException::class);
        $this->expectExceptionMessage('Não foi possível desativar as enturmações da matrícula.');

        $this->service->cancelRegistration($registration);
    }

    public function test_avisa_quando_nao_consegue_marcar_a_ultima_matricula_da_sequencia(): void
    {
        $student = LegacyStudentFactory::new()->create();
        $origin = LegacyGradeFactory::new()->create();
        $destiny = LegacyGradeFactory::new()->create();
        LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $origin, 'ref_serie_destino' => $destiny, 'ativo' => 1]);
        $previous = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_ref_cod_serie' => $origin, 'ano' => 2029, 'ativo' => 1, 'ultima_matricula' => 0]);
        $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_ref_cod_serie' => $destiny, 'ano' => 2030, 'ativo' => 1]);
        LegacyRegistration::saving(function ($model) use ($previous) {
            if ($model->is($previous)) {
                throw new \RuntimeException('banco indisponível');
            }
        });

        $this->expectException(RegistrationException::class);
        $this->expectExceptionMessage('Não foi possível editar a "Última Matrícula da Sequência".');

        $this->service->cancelRegistration($registration);
    }
}
