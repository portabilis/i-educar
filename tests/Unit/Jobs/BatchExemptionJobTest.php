<?php

namespace Tests\Unit\Jobs;

use App\Jobs\BatchExemptionJob;
use App\Models\NotificationType;
use App\Services\Exemption\BatchExemptionService;
use App\Services\NotificationService;
use App\User;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class BatchExemptionJobTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->findOrFail(LegacyUserFactory::new()->create()->getKey());
    }

    private function job(BatchExemptionService $service): BatchExemptionJob
    {
        return new BatchExemptionJob($service, DB::getDefaultConnection(), $this->user);
    }

    public function test_cria_as_dispensas_e_avisa_o_usuario(): void
    {
        $service = Mockery::mock(BatchExemptionService::class);
        $service->shouldReceive('handle')->once();
        $this->mock(NotificationService::class)
            ->shouldReceive('createByUser')->once()
            ->with($this->user->getKey(), 'O processo de Dispensa em lote foi finalizado.', '/consulta-dispensas', NotificationType::OTHER);

        $job = $this->job($service);
        $job->handle();

        $this->assertSame([DB::getDefaultConnection(), 'batch-exemption'], $job->tags());
        $this->assertSame($this->user, $job->getUser());
    }

    public function test_avisa_o_erro_e_deixa_a_falha_subir(): void
    {
        $service = Mockery::mock(BatchExemptionService::class);
        $service->shouldReceive('handle')->once()->andThrow(new RuntimeException('boom'));
        $this->mock(NotificationService::class)
            ->shouldReceive('createByUser')->once()
            ->with($this->user->getKey(), 'Não foi possível finalizar o processo de dispensa em lote.', '/consulta-dispensas', NotificationType::OTHER);

        $this->expectException(RuntimeException::class);

        $this->job($service)->handle();
    }
}
