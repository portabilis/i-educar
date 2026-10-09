<?php

namespace Tests\Unit\Jobs;

use App\Jobs\UpdateRegistrationDateJob;
use App\User;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UpdateRegistrationDateJobTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());
    }

    public function test_updates_the_registration_date_and_the_enrollment_dates_except_the_relocated_ones(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['data_matricula' => '2030-01-10']);
        $relocated = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 1, 'data_enturmacao' => '2030-01-10', 'remanejado' => true]);
        $current = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 2, 'data_enturmacao' => '2030-01-10']);

        (new UpdateRegistrationDateJob([$registration->getKey()], '2030-02-01', '2030-02-05', false, DB::getDefaultConnection(), $this->user))->handle();

        $this->assertSame('2030-02-01', $registration->refresh()->data_matricula->format('Y-m-d'));
        $this->assertSame('2030-02-05', $current->refresh()->data_enturmacao->format('Y-m-d'));
        $this->assertSame('2030-01-10', $relocated->refresh()->data_enturmacao->format('Y-m-d'));
    }

    public function test_without_an_enrollment_date_only_the_registration_changes(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['data_matricula' => '2030-01-10']);
        $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'data_enturmacao' => '2030-01-10']);

        (new UpdateRegistrationDateJob([$registration->getKey()], '2030-02-01', null, true, DB::getDefaultConnection(), $this->user))->handle();

        $this->assertSame('2030-02-01', $registration->refresh()->data_matricula->format('Y-m-d'));
        $this->assertSame('2030-01-10', $enrollment->refresh()->data_enturmacao->format('Y-m-d'));
    }

    public function test_tags_carry_the_connection(): void
    {
        $job = new UpdateRegistrationDateJob([1], '2030-02-01', null, true, 'escola-alfa', $this->user);

        $this->assertSame(['escola-alfa', 'update-registration-date'], $job->tags());
    }

    public function test_lote_cancelado_nao_altera_nada(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['data_matricula' => '2030-01-10']);
        [$job, $batch] = (new UpdateRegistrationDateJob([$registration->getKey()], '2030-02-01', null, false, DB::getDefaultConnection(), $this->user))->withFakeBatch();
        $batch->cancel();

        $job->handle();

        $this->assertSame('2030-01-10', $registration->refresh()->data_matricula->format('Y-m-d'));
    }
}
