<?php

use App\Events\NotificationEvent;
use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Models\Notification;
use App\Services\ComponentBatchManagerService;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\Feature\Api\V2\ConfiguresInstitutionToken;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, ConfiguresInstitutionToken::class);

beforeEach(function () {
    $this->institutionWithToken();
});

function createComponentBatchOperation(ComponentBatchStatus $status = ComponentBatchStatus::RUNNING): ComponentBatchOperation
{
    return ComponentBatchOperation::query()->create([
        'user_id' => LegacyUserFactory::new()->create()->getKey(),
        'status_id' => $status->value,
        'data' => ['preview_counts' => []],
    ]);
}

function postComponentBatchCallback($test, array $payload)
{
    return $test->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/component-batch-callback', $payload);
}

test('rejects request without token', function () {
    $operation = createComponentBatchOperation();

    $this->postJson('/api/v2/component-batch-callback', ['operation_id' => $operation->getKey(), 'success' => true])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    expect($operation->refresh()->status())->toBe(ComponentBatchStatus::RUNNING);
});

test('rejects request with invalid token', function () {
    $operation = createComponentBatchOperation();

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/component-batch-callback', ['operation_id' => $operation->getKey(), 'success' => true])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    expect($operation->refresh()->status())->toBe(ComponentBatchStatus::RUNNING);
});

test('requires the operation id', function () {
    postComponentBatchCallback($this, [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['operation_id' => 'Parâmetro operation_id é obrigatório.']);
});

test('rejects unknown operation', function () {
    postComponentBatchCallback($this, ['operation_id' => 999999999])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['operation_id' => 'Operação não encontrada.']);
});

test('does not process an operation that is not running', function () {
    $operation = createComponentBatchOperation(ComponentBatchStatus::COMPLETED);

    $this->mock(ComponentBatchManagerService::class)->shouldNotReceive('processCallback');

    postComponentBatchCallback($this, ['operation_id' => $operation->getKey(), 'success' => true])
        ->assertOk()
        ->assertExactJson(['message' => 'Operação não está em execução. Status atual: Concluído']);

    expect($operation->refresh()->status())->toBe(ComponentBatchStatus::COMPLETED);
});

test('processes the idiario result of a running operation', function () {
    $operation = createComponentBatchOperation();

    $this->mock(ComponentBatchManagerService::class)
        ->shouldReceive('processCallback')
        ->once()
        ->withArgs(fn (ComponentBatchOperation $received, array $result) => $received->is($operation)
            && $result === ['success' => true, 'deleted' => 12]);

    postComponentBatchCallback($this, ['operation_id' => $operation->getKey(), 'success' => 'true', 'deleted' => 12])
        ->assertOk()
        ->assertExactJson(['message' => 'Callback processado com sucesso.']);
});

test('forwards the idiario error', function () {
    $operation = createComponentBatchOperation();

    $this->mock(ComponentBatchManagerService::class)
        ->shouldReceive('processCallback')
        ->once()
        ->withArgs(fn (ComponentBatchOperation $received, array $result) => $result === [
            'success' => false,
            'deleted' => 0,
            'error' => 'Falha no i-Diário',
        ]);

    postComponentBatchCallback($this, ['operation_id' => $operation->getKey(), 'success' => false, 'error' => 'Falha no i-Diário'])
        ->assertOk()
        ->assertExactJson(['message' => 'Callback processado com sucesso.']);
});

test('marks the operation as failed when idiario reports failure', function () {
    Event::fake([NotificationEvent::class]);

    $operation = createComponentBatchOperation();

    postComponentBatchCallback($this, ['operation_id' => $operation->getKey(), 'success' => false, 'error' => 'Falha no i-Diário'])
        ->assertOk()
        ->assertExactJson(['message' => 'Callback processado com sucesso.']);

    $operation->refresh();

    expect($operation->status())->toBe(ComponentBatchStatus::FAILED)
        ->and($operation->error_message)->toBe('Não foi possível excluir os registros do i-Diário. Verifique se o serviço está disponível e tente novamente.')
        ->and($operation->data['idiario_error_detail'])->toEqual(['success' => false, 'deleted' => 0, 'error' => 'Falha no i-Diário']);

    $this->assertDatabaseHas(Notification::class, [
        'user_id' => $operation->user_id,
        'text' => 'Erro na remoção de componentes. Clique para ver detalhes.',
    ]);
});
