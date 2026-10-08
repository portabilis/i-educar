<?php

use App\Jobs\CheckInstitutionConfigurationsJob;
use App\Jobs\TenantsJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

uses(TestCase::class);

test('dispara o job informado para cada base de cliente', function () {
    Queue::fake();
    $pgsql = config('database.connections.pgsql');
    config(['database.connections' => ['pgsql' => $pgsql, 'cliente_a' => $pgsql, 'cliente_b' => $pgsql]]);

    (new TenantsJob(CheckInstitutionConfigurationsJob::class))->handle();

    Queue::assertPushed(CheckInstitutionConfigurationsJob::class, 2);
    Queue::assertPushed(CheckInstitutionConfigurationsJob::class, fn ($job) => $job->tags()[0] === 'cliente_b');
});
