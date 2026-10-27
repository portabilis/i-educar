<?php

use App\Models\NotificationType;
use App\Services\NotificationService;
use Database\Factories\LegacyTransferRequestFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\ConfiguresInstitutionToken;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, ConfiguresInstitutionToken::class);

beforeEach(function () {
    $this->institutionWithToken();
    $this->scenario = EnrollmentScenario::for(LegacyUserFactory::new()->admin()->create(), 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Bia Borges')]);
    $this->transfer = LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $this->registration]);
    $this->post = fn (int $id, array $data = []) => $this->withHeaders(['token' => 'valid-token'])->postJson("/webhook/transfer/{$id}", $data);
});

test('avisa quem pediu a transferência que os lançamentos chegaram, com a mensagem recebida ou a padrão', function () {
    $this->mock(NotificationService::class, function ($mock) {
        $mock->shouldReceive('createByUser')->once()->with($this->transfer->ref_usuario_cad, 'Tudo certo.', '/intranet/educar_matricula_det.php?cod_matricula=' . $this->registration->getKey(), NotificationType::TRANSFER);
        $mock->shouldReceive('createByUser')->once()->with($this->transfer->ref_usuario_cad, '', Mockery::any(), NotificationType::TRANSFER);
    });

    ($this->post)($this->transfer->getKey(), ['message' => 'Tudo certo.'])
        ->assertOk()
        ->assertExactJson(['message' => 'Notification created successfully']);

    ($this->post)($this->transfer->getKey())->assertOk();
});

test('uma transferência desconhecida não é encontrada', function () {
    ($this->post)(999999999)->assertNotFound();
});
