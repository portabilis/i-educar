<?php

use App\Services\RegistrationService;
use App\User;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Troca das datas de matrícula, de enturmação e de cancelamento.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = new RegistrationService(User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey()));
});

test('updates the registration and cancel dates', function () {
    // Um gatilho do banco apaga a data de cancelamento de matrícula em andamento.
    $registration = LegacyRegistrationFactory::new()->create(['data_matricula' => '2030-01-10', 'aprovado' => App_Model_MatriculaSituacao::ABANDONO]);

    $this->service->updateRegistrationDate($registration, new DateTime('2030-02-01'));
    $this->service->updateCancelDate($registration, new DateTime('2030-11-30'));

    $registration->refresh();

    $this->assertSame('2030-02-01', $registration->data_matricula->format('Y-m-d'));
    $this->assertSame('2030-11-30', $registration->data_cancel->format('Y-m-d'));
});

test('updates the enrollment dates and pulls the earlier ones back', function () {
    $registration = LegacyRegistrationFactory::new()->create(['data_matricula' => '2030-01-10']);
    $previous = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 1, 'data_enturmacao' => '2030-03-10', 'data_exclusao' => '2030-03-20', 'ativo' => 0]);
    $relocated = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 2, 'data_enturmacao' => '2030-03-20', 'remanejado' => true, 'ativo' => 0]);
    $current = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 3, 'data_enturmacao' => '2030-04-01', 'ativo' => 1]);

    $this->service->updateEnrollmentsDate($registration, new DateTime('2030-02-01'), ignoreRelocation: false);

    $this->assertSame('2030-02-01', $current->refresh()->data_enturmacao->format('Y-m-d'));
    $this->assertSame('2030-02-01', $previous->refresh()->data_enturmacao->format('Y-m-d'));
    $this->assertSame('2030-02-01', $previous->data_exclusao->format('Y-m-d'));
    $this->assertSame('2030-02-01', $relocated->refresh()->data_enturmacao->format('Y-m-d'));

    $this->service->updateEnrollmentsDate($registration, new DateTime('2030-05-01'), ignoreRelocation: true);

    $this->assertSame('2030-05-01', $relocated->refresh()->data_enturmacao->format('Y-m-d'));
});
