<?php

use Database\Factories\EmployeeWithdrawalFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('remove o afastamento do servidor marcando a exclusão', function () {
    $withdrawal = EmployeeWithdrawalFactory::new()->create(['ativo' => 1, 'data_exclusao' => null]);

    $this->deleteJson("/api/employee-withdrawal/{$withdrawal->getKey()}")
        ->assertOk()
        ->assertExactJson(['success' => true]);

    $withdrawal->refresh();
    expect($withdrawal->ativo)->toBe(0)
        ->and($withdrawal->data_exclusao)->not->toBeNull();
});

test('um afastamento desconhecido volta como erro', function () {
    $this->deleteJson('/api/employee-withdrawal/999999999')
        ->assertOk()
        ->assertExactJson(['error' => false, 'message' => 'Erro ao atualizar o afastamento do servidor']);
});
