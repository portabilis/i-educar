<?php

use App\Jobs\Concerns\ShouldNotificate;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Tests\TestCase;

uses(TestCase::class);

test('o job sem usuário não notifica, e os textos padrão valem para quem não os sobrescreve', function () {
    $job = new class
    {
        use ShouldNotificate;

        public $user = null;
    };

    $this->mock(NotificationService::class)->shouldNotReceive('createByUser');

    $job->notificateSuccess();
    $job->notificateError();

    expect($job->getSuccessMessage())->toBe('Processo executado com sucesso')
        ->and($job->getErrorMessage())->toBe('Não foi possível executar o processo')
        ->and($job->getNotificationUrl())->toBeNull()
        ->and($job->getNotificationType())->toBe(NotificationType::OTHER)
        ->and($job->getNotificationService())->toBeInstanceOf(NotificationService::class);
});
