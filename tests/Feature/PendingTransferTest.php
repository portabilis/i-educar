<?php

use App\Events\NotificationEvent;
use App\Events\TransferEvent;
use App\Listeners\TransferNotificationListener;
use App\Models\LegacyTransferRequest;
use App\Models\Notification;
use App\Process;
use App\Services\PendingTransferService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
    ]);
    $this->destination = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $this->registration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_escola' => LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]),
        'aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO,
    ]);
    $this->transfer = LegacyTransferRequestFactory::new()->create([
        'ref_cod_matricula_saida' => $this->registration,
        'ref_cod_matricula_entrada' => null,
        'ref_cod_escola_destino' => $this->destination,
    ]);
});

test('lista as informações da transferência interna ainda sem matrícula de entrada', function () {
    $this->getJson(route('transfers.pending.index'))->assertOk()
        ->assertJsonPath('total', 1)
        ->assertJsonPath('transfers.0.id', $this->transfer->getKey())
        ->assertJsonPath('latest_unconfirmed_id', $this->transfer->getKey())
        ->assertJsonPath('transfers.0.acknowledged', false)
        ->assertJsonPath('transfers.0.student', $this->registration->student->person->name)
        ->assertJsonPath('transfers.0.destination_school', $this->destination->name);
});

test('não lista transferências atendidas, excluídas ou externas', function () {
    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $this->registration]);
    LegacyTransferRequestFactory::new()->create([
        'ref_cod_matricula_saida' => $this->registration,
        'ref_cod_matricula_entrada' => null,
        'ref_cod_escola_destino' => null,
    ]);
    $this->transfer->update(['ativo' => 0]);

    $this->getJson(route('transfers.pending.index'))->assertOk()->assertJsonPath('total', 0);
});

test('usa a instituição padrão para o administrador sem instituição preenchida', function () {
    $this->user->ref_cod_instituicao = null;
    $this->user->save();

    $this->getJson(route('transfers.pending.index'))->assertOk()->assertJsonPath('total', 1);
});

test('restringe as pendências às escolas vinculadas ao usuário escolar', function () {
    $schoolUser = LegacyUserFactory::new()->school()->withSchool($this->destination)->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
    ]);
    $service = app(PendingTransferService::class);

    expect($service->forUser($schoolUser)->pluck('cod_transferencia_solicitacao')->all())
        ->toBe([$this->transfer->getKey()]);

    $this->transfer->update(['ref_cod_escola_destino' => $this->registration->ref_ref_cod_escola]);

    expect($service->forUser($schoolUser)->exists())->toBeFalse();
});

test('não permite confirmar transferência destinada a outra instituição', function () {
    $otherSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => LegacyInstitutionFactory::new()->create()]);
    $this->transfer->update(['ref_cod_escola_destino' => $otherSchool->getKey()]);

    $this->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertNotFound();

    expect($this->transfer->fresh()->data_recebimento)->toBeNull();
});

test('confirma a ciência e encaminha à matrícula sem efetivar o recebimento da transferência', function () {
    $response = $this->post(route('transfers.pending.confirm', $this->transfer->getKey()))
        ->assertRedirectContains('educar_matricula_cad.php?')
        ->assertRedirectContains('transferencia_id=' . $this->transfer->getKey());

    $transfer = $this->transfer->fresh();
    expect($transfer->ref_usuario_recebimento)->toBe($this->user->getKey())
        ->and($transfer->data_recebimento)->not->toBeNull()
        ->and($transfer->ref_cod_matricula_entrada)->toBeNull();

    $this->getJson(route('transfers.pending.index'))->assertJsonPath('total', 1)
        ->assertJsonPath('latest_unconfirmed_id', null)
        ->assertJsonPath('transfers.0.acknowledged', true);
    $this->get($response->headers->get('Location'))->assertOk();
});

