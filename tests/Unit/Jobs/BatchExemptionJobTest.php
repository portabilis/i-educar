<?php

use App\Jobs\BatchExemptionJob;
use App\Models\NotificationType;
use App\Services\Exemption\BatchExemptionService;
use App\Services\NotificationService;
use App\User;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->job = function (BatchExemptionService $service): BatchExemptionJob {
        return new BatchExemptionJob($service, DB::getDefaultConnection(), $this->user);
    };

    $this->user = User::query()->findOrFail(LegacyUserFactory::new()->create()->getKey());
});

test('cria as dispensas e avisa o usuario', function () {
    $service = Mockery::mock(BatchExemptionService::class);
    $service->shouldReceive('handle')->once();
    $this->mock(NotificationService::class)
        ->shouldReceive('createByUser')->once()
        ->with($this->user->getKey(), 'O processo de Dispensa em lote foi finalizado.', '/consulta-dispensas', NotificationType::OTHER);

    $job = ($this->job)($service);
    $job->handle();

    $this->assertSame([DB::getDefaultConnection(), 'batch-exemption'], $job->tags());
    $this->assertSame($this->user, $job->getUser());
});

test('avisa o erro e deixa a falha subir', function () {
    $service = Mockery::mock(BatchExemptionService::class);
    $service->shouldReceive('handle')->once()->andThrow(new RuntimeException('boom'));
    $this->mock(NotificationService::class)
        ->shouldReceive('createByUser')->once()
        ->with($this->user->getKey(), 'Não foi possível finalizar o processo de dispensa em lote.', '/consulta-dispensas', NotificationType::OTHER);

    $this->expectException(RuntimeException::class);

    ($this->job)($service)->handle();
});
