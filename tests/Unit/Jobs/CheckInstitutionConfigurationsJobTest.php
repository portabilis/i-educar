<?php

use App\Jobs\CheckInstitutionConfigurationsJob;
use App\Models\LegacyInstitution;
use App\Models\LegacyUserType;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('lembra os institucionais de conferir as datas de troca de turma e deslocamento quando estão preenchidas', function () {
    $institution = new LegacyInstitution(['data_base_transferencia' => '2030-03-01']);
    app()->instance(LegacyInstitution::class, $institution);

    $service = Mockery::mock(NotificationService::class);
    $service->shouldReceive('createByUserLevel')->once()->with(LegacyUserType::LEVEL_INSTITUTIONAL, Mockery::pattern('/^Bem-vindo a \d{4}!/'), null, NotificationType::OTHER);

    $job = new CheckInstitutionConfigurationsJob('pgsql');
    $job->handle($service);

    app()->instance(LegacyInstitution::class, new LegacyInstitution);
    $job->handle($service);

    expect($job->tags())->toBe(['pgsql', 'check-institution-configurations']);
});