test('notifica a escola de destino usando o evento de transferência existente', function () {
    Event::fake([NotificationEvent::class]);
    $receiver = LegacyUserFactory::new()->school()->withSchool($this->destination)->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
    ]);
    foreach ([Process::NOTIFY_TRANSFER, Process::REGISTRATIONS] as $process) {
        $menu = MenuFactory::new()->create(['process' => $process]);
        LegacyMenuUserTypeFactory::new()->create([
            'ref_cod_tipo_usuario' => $receiver->ref_cod_tipo_usuario,
            'menu_id' => $menu,
            'visualiza' => 1,
            'cadastra' => 1,
        ]);
    }
    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $this->registration->ref_ref_cod_escola,
        'ref_cod_serie' => $this->registration->ref_ref_cod_serie,
    ]);
    $schoolClass = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->registration->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $this->registration->ref_ref_cod_serie,
        'ref_cod_curso' => $this->registration->ref_cod_curso,
    ]);
    LegacyEnrollmentFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'ref_cod_turma' => $schoolClass,
    ]);

    $institutional = LegacyUserFactory::new()->institutional()->withSchool($this->destination)
        ->withAccess(Process::NOTIFY_TRANSFER)->withAccess(Process::REGISTRATIONS)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $bothSchools = LegacyUserFactory::new()->school()->withSchool($this->destination)
        ->withSchool($this->registration->school)
        ->withAccess(Process::NOTIFY_TRANSFER)->withAccess(Process::REGISTRATIONS)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $unlinked = LegacyUserFactory::new()->school()
        ->withAccess(Process::NOTIFY_TRANSFER)->withAccess(Process::REGISTRATIONS)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $this->user->update(['ref_cod_instituicao' => null]);
    app(TransferNotificationListener::class)->handle(new TransferEvent($this->transfer));

    $notification = Notification::where('user_id', $receiver->getKey())->first();
    expect($notification)->not->toBeNull()
        ->and($notification->link)->toBe('/intranet/educar_index.php')
        ->and($notification->text)->toContain('aguardando recebimento');
    Event::assertDispatched(NotificationEvent::class);
    foreach ([$this->user, $institutional, $bothSchools] as $originRecipient) {
        expect(Notification::where('user_id', $originRecipient->getKey())->count())->toBe(1)
            ->and(Notification::where('user_id', $originRecipient->getKey())
                ->where('link', '/intranet/educar_index.php')->exists())->toBeFalse();
    }
    expect(Notification::where('user_id', $receiver->getKey())->count())->toBe(1)
        ->and(Notification::where('user_id', $unlinked->getKey())->exists())->toBeFalse();
});

test('não substitui o primeiro usuário que confirmou a ciência', function () {
    $this->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertRedirect();
    $firstReceipt = $this->transfer->fresh()->data_recebimento;
    $other = LegacyUserFactory::new()->admin()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $this->actingAs($other)->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertRedirect();

    expect($this->transfer->fresh()->ref_usuario_recebimento)->toBe($this->user->getKey())
        ->and($this->transfer->fresh()->data_recebimento->equalTo($firstReceipt))->toBeTrue();
});

test('não permite confirmar uma transferência já atendida', function () {
    $this->transfer->update(['ref_cod_matricula_entrada' => LegacyRegistrationFactory::new()->create()->getKey()]);

    $this->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertNotFound();
});

test('exige permissão de matrícula para confirmar mesmo tendo acesso ao aviso', function () {
    $viewer = LegacyUserFactory::new()->school()->withSchool($this->destination)->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
    ]);
    foreach ([Process::NOTIFY_TRANSFER, Process::REGISTRATIONS] as $process) {
        $menu = MenuFactory::new()->create(['process' => $process]);
        LegacyMenuUserTypeFactory::new()->create([
            'ref_cod_tipo_usuario' => $viewer->ref_cod_tipo_usuario,
            'menu_id' => $menu,
            'visualiza' => 1,
            'cadastra' => 0,
        ]);
    }

    $this->actingAs($viewer)->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertForbidden();

    expect(LegacyTransferRequest::find($this->transfer->getKey())->data_recebimento)->toBeNull();
});

test('não inclui no alerta nem confirma transferências de outros anos letivos', function (int $offset) {
    $this->registration->update(['ano' => now()->year + $offset]);

    $this->getJson(route('transfers.pending.index'))->assertOk()
        ->assertJsonPath('total', 0)
        ->assertJsonPath('latest_unconfirmed_id', null);
    $this->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertNotFound();

    expect($this->transfer->fresh()->data_recebimento)->toBeNull();
})->with([-1, 1]);

test('identifica novas pendências mesmo quando estão fora da página exibida', function () {
    LegacyTransferRequestFactory::new()->count(20)->create([
        'ref_cod_matricula_saida' => $this->registration,
        'ref_cod_matricula_entrada' => null,
        'ref_cod_escola_destino' => $this->destination,
        'data_transferencia' => now()->addDay(),
    ]);
    $latestId = LegacyTransferRequest::query()->max('cod_transferencia_solicitacao');

    foreach ([1, 2] as $page) {
        $this->getJson(route('transfers.pending.index', ['page' => $page]))->assertOk()
            ->assertJsonPath('total', 21)
            ->assertJsonPath('latest_unconfirmed_id', $latestId);
    }
});

test('carrega o modal apenas no painel inicial e preserva o cadastro de matrícula', function () {
    $this->get('/intranet/educar_index.php')->assertOk()->assertSee('id="pending-transfers"', false);

    $response = $this->post(route('transfers.pending.confirm', $this->transfer->getKey()))->assertRedirect();
    $this->get($response->headers->get('Location'))->assertOk()
        ->assertDontSee('id="pending-transfers"', false)
        ->assertDontSee('TransferenciasPendentes.js', false);
});
