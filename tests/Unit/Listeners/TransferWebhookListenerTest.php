<?php

use App\Events\TransferEvent;
use App\Listeners\TransferWebhookListener;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('pede ao i-Diário os lançamentos do aluno transferido, bloqueia a situação e avisa quem transferiu', function () {
    Http::fake([
        'idiario.test/*' => Http::sequence()->push(['ok' => true])->push(['erro' => true], 500),
    ]);

    $institution = LegacyInstitutionFactory::new()->create();
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $institution,
        'url_novo_educacao' => ' https://idiario.test/ ',
        'token_novo_educacao' => ' segredo ',
    ]);
    $transfer = LegacyTransferRequestFactory::new()->create(['data_transferencia' => '2030-05-10']);
    $registration = $transfer->oldRegistration;

    $service = Mockery::mock(NotificationService::class);
    $service->shouldReceive('createByUser')->once()->with(
        $transfer->ref_usuario_cad,
        Mockery::on(fn ($message) => str_contains($message, $registration->student->person->name)),
        '/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey(),
        NotificationType::TRANSFER
    );

    $listener = new TransferWebhookListener($service, $institution->refresh());
    $listener->handle(new TransferEvent($transfer));
    $listener->handle(new TransferEvent($transfer));

    Http::assertSent(fn (Request $request) => $request->url() === 'https://idiario.test/api/v2/ieducar_api_student_transfers'
        && $request->header('token') === ['segredo']
        && $request['student_enrollment_api_code'] === $registration->getKey()
        && $request['transfer_date'] === '2030-05-10'
        && str_contains($request['callback_url'], (string) $transfer->getKey()));

    expect($registration->refresh()->bloquear_troca_de_situacao)->toBeTrue();
});
